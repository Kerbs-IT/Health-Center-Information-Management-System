<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\MedicineRequest;

class MedicineRequestComponent extends Component
{
    public $medicines;
    public $selectedMedicineId;
    public $quantity = 1;
    public $reason = '';

    public $edit_id;
    public $deleteRequestMedicineId;

    // Properties for view details
    public $viewRequest;

    protected $rules = [
        'selectedMedicineId' => 'required|exists:medicines,medicine_id',
        'quantity' => 'required|integer|min:1',
        'reason' => 'required|string|max:500',
    ];

    public function mount(){

        $this->medicines = Medicine::where('status', '!=', 'Out of Stock')
            ->where('stock', '>', 0)
            ->get();
    }

    public function submitRequest()
    {
        $this->validate();

        $medicine = Medicine::findOrFail($this->selectedMedicineId);

        // Check if requested quantity is available
        if ($this->quantity > $medicine->stock) {
            $this->addError('quantity', 'Requested quantity exceeds available stock.');
            return;
        }

        // Get patient user ID
        $patientId = auth()->user()->patient;
         if (!$patientId) {
            $this->addError('error', 'Patient record not found. Please contact the administrator.');
            return;
        }

        MedicineRequest::create([
            'patients_id' => $patientId->id,
            'medicine_id' => $this->selectedMedicineId,
            'quantity_requested' => $this->quantity,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        $this->dispatch('medicineRequest-added');

        $this->reset(['selectedMedicineId', 'quantity', 'reason']);
    }

    public function editRequest($requestId)
    {
        $this->dispatch('show-editRequest-modal');
        $request = MedicineRequest::findOrFail($requestId);

        if ($request->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be edited.');
            return;
        }

        // Verify this request belongs to the current user
        $patient = auth()->user()->patient;
        if ($request->patients_id !== $patient->id) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $this->edit_id = $request->id;
        $this->selectedMedicineId = $request->medicine_id;
        $this->quantity = $request->quantity_requested;
        $this->reason = $request->reason;
    


    }


    public function updateRequest()
    {
        $this->validate();

        $request = MedicineRequest::findOrFail($this->edit_id);

        // Authorization check
        $patient = auth()->user()->patient;
        if ($request->patients_id !== $patient->id) {
            $this->dispatch('swal:error', [
                'title' => 'Unauthorized',
                'text' => 'You are not authorized to perform this action.',
            ]);
            return;
        }

        if ($request->status !== 'pending') {
            $this->dispatch('swal:error', [
                'title' => 'Cannot Update',
                'text' => 'Only pending requests can be updated.',
            ]);
            return;
        }

        // Check if new quantity is available
        $medicine = Medicine::findOrFail($this->selectedMedicineId);
        if ($this->quantity > $medicine->stock) {
            $this->addError('quantity', 'Requested quantity exceeds available stock.');
            return;
        }

        // Update the request
        $request->update([
            'medicine_id' => $this->selectedMedicineId,
            'quantity_requested' => $this->quantity,
            'reason' => $this->reason,
        ]);

        $this->dispatch('swal:success', [
            'title' => 'Updated!',
            'text' => 'Medicine request updated successfully.',
        ]);

        $this->dispatch('close-medicineRequest-modal');
        $this->resetForm();
    }

    public function viewDetails($requestId)
    {
        $request = MedicineRequest::with(['medicine', 'patients.user'])->findOrFail($requestId);
        // Verify this request belongs to the current user
        $patient = Auth()->user()->patient;
        if ($request->patients_id !== $patient->id) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $this->viewRequest = $request;
    }

    public function confirmRequestMedicineDelete($id){
        $this->deleteRequestMedicineId = $id;
        $this->dispatch('show-deleleteRequestModal');
    }

    public function deleteRequest()
    {
        MedicineRequest::findOrFail($this->deleteRequestMedicineId)->delete();

        $this->dispatch('success-deleteMedicineRequestModal');
    }

    public function resetForm()
    {
        $this->reset([
            'selectedMedicineId',
            'quantity',
            'reason',
            'edit_id',
        ]);

        $this->resetErrorBag();
    }

    public function render()
    {
        $patient = auth()->user()->patient;

        $myRequests = MedicineRequest::with('medicine')->where('patients_id', $patient->id)->latest()->paginate(10);

        return view('livewire.medicine-request', compact('myRequests'))->layout('livewire.layouts.requestMedicineLayout');
    }
}
