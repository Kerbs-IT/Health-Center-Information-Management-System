<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicineRequest;
use App\Models\patients;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class MedicineRequestComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $medicines;
    public $selectedMedicineId;
    public $quantity = 1;
    public $reason = '';

    public $edit_id;
    public $deleteRequestMedicineId;


    // Who is the request for
    public $requestFor = 'self'; // 'self' or 'child'
    public $selectedChildId = null;
    public $childSearch = '';
    public $children = [];

    // Properties for view details
    public $viewRequest;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;


    public function updatedSearch()
    {
        $this->resetPage('activePage');
        $this->resetPage('historyPage');
    }

    public function updatedStatusFilter()
    {
        $this->resetPage('historyPage');
    }

    public function updatedPerPage()
    {
        $this->resetPage('activePage');
        $this->resetPage('historyPage');
    }

    protected $rules = [
        'selectedMedicineId' => 'required|exists:medicines,medicine_id',
        'quantity'           => 'required|integer|min:1',
        'reason'             => 'nullable|string|max:500',
        'requestFor'         => 'required|in:self,child',
        'selectedChildId'    => 'nullable|exists:patients,id',
    ];

    protected $messages = [
        'selectedChildId.exists' => 'Selected child was not found.',
    ];

    public function mount()
    {
        $this->loadMedicines();
        $this->loadChildren();
    }

    public function loadMedicines()
    {
        // Load medicines and compute available stock (physical - reserved) per medicine.
        // Only show medicines that actually have unreserved units ready to be requested.
        $this->medicines = Medicine::where('stock_status', '!=', 'Out of Stock')
            ->where('stock', '>', 0)
            ->where('expiry_status', '!=', 'Expired')
            ->where(function ($query) {
                $query->where('expiry_date', '>', now())
                    ->orWhereNull('expiry_date');
            })
            ->whereHas('batches') // has at least one valid batch
            ->get()
            ->map(function ($medicine) {
                // Compute truly available stock = sum(quantity - reserved_quantity)
                // across all non-expired, non-trashed batches
                $medicine->available_stock = (int) MedicineBatch::where('medicine_id', $medicine->medicine_id)
                    ->where('expiry_date', '>', now())
                    ->sum(DB::raw('quantity - reserved_quantity'));

                return $medicine;
            })
            ->filter(fn($medicine) => $medicine->available_stock > 0) // hide fully-reserved ones
            ->values();
    }

    public function loadChildren()
    {
        $userId = auth()->id();

        $this->children = patients::where('guardian_user_id', $userId)
            ->when($this->childSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->childSearch}%")
                      ->orWhere('last_name', 'like', "%{$this->childSearch}%")
                      ->orWhereRaw(
                          "CONCAT(first_name, ' ', IFNULL(middle_initial, ''), ' ', last_name) LIKE ?",
                          ["%{$this->childSearch}%"]
                      );
                });
            })
            ->orderBy('first_name')
            ->get();
    }

    public function updatedChildSearch()
    {
        $this->selectedChildId = null;
        $this->loadChildren();
    }

    public function updatedRequestFor()
    {
        $this->reset(['selectedChildId', 'childSearch']);
        $this->resetErrorBag(['selectedChildId', 'childSearch']);
        $this->loadChildren();
    }

    // ─── Helper: get available stock for a medicine ───────────────
    // Reusable so both submitRequest and updateRequest use the same logic.

    private function getAvailableStock(int $medicineId): int
    {
        return (int) MedicineBatch::where('medicine_id', $medicineId)
            ->where('expiry_date', '>', now())
            ->sum(DB::raw('quantity - reserved_quantity'));
    }

    // ─── Submit ───────────────────────────────────────────────────

    public function submitRequest()
    {
        $this->validate();

        if ($this->requestFor === 'child' && !$this->selectedChildId) {
            $this->addError('selectedChildId', 'Please select a child to request medicine for.');
            return;
        }

        $medicine = Medicine::findOrFail($this->selectedMedicineId);

        if ($medicine->expiry_status === 'Expired' || ($medicine->expiry_date && $medicine->expiry_date <= now())) {
            $this->addError('selectedMedicineId', 'This medicine has expired and cannot be requested.');
            return;
        }

        // ── Validate against AVAILABLE stock, not total stock ──
        $available = $this->getAvailableStock($medicine->medicine_id);
        if ($this->quantity > $available) {
            $this->addError(
                'quantity',
                "Requested quantity exceeds available stock. Only {$available} unit(s) available (some may be reserved for other approved requests)."
            );
            return;
        }

        $user = auth()->user();

        $requestData = [
            'medicine_id'        => $this->selectedMedicineId,
            'quantity_requested' => $this->quantity,
            'reason'             => $this->reason,
            'status'             => 'pending',
        ];

        if ($this->requestFor === 'child') {
            $child = patients::where('id', $this->selectedChildId)
                ->where('guardian_user_id', $user->id)
                ->firstOrFail();
            $requestData['patients_id'] = $child->id;
        } else {
            $patient = $user->patient;
            if ($patient) {
                $requestData['patients_id'] = $patient->id;
            } else {
                $requestData['user_id'] = $user->id;
            }
        }

        MedicineRequest::create($requestData);

        $this->resetForm();
        $this->dispatch('medicineRequest-added');
        $this->dispatch('close-medicineRequest');
    }

    // ─── Edit ─────────────────────────────────────────────────────

    public function editRequest($requestId)
    {
        $this->dispatch('show-editRequest-modal');
        $request = MedicineRequest::findOrFail($requestId);

        if ($request->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be edited.');
            return;
        }

        $user    = auth()->user();
        $patient = $user->patient;

        $authorized = false;
        if ($patient && $request->patients_id === $patient->id) {
            $authorized = true;
        } elseif ($request->user_id === $user->id) {
            $authorized = true;
        } else {
            $childIds = patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();
            if (in_array($request->patients_id, $childIds)) {
                $authorized = true;
            }
        }

        if (!$authorized) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $this->edit_id            = $request->id;
        $this->selectedMedicineId = $request->medicine_id;
        $this->quantity           = $request->quantity_requested;
        $this->reason             = $request->reason;

        $childIds = patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();
        if ($request->patients_id && in_array($request->patients_id, $childIds)) {
            $this->requestFor      = 'child';
            $this->selectedChildId = $request->patients_id;
        } else {
            $this->requestFor = 'self';
        }
    }

    // ─── Update ───────────────────────────────────────────────────

    public function updateRequest()
    {
        $this->validate();

        if ($this->requestFor === 'child' && !$this->selectedChildId) {
            $this->addError('selectedChildId', 'Please select a child to request medicine for.');
            return;
        }

        $request = MedicineRequest::findOrFail($this->edit_id);
        $user    = auth()->user();
        $patient = $user->patient;

        $authorized = false;
        if ($patient && $request->patients_id === $patient->id) {
            $authorized = true;
        } elseif ($request->user_id === $user->id) {
            $authorized = true;
        } else {
            $childIds = patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();
            if (in_array($request->patients_id, $childIds)) {
                $authorized = true;
            }
        }

        if (!$authorized) {
            $this->dispatch('swal:error', ['title' => 'Unauthorized', 'text' => 'You are not authorized to perform this action.']);
            return;
        }

        if ($request->status !== 'pending') {
            $this->dispatch('swal:error', ['title' => 'Cannot Update', 'text' => 'Only pending requests can be updated.']);
            return;
        }

        $medicine = Medicine::findOrFail($this->selectedMedicineId);

        if ($medicine->expiry_status === 'Expired' || ($medicine->expiry_date && $medicine->expiry_date <= now())) {
            $this->dispatch('swal:error', ['title' => 'Medicine Expired', 'text' => 'This medicine has expired and cannot be requested.']);
            return;
        }

        // ── Validate against AVAILABLE stock, not total stock ──
        $available = $this->getAvailableStock($medicine->medicine_id);
        if ($this->quantity > $available) {
            $this->addError(
                'quantity',
                "Requested quantity exceeds available stock. Only {$available} unit(s) available (some may be reserved for other approved requests)."
            );
            return;
        }

        $updateData = [
            'medicine_id'        => $this->selectedMedicineId,
            'quantity_requested' => $this->quantity,
            'reason'             => $this->reason,
        ];

        if ($this->requestFor === 'child') {
            $child = patients::where('id', $this->selectedChildId)
                ->where('guardian_user_id', $user->id)
                ->firstOrFail();
            $updateData['patients_id'] = $child->id;
            $updateData['user_id']     = null;
        } else {
            if ($patient) {
                $updateData['patients_id'] = $patient->id;
                $updateData['user_id']     = null;
            } else {
                $updateData['user_id']     = $user->id;
                $updateData['patients_id'] = null;
            }
        }

        $request->update($updateData);

        $this->dispatch('swal:success', ['title' => 'Updated!', 'text' => 'Medicine request updated successfully.']);
        $this->dispatch('close-medicineRequest-modal');
        $this->resetForm();
    }

    // ─── View / Delete ────────────────────────────────────────────

    public function viewDetails($requestId)
    {
        $request = MedicineRequest::with(['medicine', 'patients', 'user'])->findOrFail($requestId);

        $user    = auth()->user();
        $patient = $user->patient;

        $authorized = false;
        if ($patient && $request->patients_id === $patient->id) {
            $authorized = true;
        } elseif ($request->user_id === $user->id) {
            $authorized = true;
        } else {
            $childIds = patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();
            if (in_array($request->patients_id, $childIds)) {
                $authorized = true;
            }
        }

        if (!$authorized) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $this->viewRequest = $request;
    }

    public function confirmRequestMedicineDelete($id)
    {
        $this->deleteRequestMedicineId = $id;
        $this->dispatch('show-deleleteRequestModal');
    }

    public function deleteRequest()
    {
        $request = MedicineRequest::findOrFail($this->deleteRequestMedicineId);

        $user    = auth()->user();
        $patient = $user->patient;

        $authorized = false;
        if ($patient && $request->patients_id === $patient->id) {
            $authorized = true;
        } elseif ($request->user_id === $user->id) {
            $authorized = true;
        } else {
            $childIds = patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();
            if (in_array($request->patients_id, $childIds)) {
                $authorized = true;
            }
        }

        if (!$authorized) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $request->delete();
        $this->dispatch('success-deleteMedicineRequestModal');
    }

    // ─── Reset ────────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'selectedMedicineId',
            'quantity',
            'reason',
            'edit_id',
            'requestFor',
            'selectedChildId',
            'childSearch',
        ]);

        $this->requestFor = 'self';
        $this->quantity   = 1;
        $this->resetErrorBag();
        $this->loadMedicines();
        $this->loadChildren();
    }

    // ─── Render ───────────────────────────────────────────────────

    public function render()
    {
        $user     = auth()->user();
        $patient  = $user->patient;
        $childIds = patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();

        $patientIds = $childIds;
        if ($patient) {
            $patientIds[] = $patient->id;
        }

        $baseQuery = MedicineRequest::with(['medicine', 'patients', 'user'])
            ->where(function ($query) use ($patientIds, $user) {
                if (!empty($patientIds)) {
                    $query->whereIn('patients_id', $patientIds);
                }
                $query->orWhere('user_id', $user->id);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('medicine', fn($m) =>
                    $m->where('medicine_name', 'like', "%{$this->search}%")
                );
            });

        $activeRequests = (clone $baseQuery)
            ->whereIn('status', ['pending', 'ready_to_pickup'])
            ->latest()
            ->paginate($this->perPage, ['*'], 'activePage');

        $historyRequests = (clone $baseQuery)
            ->whereIn('status', ['completed', 'rejected'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage, ['*'], 'historyPage');

        return view('livewire.medicine-request', compact('activeRequests', 'historyRequests', 'childIds'))
            ->layout('livewire.layouts.requestMedicineLayout', [
                'page' => 'Medicine Request'
            ]);
    }
}