<?php

namespace App\Livewire\Masterlist;

use App\Models\brgy_unit;
use App\Models\vaccination_masterlists;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Vaccination extends Component
{
    use WithPagination;

    // Pagination
    public $entries = 10;

    // Sorting
    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    // Filters
    public $search = '';
    public $selectedBrgy = '';
    public $ageRange = '0-4';
    public $filterMonth = '';
    public $filterYear = '';
    public $selectedRange = '0-59 Months';

    protected $queryString = [
        'entries' => ['except' => 10],
        'search' => ['except' => ''],
        'ageRange' => ['except' => ''],
        'selectedBrgy' => ['except' => ''],
        'filterMonth' => ['except' => ''],
        'filterYear' => ['except' => '']
    ];

    protected $listeners = ['vaccinationMasterlistRefreshTable' => '$refresh'];
    // for wra masterlist
       

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAgeRange()
    {
        $this->resetPage();
    }

    public function updatingSelectedBrgy()
    {
        $this->resetPage();
    }

    public function updatingFilterMonth()
    {
        $this->resetPage();
    }

    public function updatingFilterYear()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->ageRange = '0-4';
        $this->selectedBrgy = '';
        $this->filterMonth = '';
        $this->filterYear = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = vaccination_masterlists::where('status', '!=', 'Archived');

        // Search filter
        if (!empty($this->search)) {
            $query->where('name_of_child', 'like', '%' . $this->search . '%');
        }

        // Barangay filter
        if (!empty($this->selectedBrgy)) {
            $query->where('brgy_name', $this->selectedBrgy);
        }

        // Month filter
        if (!empty($this->filterMonth)) {
            $query->whereMonth('created_at', $this->filterMonth);
        }

        // Year filter
        if (!empty($this->filterYear)) {
            $query->whereYear('created_at', $this->filterYear);
        }
        // if the user is health worker
        if(Auth::user()-> role == 'staff'){
            $query->where('health_worker_id',Auth::id());
        }
        // Age range filter
        if (!empty($this->ageRange)) {


            switch ($this->ageRange) {
                case '0-4': // 0-59 months (0-4 years)
                    $query->whereBetween('age', [0, 4]);
                    $this->selectedRange = '0-59 Months';
                    break;

                case '5-9': // 5-9 years old
                    $query->whereBetween('age', [5, 9]);
                    $this->selectedRange= '5-9 years old';
                    break;

                case '10-14': // 10-14 years old
                    $query->whereBetween('age', [10, 14]);
                    $this->selectedRange= '10-14 years old';
                    break;

                case '15-49': // 15-49 years old
                    $query->whereBetween('age', [15, 49]);
                    $this->selectedRange = '15-49 years old';
                    break;
            }
        }
        $vaccination_masterlist = $query->orderBy('name_of_child', 'ASC')
            ->paginate($this->entries);

        $brgys = brgy_unit::orderBy('brgy_unit','ASC')->get();

        // Generate year options (last 10 years)
        $years = range(date('Y'), date('Y') - 10);

        return view('livewire.masterlist.vaccination', [
            'isActive' => true,
            'page' => 'VACCINATION',
            'pageHeader' => 'MASTERLIST',
            'vaccinationMasterlist' => $vaccination_masterlist,
            'brgys' => $brgys,
            'years' => $years,
            'selectedRange' => $this->selectedRange
        ]);
    }
}
