<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicineRequest;
use App\Models\MedicineBatch;
use App\Models\Medicine;
use App\Models\User;
use App\Models\MedicineRequestLog;
use Illuminate\Support\Facades\DB;

class ManageMedicineRequests extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Table filters ────────────────────────────────────────────
    public $search       = '';
    public $filterStatus = 'pending';
    public $perPage      = 10;

    // ─── View details ─────────────────────────────────────────────
    public $viewRequest;

    // ─── Walk-in form ─────────────────────────────────────────────
    public $walkInUserId     = '';
    public $walkInMedicineId = '';
    public $walkInQuantity   = 1;
    public $walkInReason     = '';
    public $userSearch       = '';
    public $users;
    public $medicines;

    protected $rules = [
        'walkInUserId'     => 'required',
        'walkInMedicineId' => 'required|exists:medicines,medicine_id',
        'walkInQuantity'   => 'required|integer|min:1',
        'walkInReason'     => 'nullable|string|max:500',
    ];

    protected $messages = [
        'walkInUserId.required'     => 'Please select a user/patient.',
        'walkInMedicineId.required' => 'Please select a medicine.',
        'walkInQuantity.required'   => 'Please enter a quantity.',
        'walkInQuantity.min'        => 'Quantity must be at least 1.',
    ];

    // ─── Mount ────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->loadUsers();
        $this->loadMedicines();
    }

    public function loadUsers(): void
    {
        $this->users = User::with(['patients' => fn($q) => $q->whereNotNull('guardian_user_id')])
            ->when($this->userSearch, fn($q) =>
                $q->where(fn($sub) =>
                    $sub->where('first_name', 'like', "%{$this->userSearch}%")
                        ->orWhere('last_name',  'like', "%{$this->userSearch}%")
                        ->orWhereRaw(
                            "CONCAT(first_name, ' ', IFNULL(middle_initial, ''), ' ', last_name) LIKE ?",
                            ["%{$this->userSearch}%"]
                        )
                )
            )
            ->whereIn('role', ['user', 'patient'])
            ->where('status', 'active')
            ->orderBy('first_name')
            ->limit(50)
            ->get();
    }

    public function loadMedicines(): void
    {
        // Show medicines that have at least some available (non-reserved) stock
        $this->medicines = Medicine::where('stock_status', '!=', 'Out of Stock')
            ->where('stock', '>', 0)
            ->where('expiry_status', '!=', 'Expired')
            ->where(fn($q) => $q->where('expiry_date', '>', now())->orWhereNull('expiry_date'))
            ->whereHas('batches')
            ->get();
    }

    public function updatedUserSearch(): void
    {
        $this->reset('walkInUserId');
        $this->loadUsers();
    }

    // ─── RESERVE (Approve step) ───────────────────────────────────
    //
    // Locks units into reserved_quantity on the batches in FIFO order.
    // Does NOT decrement quantity — the medicine is still physically on the shelf.
    // Returns a snapshot of which batches were reserved and how many.

    private function reserveFIFO(Medicine $medicine, int $qty): array
    {
        $batches = MedicineBatch::where('medicine_id', $medicine->medicine_id)
            ->where('expiry_date', '>', now())
            ->whereRaw('quantity - reserved_quantity > 0')  // only batches with free units
            ->orderBy('expiry_date', 'asc')
            ->lockForUpdate()
            ->get();

        $remaining   = $qty;
        $batchesUsed = [];

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $available = $batch->quantity - $batch->reserved_quantity;
            $take      = min($available, $remaining);

            $batch->increment('reserved_quantity', $take);

            $batchesUsed[] = [
                'batch_id'     => $batch->id,
                'batch_number' => $batch->batch_number,
                'expiry_date'  => $batch->expiry_date->format('Y-m-d'),
                'qty_taken'    => $take,
            ];

            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new \Exception("Insufficient available stock to reserve this request. ({$remaining} units could not be reserved)");
        }

        return ['batches_used' => $batchesUsed];
    }

    // ─── DEDUCT (Dispense step) ───────────────────────────────────
    //
    // Walks the batches_snapshot saved at approval time and performs the
    // actual quantity decrement + reserved_quantity decrement.
    // This is the only place physical stock leaves the shelf.

    private function deductReserved(MedicineRequest $request): void
    {
        $snapshot = $request->batches_snapshot ?? [];

        foreach ($snapshot as $snap) {
            $batch = MedicineBatch::lockForUpdate()->find($snap['batch_id']);

            if (!$batch) {
                // Batch was archived after approval — skip silently (stock was
                // already removed when the batch was archived).
                continue;
            }

            $qty = (int) $snap['qty_taken'];

            // Decrement physical stock
            $batch->decrement('quantity', $qty);
            // Release the reservation lock
            $batch->decrement('reserved_quantity', min($qty, $batch->reserved_quantity));
        }
    }

    // ─── RELEASE RESERVATION (Cancel step) ───────────────────────
    //
    // Walks the snapshot and releases reserved_quantity only.
    // Physical quantity is untouched — nothing ever left the shelf.

    private function releaseReserved(MedicineRequest $request): void
    {
        $snapshot = $request->batches_snapshot ?? [];

        foreach ($snapshot as $snap) {
            $batch = MedicineBatch::lockForUpdate()->find($snap['batch_id']);

            if (!$batch) continue;

            $qty = (int) $snap['qty_taken'];
            $batch->decrement('reserved_quantity', min($qty, $batch->reserved_quantity));
        }
    }

    // ─── Approve ─────────────────────────────────────────────────

    public function approve($requestId): void
    {
        DB::transaction(function () use ($requestId) {
            $request = MedicineRequest::with([
                    'medicine' => fn($q) => $q->withTrashed()->with('batches'),
                ])
                ->lockForUpdate()
                ->findOrFail($requestId);

            if ($request->status !== 'pending') {
                session()->flash('error', 'This request has already been processed.');
                return;
            }

            if ($request->medicine?->trashed()) {
                session()->flash('error', 'Cannot approve — medicine is archived.');
                return;
            }

            $medicine  = $request->medicine;
            $qtyNeeded = $request->quantity_requested;

            // Check available (non-reserved) batch stock
            $availableBatchStock = MedicineBatch::where('medicine_id', $medicine->medicine_id)
                ->where('expiry_date', '>', now())
                ->whereRaw('quantity - reserved_quantity > 0')
                ->sum(DB::raw('quantity - reserved_quantity'));

            if ($availableBatchStock < $qtyNeeded) {
                session()->flash('error', "Insufficient available stock. Only {$availableBatchStock} unreserved units available.");
                return;
            }

            // Reserve — no physical deduction yet
            $snapshot = $this->reserveFIFO($medicine, $qtyNeeded);

            $request->update([
                'status'            => 'ready_to_pickup',
                'approved_by_id'    => auth()->id(),
                'approved_by_type'  => get_class(auth()->user()),
                'approved_at'       => now(),
                'ready_at'          => now(),
                'reserved_at'       => now(),
                'reserved_quantity' => $qtyNeeded,
                'batches_snapshot'  => $snapshot['batches_used'],
            ]);

            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $request->requester_name,
                'medicine_name'       => $medicine->medicine_name,
                'dosage'              => $medicine->dosage,
                'quantity'            => $qtyNeeded,
                'action'              => 'approved_reserved',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username ?? auth()->user()->full_name,
                'performed_at'        => now(),
            ]);
        });

        $this->dispatch('approve-modal');
        // session()->flash('message', 'Request approved and stock reserved. Patient notified to pick up.');
        $this->dispatch('swal-success', message: 'Request approved and stock reserved. Patient notified to pick up.');
    }

    // ─── Dispense ────────────────────────────────────────────────

    public function dispense($requestId): void
    {
        DB::transaction(function () use ($requestId) {
            $request = MedicineRequest::with([
                    'medicine' => fn($q) => $q->withTrashed(),
                ])
                ->lockForUpdate()
                ->findOrFail($requestId);

            if ($request->status !== 'ready_to_pickup') {
                session()->flash('error', 'Request must be "Ready to Pick Up" before dispensing.');
                return;
            }

            if ($request->medicine?->trashed()) {
                session()->flash('error', 'Cannot dispense — medicine has been archived. Please cancel this request instead.');
                return;
            }

            // ── Actual physical deduction happens HERE, not at approval ──
            $this->deductReserved($request);

            // Sync medicine.stock = sum of all valid batch quantities
            $medicine  = $request->medicine;
            $newStock  = MedicineBatch::where('medicine_id', $medicine->medicine_id)
                ->where('expiry_date', '>', now())
                ->sum('quantity');

            $medicine->update([
                'stock'        => $newStock,
                'stock_status' => $this->determineStockStatus($newStock),
            ]);

            $request->update([
                'status'          => 'completed',
                'dispensed_at'    => now(),
                'dispensed_by_id' => auth()->id(),
            ]);

            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $request->requester_name,
                'medicine_name'       => $medicine->medicine_name,
                'dosage'              => $medicine->dosage,
                'quantity'            => $request->reserved_quantity ?? $request->quantity_requested,
                'batches_used'        => $request->batches_snapshot ?? [],
                'action'              => 'dispensed',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username ?? auth()->user()->full_name,
                'performed_at'        => now(),
            ]);
        });

        // session()->flash('message', 'Medicine dispensed successfully.');
        $this->dispatch('swal-success', message: 'Medicine dispensed successfully.');
    }

    // ─── Cancel ready request ─────────────────────────────────────

    public function cancelReadyRequest($requestId): void
    {
        DB::transaction(function () use ($requestId) {
            $request = MedicineRequest::with([
                    'medicine' => fn($q) => $q->withTrashed(),
                ])
                ->lockForUpdate()
                ->findOrFail($requestId);

            if ($request->status !== 'ready_to_pickup') {
                session()->flash('error', 'Only "Ready to Pick Up" requests can be cancelled here.');
                return;
            }

            // Release the reservation — physical stock was never decremented
            $this->releaseReserved($request);

            // medicine.stock does NOT need adjusting — quantity was never touched
            // We only need to reflect that reserved units are now free again.
            // However, if you display available_stock in the UI you may want to
            // dispatch a refresh event here.

            $request->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $medicine = $request->medicine;

            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $request->requester_name,
                'medicine_name'       => $medicine->medicine_name ?? 'Unknown',
                'dosage'              => $medicine->dosage ?? 'N/A',
                'quantity'            => $request->reserved_quantity ?? $request->quantity_requested,
                'action'              => 'cancelled',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username ?? auth()->user()->full_name,
                'performed_at'        => now(),
            ]);
        });

        // session()->flash('message', 'Request cancelled and reservation released.');
        $this->dispatch('swal-warning', message: 'Request cancelled and reservation released.');
    }

    // ─── Reject ───────────────────────────────────────────────────

    public function reject($requestId): void
    {
        DB::transaction(function () use ($requestId) {
            $request = MedicineRequest::with(['medicine' => fn($q) => $q->withTrashed()])
                ->lockForUpdate()
                ->findOrFail($requestId);

            if ($request->status !== 'pending') {
                session()->flash('error', 'This request has already been processed.');
                return;
            }

            $request->update(['status' => 'rejected']);

            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $request->requester_name,
                'medicine_name'       => $request->medicine->medicine_name ?? 'Unknown',
                'dosage'              => $request->medicine->dosage ?? 'N/A',
                'quantity'            => $request->quantity_requested,
                'action'              => 'rejected',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username ?? auth()->user()->full_name,
                'performed_at'        => now(),
            ]);
        });

        // session()->flash('message', 'Medicine request rejected.');
        $this->dispatch('swal-error', message: 'Medicine request rejected.');
    }

    // ─── Walk-in ──────────────────────────────────────────────────
    // Walk-ins are approved + dispensed in a single step, so we use
    // deductFIFO directly (reserve then immediately deduct).

    private function deductFIFO(Medicine $medicine, int $qty): array
    {
        $batches = MedicineBatch::where('medicine_id', $medicine->medicine_id)
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>', now())
            ->orderBy('expiry_date', 'asc')
            ->lockForUpdate()
            ->get();

        $remaining   = $qty;
        $batchesUsed = [];

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $take = min($batch->quantity - $batch->reserved_quantity, $remaining);
            if ($take <= 0) continue;

            $batch->decrement('quantity', $take);

            $batchesUsed[] = [
                'batch_id'     => $batch->id,
                'batch_number' => $batch->batch_number,
                'expiry_date'  => $batch->expiry_date->format('Y-m-d'),
                'qty_taken'    => $take,
            ];

            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new \Exception("Insufficient non-reserved, non-expired stock to fulfill this walk-in.");
        }

        $newStock = MedicineBatch::where('medicine_id', $medicine->medicine_id)
            ->where('expiry_date', '>', now())
            ->sum('quantity');

        $medicine->update([
            'stock'        => $newStock,
            'stock_status' => $this->determineStockStatus($newStock),
        ]);

        return ['batches_used' => $batchesUsed];
    }

    public function createWalkIn(): void
    {
        $this->validate();

        $isChild = str_starts_with((string) $this->walkInUserId, 'child:');

        if (!$isChild && !User::find($this->walkInUserId)) {
            $this->addError('walkInUserId', 'Selected user/patient is invalid.');
            return;
        }

        if ($isChild) {
            $childId = (int) str_replace('child:', '', $this->walkInUserId);
            if (!\App\Models\patients::find($childId)) {
                $this->addError('walkInUserId', 'Selected child patient record not found.');
                return;
            }
        }

        if (!$this->validateWalkInStock()) return;

        DB::transaction(function () {
            $medicine  = Medicine::with('batches')->lockForUpdate()->findOrFail($this->walkInMedicineId);
            $qtyNeeded = (int) $this->walkInQuantity;

            // Check unreserved available stock
            $availableStock = MedicineBatch::where('medicine_id', $medicine->medicine_id)
                ->where('expiry_date', '>', now())
                ->sum(DB::raw('quantity - reserved_quantity'));

            if ($availableStock < $qtyNeeded) {
                throw new \Exception("Insufficient available stock. Only {$availableStock} unreserved units available.");
            }

            $isChild     = str_starts_with((string) $this->walkInUserId, 'child:');
            $patientName = null;

            if ($isChild) {
                $childId      = (int) str_replace('child:', '', $this->walkInUserId);
                $childPatient = \App\Models\patients::findOrFail($childId);
                $patientName  = $childPatient->full_name;

                $requestData = [
                    'medicine_id'        => $this->walkInMedicineId,
                    'quantity_requested' => $qtyNeeded,
                    'reason'             => $this->walkInReason,
                    'status'             => 'completed',
                    'approved_by_id'     => auth()->id(),
                    'approved_by_type'   => get_class(auth()->user()),
                    'approved_at'        => now(),
                    'dispensed_at'       => now(),
                    'dispensed_by_id'    => auth()->id(),
                    'patients_id'        => $childPatient->id,
                ];
            } else {
                $user        = User::findOrFail($this->walkInUserId);
                $patient     = $user->patient;
                $patientName = $user->full_name;

                $requestData = [
                    'medicine_id'        => $this->walkInMedicineId,
                    'quantity_requested' => $qtyNeeded,
                    'reason'             => $this->walkInReason,
                    'status'             => 'completed',
                    'approved_by_id'     => auth()->id(),
                    'approved_by_type'   => get_class(auth()->user()),
                    'approved_at'        => now(),
                    'dispensed_at'       => now(),
                    'dispensed_by_id'    => auth()->id(),
                ];

                $requestData[$patient ? 'patients_id' : 'user_id'] = $patient ? $patient->id : $user->id;
            }

            $fifo    = $this->deductFIFO($medicine, $qtyNeeded);
            $request = MedicineRequest::create($requestData);

            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $patientName,
                'medicine_name'       => $medicine->medicine_name,
                'dosage'              => $medicine->dosage,
                'quantity'            => $qtyNeeded,
                'batches_used'        => $fifo['batches_used'],
                'action'              => 'dispensed',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username ?? auth()->user()->full_name,
                'performed_at'        => now(),
            ]);
        });

        $this->resetWalkInForm();
        $this->dispatch('close-walkin-modal');
        // session()->flash('message', 'Walk-in medicine dispensed successfully.');
    }

    protected function validateWalkInStock(): bool
    {
        if ($this->walkInMedicineId && $this->walkInQuantity) {
            $medicine = Medicine::find($this->walkInMedicineId);
            if (!$medicine) {
                $this->addError('walkInMedicineId', 'Selected medicine not found.');
                return false;
            }

            $available = MedicineBatch::where('medicine_id', $medicine->medicine_id)
                ->where('expiry_date', '>', now())
                ->sum(DB::raw('quantity - reserved_quantity'));

            if ($this->walkInQuantity > $available) {
                $this->addError('walkInQuantity', "Quantity exceeds available stock ({$available} unreserved units available).");
                return false;
            }
        }
        return true;
    }

    public function resetWalkInForm(): void
    {
        $this->reset(['walkInUserId', 'walkInMedicineId', 'walkInQuantity', 'walkInReason', 'userSearch']);
        $this->resetErrorBag();
        $this->loadUsers();
        $this->loadMedicines();
    }

    // ─── View details ─────────────────────────────────────────────

    public function viewDetails($requestId): void
    {
        $this->viewRequest = MedicineRequest::with([
            'medicine' => fn($q) => $q->withTrashed(),
            'patients',
            'user',
        ])->findOrFail($requestId);
    }

    // ─── Stats ────────────────────────────────────────────────────

    public function getPendingCount(): int
    {
        return MedicineRequest::where('status', 'pending')->count();
    }

    public function getApprovedCount(): int
    {
        return MedicineRequest::where('status', 'approved')->count();
    }

    public function getReadyCount(): int
    {
        return MedicineRequest::where('status', 'ready_to_pickup')->count();
    }

    public function getCompletedCount(): int
    {
        return MedicineRequest::where('status', 'completed')->count();
    }

    public function getRejectedCount(): int
    {
        return MedicineRequest::where('status', 'rejected')->count();
    }

    public function getTotalCount(): int
    {
        return MedicineRequest::count();
    }

    // ─── Helpers ─────────────────────────────────────────────────

    private function determineStockStatus($stock): string
    {
        if ($stock <= 0)  return 'Out of Stock';
        if ($stock <= 10) return 'Low Stock';
        return 'In Stock';
    }

    // ─── Render ───────────────────────────────────────────────────

    public function render()
    {
        $requests = MedicineRequest::query()
            ->with([
                'patients:id,first_name,middle_initial,last_name,suffix',
                'user:id,first_name,middle_initial,last_name',
                'medicine' => fn($q) => $q->withTrashed(),
            ])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('patients', fn($p) =>
                            $p->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name',  'like', "%{$this->search}%")
                            ->orWhereRaw(
                                "CONCAT(first_name, ' ', IFNULL(middle_initial, ''), ' ', last_name, ' ', IFNULL(suffix, '')) LIKE ?",
                                ["%{$this->search}%"]
                            )
                        )
                        ->orWhereHas('user', fn($u) =>
                            $u->whereRaw(
                                "CONCAT(first_name, ' ', IFNULL(middle_initial, ''), ' ', last_name) LIKE ?",
                                ["%{$this->search}%"]
                            )
                        )
                        ->orWhereHas('medicine', fn($m) =>
                            $m->withTrashed()->where('medicine_name', 'like', "%{$this->search}%")
                        );
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest('created_at')
            ->paginate($this->perPage);

        return view('livewire.manage-medicine-requests', compact('requests'))
            ->layout('livewire.layouts.base', ['page' => 'MANAGE MEDICINE REQUEST']);
    }
}