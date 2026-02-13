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

    protected $paginationTheme = 'bootstrap'; // Add this if you're using Bootstrap

    public $caseId;
    public $sortField = 'created_at';
    public $sortDirection = 'desc'; // Changed to desc as default (most recent first)
    public $medicalRecordCase;

    protected $listeners = ['prenatalRefreshTable' => '$refresh'];

    public function mount($caseId, $medicalRecordCase = null)
    {
        // Fetch the medical record case if not provided
        if ($medicalRecordCase === null) {
            $this->medicalRecordCase = medical_record_cases::findOrFail($caseId);
        } else {
            $this->medicalRecordCase = $medicalRecordCase;
        }
        $this->caseId = $caseId;
        $this->medicalRecordCase = $medicalRecordCase;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        // Reset to page 1 when sorting
        $this->resetPage();
    }

    public function render()
    {
        $prenatalCaseRecords = medical_record_cases::with('pregnancy_checkup')
            ->where('id', $this->caseId)
            ->firstOrFail();

        // Get all records without pagination first
        $prenatal_case_record = prenatal_case_records::with('pregnancy_timeline_records')
            ->where("medical_record_case_id", $this->caseId)
            ->where("status", '!=', 'Archived')
            ->get();

        $pregnancy_plan = pregnancy_plans::where("medical_record_case_id", $this->caseId)
            ->where("status", '!=', 'Archived')
            ->first();

        $prenatalCheckupRecords = pregnancy_checkups::where('medical_record_case_id', $this->caseId)
            ->where('status', '!=', 'Archived')
            ->get();

        // Get family planning records
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
                ->where('status', '!=', 'Archived')
                ->latest()
                ->first();

            $familyPlanSideB = family_planning_side_b_records::where('medical_record_case_id', $familyPlanningMedicalCase->id)
                ->where('status', '!=', 'Archived')
                ->latest()
                ->first();
        }

        // Combine all records into one collection
        $allRecords = collect();

        // Add prenatal case records
        foreach ($prenatal_case_record as $record) {
            $allRecords->push([
                'id' => $record->id,
                'type_of_record' => $record->type_of_record,
                'created_at' => $record->created_at,
                'status' => $record->status,
                'record_type' => 'prenatal_case',
                'data' => $record
            ]);
        }

        // Add pregnancy plan
        if ($pregnancy_plan) {
            $allRecords->push([
                'id' => $pregnancy_plan->id,
                'type_of_record' => $pregnancy_plan->type_of_record,
                'created_at' => $pregnancy_plan->created_at,
                'status' => $pregnancy_plan->status,
                'record_type' => 'pregnancy_plan',
                'data' => $pregnancy_plan
            ]);
        }

        // Add family planning records
        if ($familyPlanCaseInfo) {
            $allRecords->push([
                'id' => $familyPlanCaseInfo->id,
                'type_of_record' => $familyPlanCaseInfo->type_of_record,
                'created_at' => $familyPlanCaseInfo->created_at,
                'status' => $familyPlanCaseInfo->status,
                'record_type' => 'family_planning',
                'data' => $familyPlanCaseInfo
            ]);
        }

        if ($familyPlanSideB) {
            $allRecords->push([
                'id' => $familyPlanSideB->id,
                'type_of_record' => $familyPlanSideB->type_of_record,
                'created_at' => $familyPlanSideB->created_at,
                'status' => $familyPlanSideB->status,
                'record_type' => 'family_planning_side_b',
                'data' => $familyPlanSideB
            ]);
        }

        // Add prenatal checkup records
        foreach ($prenatalCheckupRecords as $checkup) {
            $allRecords->push([
                'id' => $checkup->id,
                'type_of_record' => $checkup->type_of_record,
                'created_at' => $checkup->created_at,
                'status' => $checkup->status,
                'record_type' => 'checkup',
                'data' => $checkup
            ]);
        }

        // Sort the collection
        if ($this->sortDirection === 'asc') {
            $allRecords = $allRecords->sortBy('created_at')->values();
        } else {
            $allRecords = $allRecords->sortByDesc('created_at')->values();
        }

        // Use Livewire's pagination - FIXED
        $perPage = 10;
        $currentPage = $this->getPage(); // Use Livewire's page tracker

        $paginatedRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $allRecords->forPage($currentPage, $perPage)->values(),
            $allRecords->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                'pageName' => 'page'
            ]
        );

        $patientInfo = medical_record_cases::with(['patient', 'prenatal_medical_record'])
            ->where('id', $this->caseId)
            ->first();

        return view('livewire.prenatal.patient-case-table', [
            'isActive' => true,
            'page' => 'RECORD',
            'prenatalCaseRecords' => $prenatalCaseRecords,
            'patientInfo' => $patientInfo,
            'caseId' => $this->caseId,
            'allRecords' => $paginatedRecords,
        ]);
    }

    public function exportPdf($caseId)
    {
        return redirect()->route('prenatal-case.pdf', [
            'caseId' => $caseId,
        ]);
    }

    public function exportPregnancyPlan($planId)
    {
        return redirect()->route('pregnancy-plan.pdf', [
            'planId' => $planId,
        ]);
    }

    public function exportFamilyPlanPdf($caseId, $type)
    {
        return redirect()->route("family-planning-side-$type.pdf", [
            'caseId' => $caseId,
        ]);
    }

    public function exportCheckupPdf($caseId)
    {
        return redirect()->route("prenatal-checkup.pdf", [
            'caseId' => $caseId,
        ]);
    }
}
