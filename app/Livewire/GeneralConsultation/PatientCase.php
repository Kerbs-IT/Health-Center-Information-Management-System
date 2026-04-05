<?php

namespace App\Livewire\GeneralConsultation;

use App\Models\gc_case_records;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class PatientCase extends Component
{
    use WithPagination;

    #[Locked]
    public $medicalRecordCase;

    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    protected $paginationTheme = 'bootstrap';

    public function mount($medicalRecordCase)
    {
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
    }

    #[On('refreshTable')]
    public function refreshTable()
    {
        // Triggers re-render after add/edit
    }

    #[On('archiveRecord')]
    public function archiveRecord($recordId)
    {
        gc_case_records::where('id', $recordId)->update(['status' => 'Archived']);
    }

    public function exportPdf($caseId)
    {
        return redirect()->route('gc-case.pdf', ['caseId' => $caseId]);
    }

    public function render()
    {
        return view('livewire.general-consultation.patient-case', [
            'gc_case_record' => gc_case_records::where('medical_record_case_id', $this->medicalRecordCase->id)
                ->where('status', 'Active')
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10),
            'medicalRecordId' => $this->medicalRecordCase->id,
        ]);
    }
}
