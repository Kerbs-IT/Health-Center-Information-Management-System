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
        $this->medicines = Medicine::where('stock_status', '!=', 'Out of Stock')
            ->where('stock', '>', 0)
            ->where('expiry_status', '!=', 'Expired')
            ->where(fn($q) => $q->where('expiry_date', '>', now())->orWhereNull('expiry_date'))
            ->get();
    }

    public function updatedUserSearch(): void
    {
        $this->reset('walkInUserId');
        $this->loadUsers();
    }

    // ─── FIFO deduction ───────────────────────────────────────────

    private function deductFIFO(Medicine $medicine, int $qty): array
    {
        $batches     = $medicine->batches; // sorted by expiry_date ASC, qty > 0
        $remaining   = $qty;
        $batchesUsed = [];

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $take = min($batch->quantity, $remaining);
            $batch->decrement('quantity', $take);

            $batchesUsed[] = [
                'batch_id'     => $batch->id,
                'batch_number' => $batch->batch_number,
                'expiry_date'  => $batch->expiry_date->format('Y-m-d'),
                'qty_taken'    => $take,
            ];

            $remaining -= $take;
        }

        $newStock = $medicine->stock - $qty;
        $medicine->update([
            'stock'        => $newStock,
            'stock_status' => $this->determineStockStatus($newStock),
        ]);

        return ['batches_used' => $batchesUsed];
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

            if (!$medicine || $medicine->stock < $qtyNeeded) {
                session()->flash('error', 'Insufficient stock to approve this request.');
                return;
            }

            $totalAvailable = $medicine->batches->sum('quantity');
            if ($totalAvailable < $qtyNeeded) {
                session()->flash('error', "Insufficient batch stock. Only {$totalAvailable} available.");
                return;
            }

            $fifo = $this->deductFIFO($medicine, $qtyNeeded);

            $request->update([
                'status'            => 'ready_to_pickup',
                'approved_by_id'    => auth()->id(),
                'approved_by_type'  => get_class(auth()->user()),
                'approved_at'       => now(),
                'ready_at'          => now(),
                'reserved_at'       => now(),
                'reserved_quantity' => $qtyNeeded,
                'batches_snapshot'  => $fifo['batches_used'], // cast handles encoding
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
        session()->flash('message', 'Request approved and stock reserved. Patient notified to pick up.');
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

            // ✅ NEW: block dispense if medicine is archived
            if ($request->medicine?->trashed()) {
                session()->flash('error', 'Cannot dispense — medicine has been archived. Please cancel this request instead.');
                return;
            }

            $request->update([
                'status'          => 'completed',
                'dispensed_at'    => now(),
                'dispensed_by_id' => auth()->id(),
            ]);

            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $request->requester_name,
                'medicine_name'       => $request->medicine->medicine_name,
                'dosage'              => $request->medicine->dosage,
                'quantity'            => $request->reserved_quantity ?? $request->quantity_requested,
                'batches_used'        => $request->batches_snapshot ?? [], // reuse snapshot
                'action'              => 'dispensed',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username ?? auth()->user()->full_name,
                'performed_at'        => now(),
            ]);
        });

        session()->flash('message', 'Medicine dispensed successfully.');
    }

    // ─── Cancel ready request ─────────────────────────────────────

    public function cancelReadyRequest($requestId): void
    {
        DB::transaction(function () use ($requestId) {
            $request = MedicineRequest::with([
                    'medicine' => fn($q) => $q->withTrashed()->with('batches'),
                ])
                ->lockForUpdate()
                ->findOrFail($requestId);

            if ($request->status !== 'ready_to_pickup') {
                session()->flash('error', 'Only "Ready to Pick Up" requests can be cancelled here.');
                return;
            }

            $medicine        = $request->medicine;
            $qtyToRestore    = $request->reserved_quantity ?? $request->quantity_requested;
            // ✅ cast handles decoding — no json_decode() needed
            $batchesSnapshot = $request->batches_snapshot ?? [];

            foreach ($batchesSnapshot as $snap) {
                $batch = MedicineBatch::find($snap['batch_id']);
                if ($batch) {
                    $batch->increment('quantity', $snap['qty_taken']);
                }
            }

            if ($medicine && !$medicine->trashed()) {
                $newStock = $medicine->stock + $qtyToRestore;
                $medicine->update([
                    'stock'        => $newStock,
                    'stock_status' => $this->determineStockStatus($newStock),
                ]);
            }

            $request->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

            MedicineRequestLog::create([
                'medicine_request_id' => $request->id,
                'patient_name'        => $request->requester_name,
                'medicine_name'       => $medicine->medicine_name ?? 'Unknown',
                'dosage'              => $medicine->dosage ?? 'N/A',
                'quantity'            => $qtyToRestore,
                'action'              => 'cancelled',
                'performed_by_id'     => auth()->id(),
                'performed_by_name'   => auth()->user()->username ?? auth()->user()->full_name,
                'performed_at'        => now(),
            ]);
        });

        session()->flash('message', 'Request cancelled and stock restored successfully.');
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

        session()->flash('message', 'Medicine request rejected.');
    }

    // ─── Walk-in ──────────────────────────────────────────────────

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

            if ($medicine->stock < $qtyNeeded) {
                throw new \Exception("Insufficient stock. Only {$medicine->stock} available.");
            }

            $totalAvailable = $medicine->batches->sum('quantity');
            if ($totalAvailable < $qtyNeeded) {
                throw new \Exception("Insufficient batch stock. Only {$totalAvailable} available.");
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

            // ✅ FIFO deduction — $fifo resolved before log creation
            $fifo    = $this->deductFIFO($medicine, $qtyNeeded);
            $request = MedicineRequest::create($requestData);

            // ✅ Use $patientName, $medicine, $qtyNeeded, $fifo — NOT $request->*
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
        session()->flash('message', 'Walk-in medicine dispensed successfully.');
    }

    protected function validateWalkInStock(): bool
    {
        if ($this->walkInMedicineId && $this->walkInQuantity) {
            $medicine = Medicine::find($this->walkInMedicineId);
            if (!$medicine) {
                $this->addError('walkInMedicineId', 'Selected medicine not found.');
                return false;
            }
            if ($this->walkInQuantity > $medicine->stock) {
                $this->addError('walkInQuantity', "Quantity exceeds available stock ({$medicine->stock} available).");
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