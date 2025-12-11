<?php

namespace App\Livewire\FamilyPlanning;

use App\Models\medical_record_cases;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RecordsTable extends Component
{
    use WithPagination;
    // for sorting
    public $entries = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'asc';
    // new property for searching
    public $search = '';

    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search'];

    // dont forget this for changes in the show entries
    public function updatingEntries()
    {
        $this->resetPage();
    }
    // dont forget this for refreshing the page every seach
    public function updatingSearch()
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
    public function render()
    {
        $familyPlanning = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'family-planning')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when(Auth::user()->role == 'staff', function ($query) {
                // Add join to vaccination_medical_records to filter by health_worker_id
                $query->join('family_planning_medical_records', 'family_planning_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('family_planning_medical_records.health_worker_id', Auth::id());
            })
            ->orderBy("patients.$this->sortField", $this->sortDirection)
            ->paginate($this->entries);
      
        return view('livewire.family-planning.records-table',
            ['isActive' => true, 'page' => 'RECORD', 'familyPlanningRecords' => $familyPlanning]);
    }
    public function exportPdf()
    {
        return redirect()->route('family-planning.pdf', [
            'search' => $this->search,              // Sends "Maria"
            'sortField' => $this->sortField,        // Sends "full_name"
            'sortDirection' => $this->sortDirection,
            'entries' => $this->entries, // Sends "desc"
        ]);
    }
}
