<?php

namespace App\Livewire\Masterlist;

use App\Exports\WRAMasterlistExport;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\wra_masterlists;
use App\Models\brgy_unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class WRA extends Component
{
    use WithPagination;

    // Pagination
    public $entries = 10;

    // Sorting
    public $sortField = 'name_of_wra';
    public $sortDirection = 'asc';
    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $selectedBrgy = '';
    public $selectedMonth = '';
    public $selectedYear = ''; // CHANGED: Default to empty string for "All Years"
    public $withUnmetNeed = '';
    public $selectedAge = '';
    public $isHealthWorker = false;
    public $availablePuroks = [];

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
        'selectedYear' => ['except' => ''], // CHANGED: Exception to empty string
        'withUnmetNeed' => ['except' => ''],
        'selectedAge' => ['except'=> '']
    ];

    protected $listeners = ['wraMasterlistRefreshTable' => '$refresh'];

    // ADDED: Initialize with current year
    public function mount()
    {
        if (empty($this->selectedYear)) {
            $this->selectedYear = date('Y');
        }

        if (Auth::check() && Auth::user()->role === 'staff') {
            $this->isHealthWorker = true;

            $assignedAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', Auth::id())
                ->pluck('area_id');

            $this->availablePuroks = brgy_unit::whereIn('id', $assignedAreaIds)
                ->orderBy('brgy_unit')
                ->pluck('brgy_unit', 'brgy_unit')
                ->toArray();
        } else {
            $this->availablePuroks = brgy_unit::where('status', 'Active')
                ->orderBy('brgy_unit')
                ->pluck('brgy_unit', 'brgy_unit')
                ->toArray();
        }
    }

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
    public function updatedSelectedAge(){
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
        $this->reset(['search', 'selectedBrgy', 'selectedMonth', 'withUnmetNeed']);
        $this->selectedYear = date('Y'); // CHANGED: Reset to current year instead of empty
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


        if (!empty($this->selectedMonth)) {
            $query->whereMonth('created_at', $this->selectedMonth);
        }

        // CHANGED: Only apply year filter if a specific year is selected
        if (!empty($this->selectedYear) && $this->selectedYear !== '') {
            $query->whereYear('created_at', $this->selectedYear);
        }

        if (!empty($this->withUnmetNeed)) {
            $query->where('wra_with_MFP_unmet_need', $this->withUnmetNeed);
        }

        // for the age filter
        if (!empty($this->selectedAge)) {
            [$min, $max] = explode('-', $this->selectedAge);
            $query->whereBetween('age', [(int)$min, (int)$max]);
        }

        // WITH this:
        if (Auth::user()->role == 'staff') {
            $assignedAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', Auth::id())
                ->pluck('area_id');
            $assignedPuroks = brgy_unit::whereIn('id', $assignedAreaIds)->pluck('brgy_unit');

            if (!empty($this->selectedBrgy)) {
                $query->where('brgy_name', $this->selectedBrgy);
            } else {
                $query->whereIn('brgy_name', $assignedPuroks);
            }
        }else {
            if (!empty($this->selectedBrgy)) {
                $query->where('brgy_name', $this->selectedBrgy);
            }
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $wra_masterList = $query->paginate($this->entries);

        $brgyList = brgy_unit::where('status','Active') -> orderBy('brgy_unit', 'ASC')->get();

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
            'withUnmetNeed' => $this->withUnmetNeed,
            'selectedAge' => $this->selectedAge,
        ];

        $url = route('wra-masterlist.pdf', $params);
        return redirect($url);
    }
    public function exportExcel()
    {
        $query = wra_masterlists::where('status', '!=', 'Archived');

        if (!empty($this->search))        $query->where('name_of_wra', 'like', '%' . $this->search . '%');
        if (!empty($this->selectedMonth)) $query->whereMonth('created_at', $this->selectedMonth);
        if (!empty($this->selectedYear))  $query->whereYear('created_at', $this->selectedYear);
        if (!empty($this->withUnmetNeed)) $query->where('wra_with_MFP_unmet_need', $this->withUnmetNeed);
        if (!empty($this->selectedAge)) {
            [$min, $max] = explode('-', $this->selectedAge);
            $query->whereBetween('age', [(int)$min, (int)$max]);
        }
        if (Auth::user()->role == 'staff') {
            $query->where('health_worker_id', Auth::id());

            if (!empty($this->selectedBrgy)) {
                $query->where('brgy_name', $this->selectedBrgy);
            } else {
                $assignedAreaIds = DB::table('staff_area_assignments')
                    ->where('staff_id', Auth::id())
                    ->pluck('area_id');
                $assignedPuroks = brgy_unit::whereIn('id', $assignedAreaIds)->pluck('brgy_unit');
                $query->whereIn('brgy_name', $assignedPuroks);
            }
        } else {
            if (!empty($this->selectedBrgy)) {
                $query->where('brgy_name', $this->selectedBrgy);
            }
        }

        $rows = $query->orderBy('name_of_wra', 'ASC')->get();

        $midwife = User::where('role','nurse')->first();
        $midwifeName = $midwife?->nurses?->full_name;

        $filters = [
            'search'       => $this->search,
            'selectedBrgy' => $this->selectedBrgy,
            'selectedMonth' => $this->selectedMonth,
            'monthName'    => $this->monthName($this->selectedMonth),
            'selectedYear' => $this->selectedYear,
            'withUnmetNeed' => $this->withUnmetNeed,
            'selectedAge'  => $this->selectedAge,
            'midwifeName'  => $midwifeName,
        ];

        return Excel::download(
            new WRAMasterlistExport($rows, $filters),
            'wra-masterlist-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
