<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use \Illuminate\Validation\Rule;

class MedicineBatches extends Component
{
    public Medicine $medicine;

    // ─── Add batch fields ─────────────────────────────────────────
    public $newBatchNumber       = '';
    public $newBatchQty          = '';
    public $newBatchExpiry       = '';
    public $newBatchManufactured = '';

    // ─── Edit batch fields ────────────────────────────────────────
    public $editBatchId           = null;
    public $editBatchNumber       = '';
    public $editBatchQty          = '';
    public $editBatchExpiry       = '';
    public $editBatchManufactured = '';

    // ─── Archive state ────────────────────────────────────────────
    public $archiveBatchId    = null;
    public $showArchived      = false;
    public $batchErrorMessage = '';

    public string $backUrl = '';

    public function updated($propertyName): void
    {
        $this->resetValidation($propertyName);
    }

    // ─── Mount ────────────────────────────────────────────────────

    public function mount(Medicine $medicine): void
    {
        $this->medicine = $medicine;
        $this->backUrl  = request('back', route('medicines'));
    }

    // ─── Expiry / stock helpers ───────────────────────────────────

    private function determineExpiryStatus($expiry_date): string
    {
        $expiry = \Carbon\Carbon::parse($expiry_date, 'Asia/Manila')->startOfDay();
        $today  = now('Asia/Manila')->startOfDay();

        if ($expiry->lte($today)) return 'Expired';
        if ($expiry->lte($today->copy()->addDays(30)->startOfDay())) return 'Expiring Soon';
        return 'Valid';
    }

    private function determineStockStatus($stock): string
    {
        if ($stock <= 0)  return 'Out of Stock';
        if ($stock <= 10) return 'Low Stock';
        return 'In Stock';
    }

    // ─────────────────────────────────────────────────────────────
    // Only syncs medicine stock + expiry columns.
    // Does NOT trigger any notifications — kept pure for use inside
    // transactions where no side effects should happen.
    // ─────────────────────────────────────────────────────────────
    private function syncMedicineExpiryFromBatches(): void
    {
        $allActiveBatches = MedicineBatch::where('medicine_id', $this->medicine->medicine_id)->get();

        if ($allActiveBatches->isEmpty()) {
            $this->medicine->update([
                'expiry_date'   => null,
                'expiry_status' => 'N/A',
                'stock'         => 0,
                'stock_status'  => 'Out of Stock',
            ]);
            return;
        }

        $latestBatch  = $allActiveBatches->sortByDesc('expiry_date')->first();
        $latestExpiry = $latestBatch->expiry_date;
        $status       = $this->determineExpiryStatus($latestExpiry);

        $validStock = MedicineBatch::where('medicine_id', $this->medicine->medicine_id)
            ->where('expiry_date', '>', now())
            ->where('quantity', '>', 0)
            ->sum('quantity');

        $this->medicine->update([
            'expiry_date'   => $latestExpiry,
            'expiry_status' => $status,
            'stock'         => $validStock,
            'stock_status'  => $this->determineStockStatus($validStock),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Builds the list of alerts to fire based on committed DB state.
    // Called OUTSIDE the transaction so queries see committed data.
    // ─────────────────────────────────────────────────────────────
    private function collectAlerts(): array
    {
        $medicine      = $this->medicine->fresh();
        $pendingAlerts = [];

        // ── Stock alerts ───────────────────────────────────────────
        if ($medicine->stock <= 0) {
            $pendingAlerts[] = ['type' => 'stock', 'alertType' => 'out_of_stock', 'batch' => null, 'medicine' => $medicine];
        } elseif ($medicine->stock <= 10) {
            $pendingAlerts[] = ['type' => 'stock', 'alertType' => 'low_stock', 'batch' => null, 'medicine' => $medicine];
        }

        // ── Expiry alerts — scan all committed active batches ──────
        $activeBatches = MedicineBatch::where('medicine_id', $medicine->medicine_id)
            ->whereNull('deleted_at')
            ->get();

        foreach ($activeBatches as $batch) {
            $expiryStatus = $this->determineExpiryStatus($batch->expiry_date);

            if ($expiryStatus === 'Expiring Soon') {
                $pendingAlerts[] = ['type' => 'expiry', 'alertType' => 'expiring_soon', 'batch' => $batch, 'medicine' => $medicine];
            } elseif ($expiryStatus === 'Expired') {
                $pendingAlerts[] = ['type' => 'expiry', 'alertType' => 'expired', 'batch' => $batch, 'medicine' => $medicine];
            }
        }

        return $pendingAlerts;
    }

    // ─────────────────────────────────────────────────────────────
    // Dispatches all collected alerts after transaction commits.
    // ─────────────────────────────────────────────────────────────
    private function dispatchAlerts(array $pendingAlerts): void
    {
        foreach ($pendingAlerts as $alert) {
            if ($alert['type'] === 'stock') {
                $this->sendStockNotification($alert['alertType'], $alert['medicine']);
            } else {
                $this->sendExpiryNotification($alert['alertType'], $alert['batch'], $alert['medicine']);
            }
        }
    }

    // ─── Real-time stock bell + email ─────────────────────────────

    private function sendStockNotification(string $alertType, Medicine $medicine): void
    {
        $message = match ($alertType) {
            'out_of_stock' => "{$medicine->medicine_name} ({$medicine->dosage}) is OUT OF STOCK. Current stock: {$medicine->stock}.",
            'low_stock'    => "{$medicine->medicine_name} ({$medicine->dosage}) is running LOW. Current stock: {$medicine->stock}.",
            default        => "Stock alert for {$medicine->medicine_name}.",
        };

        $title = match ($alertType) {
            'out_of_stock' => '🔴 Out of Stock Alert',
            'low_stock'    => '🟡 Low Stock Alert',
            default        => '⚠️ Inventory Alert',
        };

        $recipients = User::whereIn('role', ['nurse', 'staff'])->get();

        foreach ($recipients as $user) {

            // ── Bell notification (always fire) ───────────────────────
            DB::table('notifications')->insert([
                'user_id'          => $user->id,
                'type'             => $alertType,
                'title'            => $title,
                'message'          => $message,
                'appointment_type' => 'inventory',
                'link_url'         => '/medicines?filter=' . $alertType,
                'is_read'          => 0,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // ── Email notification (always fire) ──────────────────────
            try {
                Mail::to($user->email)->queue(new \App\Mail\InventoryAlertMail(
                    $alertType,
                    $medicine,
                    null,
                    null,
                    $user
                ));

                DB::table('inventory_email_logs')->insert([
                    'user_id'    => $user->id,
                    'alert_type' => $alertType,
                    'reference'  => $message,
                    'sent_at'    => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error("Stock alert mail queue failed for {$user->email}: " . $e->getMessage());
            }
        }
    }

    // ─── Real-time expiry bell + email ────────────────────────────

    private function sendExpiryNotification(string $alertType, MedicineBatch $batch, Medicine $medicine): void
    {
        $expiryDate = \Carbon\Carbon::parse($batch->expiry_date)->setTimezone('Asia/Manila');

        $message = match ($alertType) {
            'expired'       => "{$medicine->medicine_name} ({$medicine->dosage}) batch {$batch->batch_number} has EXPIRED as of {$expiryDate->format('M d, Y')}.",
            'expiring_soon' => "{$medicine->medicine_name} ({$medicine->dosage}) batch {$batch->batch_number} is expiring on {$expiryDate->format('M d, Y')}.",
            default         => "Expiry alert for {$medicine->medicine_name}.",
        };

        $title = match ($alertType) {
            'expired'       => '⛔ Expired Medicine Alert',
            'expiring_soon' => '🟠 Expiring Soon Alert',
            default         => '⚠️ Inventory Alert',
        };

        $recipients = User::whereIn('role', ['nurse', 'staff'])->get();

        foreach ($recipients as $user) {

            // ── Bell notification ──────────────────────────────────
            DB::table('notifications')->insert([
                'user_id'          => $user->id,
                'type'             => $alertType,
                'title'            => $title,
                'message'          => $message,
                'appointment_type' => 'inventory',
                'link_url'         => '/medicines?filter=' . $alertType,
                'is_read'          => 0,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // ── Email notification ─────────────────────────────────
            try {
                Mail::to($user->email)->queue(new \App\Mail\InventoryAlertMail(
                    $alertType,
                    $medicine,
                    $batch->batch_number,
                    $expiryDate->format('M d, Y'),
                    $user
                ));

                DB::table('inventory_email_logs')->insert([
                    'user_id'    => $user->id,
                    'alert_type' => $alertType,
                    'reference'  => $message,
                    'sent_at'    => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error("Expiry alert mail queue failed for {$user->email}: " . $e->getMessage());
            }
        }
    }
    // ─── Add batch ────────────────────────────────────────────────

    public function addBatch(): void
    {
        $this->validate([
            'newBatchQty'          => 'required|integer|min:1',
            'newBatchExpiry'       => 'required|date|after:today',
            'newBatchNumber'       => [
                'required',
                'string',
                'max:100',
                Rule::unique('medicine_batches', 'batch_number')
                    ->where('medicine_id', $this->medicine->medicine_id)
                    ->whereNull('deleted_at'),
            ],
            'newBatchManufactured' => 'nullable|date|before_or_equal:today',
        ], [
            'newBatchQty.required'     => 'Quantity is required.',
            'newBatchExpiry.required'  => 'Expiry date is required.',
            'newBatchExpiry.after'     => 'Expiry date must be in the future.',
            'newBatchNumber.required'  => 'Batch number is required.',
            'newBatchNumber.unique'    => 'This batch number already exists.',
            'newBatchManufactured.before_or_equal' => 'Manufactured date cannot be in the future.',
        ]);

        $expiryStatus = $this->determineExpiryStatus($this->newBatchExpiry);

        // ── Step 1: save to DB ─────────────────────────────────────
        DB::transaction(function () use ($expiryStatus) {
            MedicineBatch::create([
                'medicine_id'       => $this->medicine->medicine_id,
                'batch_number'      => $this->newBatchNumber,
                'quantity'          => (int) $this->newBatchQty,
                'initial_quantity'  => (int) $this->newBatchQty,
                'reserved_quantity' => 0,
                'manufactured_date' => $this->newBatchManufactured ?: null,
                'expiry_date'       => $this->newBatchExpiry,
                'expiry_status'     => $expiryStatus,
            ]);

            $this->syncMedicineExpiryFromBatches();
            $this->medicine->refresh();
        });

        // ── Step 2: collect alerts from committed DB state ─────────
        $pendingAlerts = $this->collectAlerts();

        // ── Step 3: dispatch notifications ────────────────────────
        $this->dispatchAlerts($pendingAlerts);

        $this->reset(['newBatchNumber', 'newBatchQty', 'newBatchExpiry', 'newBatchManufactured']);
        session()->flash('batch_success', 'Batch added successfully.');
    }

    // ─── Edit batch ───────────────────────────────────────────────

    public function editBatch($batchId): void
    {
        $batch = MedicineBatch::findOrFail($batchId);

        if ($batch->expiry_date->startOfDay()->lte(now('Asia/Manila')->startOfDay())) {
            $this->dispatch('notify-error', message: 'Expired batches cannot be edited.');
            return;
        }

        $this->editBatchId           = $batchId;
        $this->editBatchNumber       = $batch->batch_number;
        $this->editBatchQty          = $batch->quantity;
        $this->editBatchExpiry       = $batch->expiry_date->format('Y-m-d');
        $this->editBatchManufactured = $batch->manufactured_date
            ? $batch->manufactured_date->format('Y-m-d')
            : '';

        $this->dispatch('show-edit-batch-modal');
    }

    public function updateBatch(): void
    {
        $batch = MedicineBatch::findOrFail($this->editBatchId);

        if ($batch->expiry_date->startOfDay()->lte(now('Asia/Manila')->startOfDay())) {
            $this->dispatch('notify-error', message: 'Expired batches cannot be edited.');
            $this->resetEditFields();
            $this->dispatch('close-edit-batch-modal');
            return;
        }

        $this->validate([
            'editBatchNumber'       => [
                'required',
                'string',
                'max:100',
                Rule::unique('medicine_batches', 'batch_number')
                    ->where('medicine_id', $this->medicine->medicine_id)
                    ->ignore($this->editBatchId),
            ],
            'editBatchQty'          => ['required', 'integer', 'min:1'],
            'editBatchExpiry'       => 'required|date',
            'editBatchManufactured' => 'nullable|date|before_or_equal:today',
        ], [
            'editBatchQty.required'    => 'Quantity is required.',
            'editBatchExpiry.required' => 'Expiry date is required.',
            'editBatchNumber.required' => 'Batch number is required.',
            'editBatchNumber.unique'   => 'This batch number already exists.',
            'editBatchExpiry.after'    => 'Expiry date must be in the future.',
            'editBatchManufactured.before_or_equal' => 'Manufactured date cannot be in the future.',
        ]);

        $newQty = (int) $this->editBatchQty;

        if ($newQty < $batch->reserved_quantity) {
            $this->addError(
                'editBatchQty',
                "Cannot set quantity below the reserved amount ({$batch->reserved_quantity} units are currently reserved for pending dispensing)."
            );
            return;
        }

        $expiryStatus = $this->determineExpiryStatus($this->editBatchExpiry);

        // ── Step 1: save to DB ─────────────────────────────────────
        DB::transaction(function () use ($batch, $expiryStatus, $newQty) {
            $batch->update([
                'batch_number'      => $this->editBatchNumber,
                'quantity'          => $newQty,
                'manufactured_date' => $this->editBatchManufactured ?: null,
                'expiry_date'       => $this->editBatchExpiry,
                'expiry_status'     => $expiryStatus,
            ]);

            $this->syncMedicineExpiryFromBatches();
            $this->medicine->refresh();
        });

        // ── Step 2: collect alerts from committed DB state ─────────
        $pendingAlerts = $this->collectAlerts();

        // ── Step 3: dispatch notifications ────────────────────────
        $this->dispatchAlerts($pendingAlerts);

        $this->resetEditFields();
        $this->dispatch('close-edit-batch-modal');
        session()->flash('batch_success', 'Batch updated successfully.');
    }

    public function cancelEdit(): void
    {
        $this->resetEditFields();
        $this->dispatch('close-edit-batch-modal');
    }

    private function resetEditFields(): void
    {
        $this->editBatchId           = null;
        $this->editBatchNumber       = '';
        $this->editBatchQty          = '';
        $this->editBatchExpiry       = '';
        $this->editBatchManufactured = '';
        $this->resetErrorBag();
    }

    // ─── Archive / restore batch ──────────────────────────────────

    public function confirmArchiveBatch($batchId): void
    {
        $this->archiveBatchId = $batchId;
        $this->dispatch('show-archive-batch-confirmation');
    }

    public function archiveBatch(): void
    {
        $batch = MedicineBatch::findOrFail($this->archiveBatchId);

        if ($batch->reserved_quantity > 0) {
            session()->flash('batch_error', "Warning: this batch has {$batch->reserved_quantity} units reserved for approved requests. Those requests may fail to dispense. Cancel or dispense them before archiving.");
            $this->archiveBatchId = null;
            return;
        }

        // ── Step 1: save to DB ─────────────────────────────────────
        DB::transaction(function () use ($batch) {
            $batch->delete();
            $this->medicine->refresh();
            $this->syncMedicineExpiryFromBatches();
        });

        // ── Step 2: collect alerts from committed DB state ─────────
        $pendingAlerts = $this->collectAlerts();

        // ── Step 3: dispatch notifications ────────────────────────
        $this->dispatchAlerts($pendingAlerts);
        $this->medicine->refresh();

        $this->archiveBatchId = null;
        session()->flash('batch_success', 'Batch archived successfully.');
    }

    public function restoreBatch($batchId): void
    {
        $batch = MedicineBatch::withTrashed()->findOrFail($batchId);

        $conflict = MedicineBatch::where('medicine_id', $this->medicine->medicine_id)
            ->where('batch_number', $batch->batch_number)
            ->whereNull('deleted_at')
            ->exists();

        if ($conflict) {
            session()->flash('batch_error', "Cannot restore: an active batch with number \"{$batch->batch_number}\" already exists.");
            return;
        }

        // ── Step 1: save to DB ─────────────────────────────────────
        DB::transaction(function () use ($batch) {
            $batch->restore();
            $this->medicine->refresh();
            $this->syncMedicineExpiryFromBatches();
        });

        // ── Step 2: collect alerts from committed DB state ─────────
        $pendingAlerts = $this->collectAlerts();

        // ── Step 3: dispatch notifications ────────────────────────
        $this->dispatchAlerts($pendingAlerts);
        $this->medicine->refresh();

        session()->flash('batch_success', 'Batch restored successfully.');
    }

    public function toggleArchived(): void
    {
        $this->showArchived = !$this->showArchived;
    }

    // ─── Render ───────────────────────────────────────────────────

    public function render()
    {
        $batchQuery = MedicineBatch::where('medicine_id', $this->medicine->medicine_id)
            ->orderBy('expiry_date', 'asc');

        if ($this->showArchived) {
            $batchQuery->onlyTrashed();
        }

        $batches = $batchQuery->get();

        return view('livewire.medicine-batches', [
            'batches' => $batches,
        ])->layout('livewire.layouts.base', ['page' => 'BATCH MANAGEMENT']);
    }
}
