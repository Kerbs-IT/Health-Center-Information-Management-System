<?php

namespace App\Livewire\Prenatal;

use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\pregnancy_checkups;
use App\Models\pregnancy_plans;
use App\Models\prenatal_case_records;
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
    protected $listeners = ['prenatalRefreshTable' => '$refresh'];
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
        // For single record by ID, no sorting needed
        $prenatalCaseRecords = medical_record_cases::with(
            'pregnancy_checkup'
        )->where('id', $this->caseId)
            ->firstOrFail();

        $prenatal_case_record = prenatal_case_records::with('pregnancy_timeline_records') -> where("medical_record_case_id", $this->caseId)->where("status",'!=','Archived')
        ->get();
        $pregnancy_plan = pregnancy_plans::where("medical_record_case_id", $this->caseId)->where("status", '!=', 'Archived')->first();

        // For multiple records, use ONLY orderBy OR latest, not both
        $prenatalCheckupRecords = pregnancy_checkups::where('medical_record_case_id', $this->caseId)
            ->where('status', '!=', 'Archived')
            ->orderBy($this->sortField, $this->sortDirection) // Choose this OR latest(), not both
            ->paginate(8);

        // Rest of your code...
        $familyPlanningMedicalCase = medical_record_cases::where('patient_id', $prenatalCaseRecords->patient_id)
            ->where('type_of_case', 'family-planning')
            ->latest()
            ->first();

        $familyPlanCaseInfo = null;
        $familyPlanSideB = null;

        if ($familyPlanningMedicalCase) {
            $familyPlanCaseInfo = family_planning_case_records::with([
                'medical_history',
                'obsterical_history',
                'risk_for_sexually_transmitted_infection',
                'physical_examinations'
            ])->where('medical_record_case_id', $familyPlanningMedicalCase->id)
                ->where('status', '!=','Archived')
                ->latest()
                ->first();

            $familyPlanSideB = family_planning_side_b_records::where('medical_record_case_id', $familyPlanningMedicalCase->id)
                ->where('status', '!=', 'Archived')
                ->latest()
                ->first();
        }

        // for viewing patient info
        $patientInfo = medical_record_cases::with(['patient', 'prenatal_medical_record']) -> where('id', $this->caseId)->first();
        

        return view('livewire.prenatal.patient-case-table', [
            'isActive' => true,
            'page' => 'RECORD',
            'prenatalCaseRecords' => $prenatalCaseRecords,
            'familyPlanningRecord' => $familyPlanCaseInfo,
            'familyPlanSidebRecord' => $familyPlanSideB,
            'patientInfo' => $patientInfo,
            'caseId' => $this->caseId,
            'prenatalCheckupRecords' => $prenatalCheckupRecords,
            'prenatal_case_record'=> $prenatal_case_record,
            'pregnancy_plan' => $pregnancy_plan
        ]);
    }
}
