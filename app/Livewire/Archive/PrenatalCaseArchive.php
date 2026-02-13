<?php

namespace App\Livewire\Archive;

use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\pregnancy_checkups;
use App\Models\pregnancy_plans;
use App\Models\prenatal_case_records;
use App\Models\prenatal_medical_records;
use Livewire\Component;
use Livewire\WithPagination;

class PrenatalCaseArchive extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $caseId;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $medicalRecordCase; // This will be fetched in mount
    public $medical_record_id ;

    protected $listeners = ['prenatalRefreshTable' => '$refresh'];

    public function mount($caseId)  // Only require caseId
    {
        $this->caseId = $caseId;
        // Fetch the medical record case here
        $this->medical_record_id = request() -> get('medical_record_id');
        $this->medicalRecordCase = medical_record_cases::findOrFail($caseId);
    }
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function restoreRecord($recordId, $recordType)
    {
        try {
            switch ($recordType) {
                case 'prenatal_case':
                    $this->restorePrenatalCase($recordId);
                    break;
                case 'pregnancy_plan':
                    $this->restorePregnancyPlan($recordId);
                    break;
                case 'checkup':
                    $this->restoreCheckup($recordId);
                    break;
                case 'family_planning':
                    $this->restoreFamilyPlanning($recordId);
                    break;
                case 'family_planning_side_b':
                    $this->restoreFamilyPlanningSideB($recordId);
                    break;
            }
        } catch (\Exception $e) {
            $this->dispatch('activationError', message: $e->getMessage());
        }
    }

    private function restorePrenatalCase($recordId)
    {
        // Check if there's an active prenatal case record
        $activeRecord = prenatal_case_records::where('medical_record_case_id', $this->caseId)
            ->where('status', '!=', 'Archived')
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active prenatal case record already exists.');
        }

        $record = prenatal_case_records::findOrFail($recordId);
        $record->status = 'Active';
        $record->save();

        $this->dispatch('patientActivated');
        $this->dispatch('prenatalRefreshTable');
    }

    private function restorePregnancyPlan($recordId)
    {
        // Check if there's an active pregnancy plan
        $activeRecord = pregnancy_plans::where('medical_record_case_id', $this->caseId)
            ->where('status', '!=', 'Archived')
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active pregnancy plan already exists.');
        }

        $record = pregnancy_plans::findOrFail($recordId);
        $record->status = 'Active';
        $record->save();

        $this->dispatch('patientActivated');
        $this->dispatch('prenatalRefreshTable');
    }

    private function restoreCheckup($recordId)
    {
        $archivedRecord = pregnancy_checkups::findOrFail($recordId);

        // Check if there's an active checkup with the same date_of_comeback
        $activeRecord = pregnancy_checkups::where('medical_record_case_id', $this->caseId)
            ->where('status', '!=', 'Archived')
            ->where('date_of_comeback', $archivedRecord->date_of_comeback)
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active checkup record with the same comeback date already exists.');
        }

        $archivedRecord->status = 'Active';
        $archivedRecord->save();

        $this->dispatch('patientActivated');
        $this->dispatch('prenatalRefreshTable');
    }

    private function restoreFamilyPlanning($recordId)
    {
        // NEW CODE BLOCK - Check if family planning is allowed
        $prenatalMedicalRecord = prenatal_medical_records::where('medical_record_case_id', $this->caseId)
            ->first();

        if (!$prenatalMedicalRecord || strtolower($prenatalMedicalRecord->family_planning_decision) !== 'yes') {
            throw new \Exception('Cannot restore: Family planning decision must be "Yes" in prenatal medical record.');
        }
        $archivedRecord = family_planning_case_records::findOrFail($recordId);

        // Check if there's an active family planning record
        $activeRecord = family_planning_case_records::where('medical_record_case_id', $archivedRecord->medical_record_case_id)
            ->where('status', '!=', 'Archived')
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active family planning record already exists.');
        }

        $archivedRecord->status = 'Active';
        $archivedRecord->save();

        $this->dispatch('patientActivated');
        $this->dispatch('prenatalRefreshTable');
    }

    private function restoreFamilyPlanningSideB($recordId)
    {
        // NEW CODE BLOCK - Check if family planning is allowed
        $prenatalMedicalRecord = prenatal_medical_records::where('medical_record_case_id', $this->caseId)
            ->first();

        if (!$prenatalMedicalRecord || strtolower($prenatalMedicalRecord->family_planning_decision) !== 'yes') {
            throw new \Exception('Cannot restore: Family planning decision must be "Yes" in prenatal medical record.');
        }
        // END NEW CODE BLOCK
        $archivedRecord = family_planning_side_b_records::findOrFail($recordId);

        // Check if there's an active side B record with the same date_of_follow_up_visit
        $activeRecord = family_planning_side_b_records::where('medical_record_case_id', $archivedRecord->medical_record_case_id)
            ->where('status', '!=', 'Archived')
            ->where('date_of_follow_up_visit', $archivedRecord->date_of_follow_up_visit)
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active family planning side B record with the same follow-up date already exists.');
        }

        $archivedRecord->status = 'Active';
        $archivedRecord->save();

        $this->dispatch('patientActivated');
        $this->dispatch('prenatalRefreshTable');
    }

    public function render()
    {
        $prenatalCaseRecords = medical_record_cases::with('pregnancy_checkup')
            ->where('id', $this->caseId)
            ->firstOrFail();

        // Get all ARCHIVED records
        $prenatal_case_record = prenatal_case_records::with('pregnancy_timeline_records')
            ->where("medical_record_case_id", $this->caseId)
            ->where("status", 'Archived')
            ->get();

        $pregnancy_plan = pregnancy_plans::where("medical_record_case_id", $this->caseId)
            ->where("status", 'Archived')
            ->get();

        $prenatalCheckupRecords = pregnancy_checkups::where('medical_record_case_id', $this->caseId)
            ->where('status', 'Archived')
            ->get();

        // Get family planning records
        $familyPlanningMedicalCase = medical_record_cases::where('patient_id', $prenatalCaseRecords->patient_id)
            ->where('type_of_case', 'family-planning')
            ->latest()
            ->first();

        $familyPlanCaseInfo = collect();
        $familyPlanSideB = collect();

        if ($familyPlanningMedicalCase) {
            $familyPlanCaseInfo = family_planning_case_records::with([
                'medical_history',
                'obsterical_history',
                'risk_for_sexually_transmitted_infection',
                'physical_examinations'
            ])->where('medical_record_case_id', $familyPlanningMedicalCase->id)
                ->where('status', 'Archived')
                ->get();

            $familyPlanSideB = family_planning_side_b_records::where('medical_record_case_id', $familyPlanningMedicalCase->id)
                ->where('status', 'Archived')
                ->get();
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

        // Add pregnancy plans
        foreach ($pregnancy_plan as $plan) {
            $allRecords->push([
                'id' => $plan->id,
                'type_of_record' => $plan->type_of_record,
                'created_at' => $plan->created_at,
                'status' => $plan->status,
                'record_type' => 'pregnancy_plan',
                'data' => $plan
            ]);
        }

        // Add family planning records
        foreach ($familyPlanCaseInfo as $info) {
            $allRecords->push([
                'id' => $info->id,
                'type_of_record' => $info->type_of_record,
                'created_at' => $info->created_at,
                'status' => $info->status,
                'record_type' => 'family_planning',
                'data' => $info
            ]);
        }

        foreach ($familyPlanSideB as $sideB) {
            $allRecords->push([
                'id' => $sideB->id,
                'type_of_record' => $sideB->type_of_record,
                'created_at' => $sideB->created_at,
                'status' => $sideB->status,
                'record_type' => 'family_planning_side_b',
                'data' => $sideB
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

        // Use Livewire's pagination
        $perPage = 10;
        $currentPage = $this->getPage();

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

        return view('livewire.archive.prenatal-case-archive', [
            'isActive' => true,
            'page' => 'ARCHIVED',
            'prenatalCaseRecords' => $prenatalCaseRecords,
            'patientInfo' => $patientInfo,
            'caseId' => $this->caseId,
            'allRecords' => $paginatedRecords,
        ]);
    }

   
}
