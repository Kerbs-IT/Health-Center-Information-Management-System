<?php

namespace App\Livewire\Archive;

use App\Models\medical_record_cases;
use App\Models\senior_citizen_case_records;
use Livewire\Component;
use Livewire\WithPagination;

class SeniorCitizenCaseArchive extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $caseId;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $section = 'senior-citizen'; // For back button

    protected $listeners = ['seniorCitizenRefreshTable' => '$refresh'];

    public function mount($caseId = null)
    {
        // Get caseId from parameter or from URL
        $this->caseId = $caseId ?? request()->get('medical_record_id');

        // Validate that the case exists
        if (!$this->caseId || !medical_record_cases::find($this->caseId)) {
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

    public function restoreRecord($recordId)
    {
        try {
            $archivedRecord = senior_citizen_case_records::findOrFail($recordId);
            // Check if there's an active senior citizen case record
            $activeRecord = senior_citizen_case_records::where('medical_record_case_id', $this->caseId)
                ->where('status', '!=', 'Archived')
                ->where('date_of_comeback', $archivedRecord->date_of_comeback)
                ->exists();

            if ($activeRecord) {
                throw new \Exception('Cannot restore: An active senior citizen case record already exists.');
            }

            $record = senior_citizen_case_records::findOrFail($recordId);
            $record->status = 'Active';
            $record->save();

            $this->dispatch('patientActivated');
        } catch (\Exception $e) {
            $this->dispatch('activationError', message: $e->getMessage());
        }
    }

    public function render()
    {
        // Get all ARCHIVED records
        $seniorCaseRecords = senior_citizen_case_records::where('medical_record_case_id', $this->caseId)
            ->where('status', 'Archived')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $patientRecord = medical_record_cases::with('patient', 'senior_citizen_medical_record')
            ->findOrFail($this->caseId);

        return view('livewire.archive.senior-citizen-case-archive', [
            'isActive' => true,
            'page' => 'ARCHIVED',
            'seniorCaseRecords' => $seniorCaseRecords,
            'patient_name' => $patientRecord->patient->full_name,
            'healthWorkerId' => $patientRecord->senior_citizen_medical_record->health_worker_id,
            'medicalRecordId' => $this->caseId,
            'caseId' => $this->caseId,
        ]);
    }

    public function exportPdf($caseId)
    {
        return redirect()->route('senior-citizen-case.pdf', [
            'caseId' => $caseId,
        ]);
    }
   
}
