<?php

namespace App\Livewire\SeniorCitizen;

use App\Models\medical_record_cases;
use App\Models\senior_citizen_case_records;
use Livewire\Component;
use Livewire\WithPagination;

class PatientCaseTable extends Component
{
    use WithPagination;
    public $caseId;
    // for sorting
    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    // Optional: listen to events for add/edit/archive
    protected $listeners = ['seniorCitizenRefreshTable' => '$refresh'];
    protected $paginationTheme = 'bootstrap';
    public function mount($caseId)
    {
        $this->caseId = $caseId; // THIS catches the ID from URL
    }
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }



    public function render()
    {
        $seniorCaseRecords = senior_citizen_case_records::where('medical_record_case_id', $this-> caseId)
        ->where('status','!=','Archived')
        ->orderBy($this->sortField, $this->sortDirection)
        ->latest()
        ->paginate(10);
        $patientRecord = medical_record_cases::with('patient', 'senior_citizen_medical_record')->findOrFail( $this->caseId);
        return view(
            'livewire.senior-citizen.patient-case-table',
            [
                'isActive' => true,
                'page' => 'RECORD',
                'seniorCaseRecords' =>  $seniorCaseRecords,
                'patient_name' => $patientRecord->patient->full_name,
                'healthWorkerId' => $patientRecord->senior_citizen_medical_record->health_worker_id,
                'medicalRecordId' => $this->caseId
            ]
        );
       
    }
    public function exportPdf($caseId)
    {
        return redirect()->route('senior-citizen-case.pdf', [
            'caseId' => $caseId,              // Sends "Maria"
        ]);
    }
}
