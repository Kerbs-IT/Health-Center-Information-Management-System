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
        $tbDotsCaseRecords = tb_dots_case_records::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->where('status', '!=', 'Archived')
            ->get();

        $patientRecord = medical_record_cases::with('patient', 'tb_dots_medical_record')
            ->where('status', '!=', 'Archived')
            ->findOrFail($this->medicalRecordCaseId);

        // check up 

        $checkUpRecords = tb_dots_check_ups::where('medical_record_case_id', $this->medicalRecordCaseId)
            ->orderBy($this->sortField, $this->sortDirection)
            ->latest()
            ->get();
        return view('livewire.tb-dots.patient-case-table', [
            'isActive' => true,
            'page' => 'RECORD',
            'tbDotsRecords' =>  $tbDotsCaseRecords,
            'checkUpRecords' => $checkUpRecords,
            'patient_name' => $patientRecord->patient->full_name,
            'healthWorkerId' => $patientRecord->tb_dots_medical_record->health_worker_id,
            'medicalRecordId' => $this->medicalRecordCaseId,
            'patientInfo' => $patientRecord
        ]);

    }
}
