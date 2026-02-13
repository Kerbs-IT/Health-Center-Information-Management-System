<?php

namespace App\Livewire\Archive;

use App\Models\medical_record_cases;
use App\Models\tb_dots_case_records;
use App\Models\tb_dots_check_ups;
use Livewire\Component;
use Livewire\WithPagination;

class TbDotsCaseArchive extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $medicalRecordCaseId;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $section = 'tb-dots'; // For back button

    protected $listeners = ['tbRefreshTable' => '$refresh'];

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
                case 'tb_dots_case':
                    $this->restoreTbDotsCase($recordId);
                    break;
                case 'checkup':
                    $this->restoreCheckup($recordId);
                    break;
            }
        } catch (\Exception $e) {
            $this->dispatch('activationError', message: $e->getMessage());
        }
    }

    private function restoreTbDotsCase($recordId)
    {
        // Check if there's an active TB DOTS case record
        $activeRecord = tb_dots_case_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active TB DOTS case record already exists.');
        }

        $record = tb_dots_case_records::findOrFail($recordId);
        $record->status = 'Active';
        $record->save();

        $this->dispatch('patientActivated');
    }

    private function restoreCheckup($recordId)
    {
        $archivedRecord = tb_dots_check_ups::findOrFail($recordId);

        // Check if there's an active checkup with the same date_of_comeback
        $activeRecord = tb_dots_check_ups::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->where('date_of_comeback', $archivedRecord->date_of_comeback)
            ->exists();

        if ($activeRecord) {
            throw new \Exception('Cannot restore: An active checkup record with the same comeback date already exists.');
        }

        $archivedRecord->status = 'Active';
        $archivedRecord->save();

        $this->dispatch('patientActivated');
    }

    public function render()
    {
        $patientRecord = medical_record_cases::with('patient', 'tb_dots_medical_record')
            ->findOrFail($this->medicalRecordCaseId);

        // Get all ARCHIVED records
        $tbDotsCaseRecords = tb_dots_case_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', 'Archived')
            ->get();

        $checkUpRecords = tb_dots_check_ups::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', 'Archived')
            ->get();

        // Combine all records into one collection
        $allRecords = collect();

        // Add TB DOTS case records
        foreach ($tbDotsCaseRecords as $record) {
            $allRecords->push([
                'id' => $record->id,
                'type_of_record' => $record->type_of_record,
                'created_at' => $record->created_at,
                'status' => $record->status,
                'record_type' => 'tb_dots_case',
                'data' => $record
            ]);
        }

        // Add check-up records
        foreach ($checkUpRecords as $record) {
            $allRecords->push([
                'id' => $record->id,
                'type_of_record' => $record->type_of_record,
                'created_at' => $record->created_at,
                'status' => $record->status,
                'record_type' => 'checkup',
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

        return view('livewire.archive.tb-dots-case-archive', [
            'isActive' => true,
            'page' => 'ARCHIVED',
            'patient_name' => $patientRecord->patient->full_name,
            'healthWorkerId' => $patientRecord->tb_dots_medical_record->health_worker_id,
            'medicalRecordId' => $this->medicalRecordCaseId,
            'patientInfo' => $patientRecord,
            'allRecords' => $paginatedRecords,
        ]);
    }
  
}
