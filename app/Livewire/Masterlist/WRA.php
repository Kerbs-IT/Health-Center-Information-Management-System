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
    public $withUnmetNeed = '';

    // ADDED: Force re-render on filter changes
    public $refreshKey = 0;

    // Query string parameters
    protected $queryString = [
        'entries' => ['except' => 10],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'asc'],
        'search' => ['except' => ''],
        'selectedBrgy' => ['except' => ''],
        'selectedMonth' => ['except' => ''],
        'selectedYear' => ['except' => '2025'],
        'withUnmetNeed' => ['except' => '']
    ];

    protected $listeners = ['wraMasterlistRefreshTable' => '$refresh'];

    // CHANGED: From updatingXXX to updatedXXX
    public function updatedEntries()
    {
        $this->resetPage();
        $this->refreshKey++;
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->refreshKey++;
    }

    public function updatedSelectedBrgy()
    {
        $this->resetPage();
        $this->refreshKey++;
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
        $this->refreshKey++;
    }

    public function updatedSelectedYear()
    {
        $this->resetPage();
        $this->refreshKey++;
    }

    public function updatedWithUnmetNeed()
    {
        $this->resetPage();
        $this->refreshKey++;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->refreshKey++;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedBrgy', 'selectedMonth', 'selectedYear', 'withUnmetNeed']);
        $this->resetPage();
        $this->refreshKey++;
    }

    public function monthName($monthNumber)
    {
        $monthNumber = intval($monthNumber);

        if ($monthNumber < 1 || $monthNumber > 12) {
            return null;
        }

        return date("F", mktime(0, 0, 0, $monthNumber, 1));
    }

    public function render()
    {
        $query = wra_masterlists::where('status', '!=', 'Archived');

        if (!empty($this->search)) {
            $query->where('name_of_wra', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->selectedBrgy)) {
            $query->where('brgy_name', $this->selectedBrgy);
        }

        if (!empty($this->selectedMonth)) {
            $query->whereMonth('created_at', $this->selectedMonth);
        }

        if (!empty($this->selectedYear)) {
            $query->whereYear('created_at', $this->selectedYear);
        }

        if (!empty($this->withUnmetNeed)) {
            $query->where('wra_with_MFP_unmet_need', $this->withUnmetNeed);
        }

        if (Auth::user()->role == 'staff') {
            $query->where('health_worker_id', Auth::id());
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $wra_masterList = $query->latest()->paginate($this->entries);

        $brgyList = brgy_unit::orderBy('brgy_unit', 'ASC')->get();

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

    public function exportPdf()
    {
        $params = [
            'search' => $this->search,
            'selectedBrgy' => $this->selectedBrgy,
            'selectedMonth' => $this->selectedMonth,
            'monthName' => $this->monthName($this->selectedMonth),
            'selectedYear' => $this->selectedYear,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'entries' => $this->entries,
            'withUnmetNeed' => $this->withUnmetNeed
        ];

        $url = route('wra-masterlist.pdf', $params);
        return redirect($url);
    }
}
