<?php

namespace App\Livewire\TbDots;

use App\Models\medical_record_cases;
use App\Models\tb_dots_case_records;
use App\Models\tb_dots_check_ups;
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
    protected $listeners = ['tbRefreshTable' => '$refresh'];
    protected $paginationTheme = 'bootstrap';
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
        $patientRecord = medical_record_cases::with('patient', 'tb_dots_medical_record')
            ->where('status', '!=', 'Archived')
            ->findOrFail($this->medicalRecordCaseId);

        // Get all records without pagination first
        $tbDotsCaseRecords = tb_dots_case_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->get();

        $checkUpRecords = tb_dots_check_ups::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
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
            $allRecords = $allRecords->sortBy('created_at');
        } else {
            $allRecords = $allRecords->sortByDesc('created_at');
        }

        // Manual pagination
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $paginatedRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $allRecords->forPage($currentPage, $perPage),
            $allRecords->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.tb-dots.patient-case-table', [
            'isActive' => true,
            'page' => 'RECORD',
            'patient_name' => $patientRecord->patient->full_name,
            'healthWorkerId' => $patientRecord->tb_dots_medical_record->health_worker_id,
            'medicalRecordId' => $this->medicalRecordCaseId,
            'patientInfo' => $patientRecord,
            'allRecords' => $paginatedRecords,
        ]);
    }
    public function exportPdf($caseId)
    {
        return redirect()->route('tb-dots-case.pdf', [
            'caseId' => $caseId,              // Sends "Maria"
        ]);
    }
    public function exportCheckUpPdf($caseId)
    {
        return redirect()->route('tb-dots-checkup.pdf', [
            'checkupId' => $caseId,              // Sends "Maria"
        ]);
    }
}
