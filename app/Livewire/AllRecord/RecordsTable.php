<?php

namespace App\Livewire\AllRecord;

use App\Models\patients;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RecordsTable extends Component
{
    use WithPagination;
    public $entries = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'asc';
    // new property for searching
    public $search = '';

    public $start_date;
    public $end_date;
    
    protected $paginationTheme = 'bootstrap';

    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search'];

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');
    }

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
    #[On('dateRangeChanged')]
    public function updateDateRange($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->resetPage(); // Reset to first page when filtering
    }


    public function render()
    {
        // get all the active patient list
        $records = patients::with(['medical_record_case' => function ($query){
            $query->where("status", 'Active');
        }])
        ->whereHas('medical_record_case', function($query){
            $query->where('status','Active');
        })
            ->where(
                'full_name',
                'like',
                '%' . $this->search . '%')
            ->where('status', 'Active')
            ->orderBy($this->sortField, $this->sortDirection)
            ->whereDate('patients.created_at', '>=', $this->start_date)
            ->whereDate('patients.created_at', '<=', $this->end_date)
            ->latest()
            ->paginate($this->entries);

        return view('livewire.all-record.records-table', compact('records'));
    }
}
