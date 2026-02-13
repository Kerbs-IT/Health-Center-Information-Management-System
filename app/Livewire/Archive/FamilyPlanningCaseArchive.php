<?php

namespace App\Livewire\Archive;

use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use Livewire\Component;
use Livewire\WithPagination;

class FamilyPlanningCaseArchive extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $medicalRecordCaseId;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $section = 'family-planning'; // For back button

    protected $listeners = ['familyPlanningRefreshTable' => '$refresh'];

    public function mount($medicalRecordCaseId = null)
    {
        // Get medicalRecordCaseId from parameter or from URL
        $this->medicalRecordCaseId = $medicalRecordCaseId ?? request()->get('medical_record_id');

        // Validate that the case exists
        if (!$this->medicalRecordCaseId || !medical_record_cases::find($this->medicalRecordCaseId)) {
            abort(404, 'Medical record case not found');
        }
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
                case 'family_planning_side_a':
                    $this->restoreFamilyPlanningSideA($recordId);
                    break;
                case 'family_planning_side_b':
                    $this->restoreFamilyPlanningSideB($recordId);
                    break;
            }
        } catch (\Exception $e) {
            $this->dispatch('activationError', message: $e->getMessage());
        }
    }

    private function restoreFamilyPlanningSideA($recordId)
    {
        // Check if there's an active family planning case record (Side A)
        $activeRecord = family_planning_case_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active family planning case record (Side A) already exists.');
        }

        $record = family_planning_case_records::findOrFail($recordId);
        $record->status = 'Active';
        $record->save();

        $this->dispatch('patientActivated');
    }

    private function restoreFamilyPlanningSideB($recordId)
    {
        $archivedRecord = family_planning_side_b_records::findOrFail($recordId);

        // Check if there's an active Side B record with the same date_of_follow_up_visit
        $activeRecord = family_planning_side_b_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->where('date_of_follow_up_visit', $archivedRecord->date_of_follow_up_visit)
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active family planning Side B record with the same follow-up date already exists.');
        }

        $archivedRecord->status = 'Active';
        $archivedRecord->save();

        $this->dispatch('patientActivated');
    }

    public function render()
    {
        // Get all ARCHIVED records
        $familyPlanningCases = family_planning_case_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', 'Archived')
            ->latest()
            ->get();

        $familyPlanningSideB = family_planning_side_b_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', 'Archived')
            ->latest()
            ->get();

        $patientInfo = medical_record_cases::with(['family_planning_medical_record', 'patient'])
            ->findOrFail($this->medicalRecordCaseId);

        // Combine all records
        $allRecords = collect();

        // Push family planning Side A
        foreach ($familyPlanningCases as $record) {
            $allRecords->push([
                'id' => $record->id,
                'type_of_record' => $record->type_of_record,
                'created_at' => $record->created_at,
                'status' => $record->status,
                'record_type' => 'family_planning_side_a',
                'data' => $record
            ]);
        }

        // Push Side B
        foreach ($familyPlanningSideB as $record) {
            $allRecords->push([
                'id' => $record->id,
                'type_of_record' => $record->type_of_record,
                'created_at' => $record->created_at,
                'status' => $record->status,
                'record_type' => 'family_planning_side_b',
                'data' => $record
            ]);
        }

        // Sort the collection
        if ($this->sortDirection === 'asc') {
            $allRecords = $allRecords->sortBy('created_at')->values();
        } else {
            $allRecords = $allRecords->sortByDesc('created_at')->values();
        }

        // Use Livewire's pagination
        $perPage = 15;
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

        $address = patient_addresses::where("patient_id", $patientInfo->patient->id)->first() ?? null;

        return view('livewire.archive.family-planning-case-archive', [
            'isActive' => true,
            'page' => 'ARCHIVED',
            'patientInfo' => $patientInfo,
            'medicalRecordCaseId' => $this->medicalRecordCaseId,
            'address' => $address,
            'allRecords' => $paginatedRecords
        ]);
    }

}
