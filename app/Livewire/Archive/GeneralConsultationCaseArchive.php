<?php

namespace App\Livewire\Archive;

use App\Models\gc_case_records;
use Livewire\Component;
use Livewire\WithPagination;

class GeneralConsultationCaseArchive extends Component
{
    use WithPagination;

    public $entries = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'asc';
    public $medical_record_id;

    protected $queryString = ['entries', 'sortField', 'sortDirection', 'medical_record_id'];
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->medical_record_id = request('medical_record_id');
    }

    public function updatingEntries()
    {
        $this->resetPage();
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

    public function activateGcRecord($recordId)
    {
        $record = gc_case_records::findOrFail($recordId);

        if ($record->status !== 'Archived') {
            $this->dispatch('activationError', message: 'Record is not archived and cannot be activated.');
            return;
        }

        // Duplicate check — same date_of_consultation, same medical_record_case_id, not archived
        $duplicate = gc_case_records::where('medical_record_case_id', $record->medical_record_case_id)
            ->where('date_of_consultation', $record->date_of_consultation)
            ->where('status', '!=', 'Archived')
            ->where('id', '!=', $recordId)
            ->first();

        if ($duplicate) {
            $this->dispatch('activationError', message: 'Cannot activate record. A consultation record for this date already exists in active records.');
            return;
        }

        $record->update(['status' => 'Active']);

        $this->dispatch('patientActivated');
    }

    public function render()
    {
        $records = gc_case_records::where('medical_record_case_id', $this->medical_record_id)
            ->where('status', 'Archived')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->entries);

        return view('livewire.archive.general-consultation-case-archive', compact('records'));
    }
   
}
