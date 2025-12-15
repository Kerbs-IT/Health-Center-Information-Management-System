<?php

namespace App\Livewire;

use App\Models\medical_record_cases;
use App\Models\patients;
use App\Models\vaccination_masterlists;
use App\Models\wra_masterlists;
use Livewire\Component;
use Livewire\WithPagination;

class PatientList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all'; // all, active, archived

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshPatients' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function archivePatient($patientId)
    {
        $patient = patients::findOrFail($patientId);
        $patient->update(['status' => 'Archived']);
        $medicalRecord = medical_record_cases::where("patient_id", $patientId)->get();

        if ($medicalRecord) {
            foreach ($medicalRecord as $record) {
                if ($record->type_of_case == 'prenatal' || $record->type_of_case == 'family-planning') {
                    $wra_masterlist = wra_masterlists::where('medical_record_case_id', $record->id)->first();

                    $wra_masterlist->update([
                        'status' => 'Archived'
                    ]);
                }else{
                    if ($record->type_of_case == 'vaccination'){
                        $vaccination_masterlist = vaccination_masterlists::where('medical_record_case_id',$record->id)->first();
                        $vaccination_masterlist->update([
                            'status'=>'Archived'
                        ]);
                    }
                }
                
            }
        }

        $this->dispatch('patientArchived');
    }

    public function activatePatient($patientId)
    {
        $patient = patients::findOrFail($patientId);
        $patient->update(['status' => 'Active']);
        // update masterlist
        $medicalRecord = medical_record_cases::where("patient_id", $patientId)->get();

        if($medicalRecord){
            foreach ($medicalRecord as $record) {
                if($record->type_of_case == 'prenatal' || $record->type_of_case == 'family-planning'){
                    $wra_masterlist = wra_masterlists::where('medical_record_case_id',$record->id)->first();

                    $wra_masterlist->update([
                        'status' => 'Active'
                    ]);

                } else {
                    if ($record->type_of_case == 'vaccination') {
                        $vaccination_masterlist = vaccination_masterlists::where('medical_record_case_id', $record->id)->first();
                        $vaccination_masterlist->update([
                            'status' => 'Active'
                        ]);
                    }
                }
            }
        }

        $this->dispatch('patientActivated');
    }

    public function render()
    {
        $query = patients::query();

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('contact_number', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $patients = $query->latest()->paginate(10);

        return view('livewire.patient-list', [
            'patients' => $patients
        ]);
    }
}
