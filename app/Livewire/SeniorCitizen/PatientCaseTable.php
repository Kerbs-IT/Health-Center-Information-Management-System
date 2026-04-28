<?php

namespace App\Livewire\SeniorCitizen;

use App\Models\medical_record_cases;
use App\Models\senior_citizen_case_records;
use Livewire\Component;
use Livewire\WithPagination;

class PatientCaseTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $caseId;
    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    // Tracks whether this case already has a final record
    public bool $hasFinalRecord = false;

    protected $listeners = ['seniorCitizenRefreshTable' => 'refreshTable'];

    public function mount($caseId)
    {
        $this->caseId = $caseId;
        $this->checkFinalRecord();
    }

    private function checkFinalRecord(): void
    {
        $this->hasFinalRecord = senior_citizen_case_records::where('medical_record_case_id', $this->caseId)
            ->where('is_final', true)
            ->exists();
    }

    public function refreshTable(): void
    {
        $this->checkFinalRecord();
        // Livewire re-renders automatically after this
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
        $seniorCaseRecords = senior_citizen_case_records::where('medical_record_case_id', $this->caseId)
            ->where('status', '!=', 'Archived')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $patientRecord = medical_record_cases::with('patient', 'senior_citizen_medical_record')
            ->findOrFail($this->caseId);

        return view('livewire.senior-citizen.patient-case-table', [
            'isActive'        => true,
            'page'            => 'RECORD',
            'seniorCaseRecords' => $seniorCaseRecords,
            'patient_name'    => $patientRecord->patient->full_name,
            'healthWorkerId'  => $patientRecord->senior_citizen_medical_record->health_worker_id,
            'medicalRecordId' => $this->caseId,
            // Passed to blade for the button condition
            'hasFinalRecord'  => $this->hasFinalRecord,
        ]);
    }

    public function exportPdf($caseId)
    {
        return redirect()->route('senior-citizen-case.pdf', [
            'caseId' => $caseId,
        ]);
    }
}
