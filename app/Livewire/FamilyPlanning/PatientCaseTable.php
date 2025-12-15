<?php

namespace App\Livewire\FamilyPlanning;

use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use Livewire\Component;
use Livewire\WithPagination;

class PatientCaseTable extends Component
{
    use WithPagination;
    public $medicalRecordCaseId;
    // for sorting
    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    // Optional: listen to events for add/edit/archive
    protected $listeners = ['familyPlanningRefreshTable' => '$refresh'];
    public function mount($medicalRecordCaseId)
    {
        $this->$medicalRecordCaseId = $medicalRecordCaseId; // THIS catches the ID from URL
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
        $familyPlanningCases = family_planning_case_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->orderBy($this->sortField, $this->sortDirection)
            ->latest()
            ->get();
        $familyPlanningSideB = family_planning_side_b_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->orderBy($this->sortField, $this->sortDirection)
            ->latest()
            ->get();
        
        $patientInfo = medical_record_cases::with(['family_planning_medical_record', 'patient'])->findOrFail($this->medicalRecordCaseId);

        $address = patient_addresses::where("patient_id", $patientInfo->patient->id)->first() ?? null;
        return view('livewire.family-planning.patient-case-table', 
        ['isActive' => true, 'page' => 'RECORD', 
        'familyPlanningCases' => $familyPlanningCases, 
        'patientInfo' => $patientInfo, 
        'familyPlanningSideB' => $familyPlanningSideB,
        'medicalRecordCaseId'=> $this-> medicalRecordCaseId,
        'address'=> $address]);

    }

    public function exportPdf($caseId,$type)
    {
        return redirect()->route("family-planning-side-$type.pdf", [
            'caseId' => $caseId, // Sends "desc"
        ]);
    }

}
