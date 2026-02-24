<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\patients;

class MedicineRequestComponent extends Component
{
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
        $this->medicines = Medicine::where('stock_status', '!=', 'Out of Stock')
            ->where('stock', '>', 0)
            ->where('expiry_status', '!=', 'Expired')
            ->where(function ($query) {
                $query->where('expiry_date', '>', now())
                      ->orWhereNull('expiry_date');
            })
            ->get();
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
        // Reset child selection when toggling
        $this->reset(['selectedChildId', 'childSearch']);
        $this->resetErrorBag(['selectedChildId', 'childSearch']);
        $this->loadChildren();
    }

    public function submitRequest()
    {
        $this->validate();

        // Extra check: child must be selected when requestFor = child
        if ($this->requestFor === 'child' && !$this->selectedChildId) {
            $this->addError('selectedChildId', 'Please select a child to request medicine for.');
            return;
        }

        $medicine = Medicine::findOrFail($this->selectedMedicineId);

        if ($medicine->expiry_status === 'Expired' || ($medicine->expiry_date && $medicine->expiry_date <= now())) {
            $this->addError('selectedMedicineId', 'This medicine has expired and cannot be requested.');
            return;
        }

        if ($this->quantity > $medicine->stock) {
            $this->addError('quantity', 'Requested quantity exceeds available stock.');
            return;
        }

        $user  = auth()->user();

        $requestData = [
            'medicine_id'        => $this->selectedMedicineId,
            'quantity_requested' => $this->quantity,
            'reason'             => $this->reason,
            'status'             => 'pending',
        ];

        if ($this->requestFor === 'child') {
            // Verify child actually belongs to this guardian
            $child = patients::where('id', $this->selectedChildId)
                ->where('guardian_user_id', $user->id)
                ->firstOrFail();
            $requestData['patients_id'] = $child->id;
        } else {
            // Self — use the user's own patient record or user_id
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
    }

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
            // Check if it's one of the user's children
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

        // Determine if this was originally for self or child
        $childIds = patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();
        if ($request->patients_id && in_array($request->patients_id, $childIds)) {
            $this->requestFor      = 'child';
            $this->selectedChildId = $request->patients_id;
        } else {
            $this->requestFor = 'self';
        }
    }

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

        // Authorization check
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

        if ($this->quantity > $medicine->stock) {
            $this->addError('quantity', 'Requested quantity exceeds available stock.');
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

    public function render()
    {
        $user    = auth()->user();
        $patient = $user->patient;

        // Get all patient IDs this user has access to (self + children)
        $childIds = patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();

        $patientIds = $childIds;
        if ($patient) {
            $patientIds[] = $patient->id;
        }

        $myRequests = MedicineRequest::with(['medicine', 'patients', 'user'])
            ->where(function ($query) use ($patientIds, $user) {
                if (!empty($patientIds)) {
                    $query->whereIn('patients_id', $patientIds);
                }
                $query->orWhere('user_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.medicine-request', compact('myRequests'))
            ->layout('livewire.layouts.requestMedicineLayout');
    }
}