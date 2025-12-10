<?php

namespace App\Livewire\Masterlist;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\wra_masterlists;
use App\Models\brgy_unit;
use Illuminate\Support\Facades\Auth;

class WRA extends Component
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
    public $selectedMonth = '';
    public $selectedYear = '2025';
    // with unmet need, those who active that needs family planning
    public $withUnmetNeed = '';

    // Query string parameters
    protected $queryString = [
        'entries' => ['except' => 10],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'search' => ['except' => ''],
        'selectedBrgy' => ['except' => ''],
        'selectedMonth' => ['except' => ''],
        'selectedYear' => ['except' => ''],
        'withUnmetNeed' => ['except' => '']
    ];

    protected $listeners = ['wraMasterlistRefreshTable' => '$refresh'];

    /**
     * Reset pagination when entries changes
     */
    public function updatingEntries()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingSelectedBrgy()
    {
        $this->resetPage();
    }

    public function updatingSelectedMonth()
    {
        $this->resetPage();
    }

    public function updatingSelectedYear()
    {
        $this->resetPage();
    }

    public function updatingWithUnmetNeed(){
        $this->resetPage();
    }

    /**
     * Sort by field
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->reset(['search', 'selectedBrgy', 'selectedMonth', 'selectedYear', 'withUnmetNeed']);
        $this->resetPage();
    }

    public function monthName($monthNumber)
    {
        // Ensure it's always between 1–12
        $monthNumber = intval($monthNumber);

        if ($monthNumber < 1 || $monthNumber > 12) {
            return null; // or return "Invalid month"
        }

        return date("F", mktime(0, 0, 0, $monthNumber, 1));
    }


    public function render()
    {
        // Build query
        $query = wra_masterlists::where('status', '!=', 'Archived');

        // Search by name
        if (!empty($this->search)) {
            $query->where('name_of_wra', 'like', '%' . $this->search . '%');
        }

        // Filter by barangay
        if (!empty($this->selectedBrgy)) {
            $query->where('brgy_name', $this->selectedBrgy);
        }

        // Filter by month
        if (!empty($this->selectedMonth)) {
            $query->whereMonth('created_at', $this->selectedMonth);
        }

        // Filter by year
        if (!empty($this->selectedYear)) {
            $query->whereYear('created_at', $this->selectedYear);
        }
        if(!empty($this->withUnmetNeed)){
            $query->where('wra_with_MFP_unmet_need',$this->withUnmetNeed);
        }

        if (Auth::user()->role == 'staff') {
            $query->where('health_worker_id', Auth::id());
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        // Paginate
        $wra_masterList = $query
        ->latest()
        ->paginate($this->entries);

        // Get barangay list for dropdown
        $brgyList = brgy_unit::orderBy('brgy_unit', 'ASC')->get();

        // Get available years from data
        $availableYears = wra_masterlists::selectRaw('YEAR(created_at) as year')
            ->where('status', '!=', 'Archived')
            ->distinct()
            ->orderBy('year', 'DESC')
            ->pluck('year');

        return view('livewire.masterlist.w-r-a', [
            'isActive' => true,
            'page' => 'WOMEN OF REPRODUCTIVE AGE',
            'pageHeader' => 'MASTERLIST',
            'masterlistRecords' => $wra_masterList,
            'brgyList' => $brgyList,
            'availableYears' => $availableYears,
        ]);
    }
}
