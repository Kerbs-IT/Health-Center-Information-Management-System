<?php

namespace App\Livewire\Masterlist;

use App\Exports\PatientRecordsExport;
use App\Models\brgy_unit;
use App\Models\vaccination_masterlists;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

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
    protected $paginationTheme = 'bootstrap';
    public $isHealthWorker = false;
    public $availablePuroks = [];

    // ADDED: Force component re-render on filter changes
    public $refreshKey = 0;

    protected $queryString = [
        'entries' => ['except' => 10],
        'search' => ['except' => ''],
        'ageRange' => ['except' => '0-4'],
        'selectedBrgy' => ['except' => ''],
        'filterMonth' => ['except' => ''],
        'filterYear' => ['except' => '']
    ];

    protected $listeners = ['vaccinationMasterlistRefreshTable' => '$refresh'];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->refreshKey++; // ADDED: Force re-render
    }

    public function updatedAgeRange()
    {
        $this->updateSelectedRange();
        $this->resetPage();
        $this->refreshKey++; // ADDED: Force re-render
    }

    public function updatedSelectedBrgy()
    {
        $this->resetPage();
        $this->refreshKey++; // ADDED: Force re-render
    }

    public function updatedFilterMonth()
    {
        $this->resetPage();
        $this->refreshKey++; // ADDED: Force re-render
    }

    public function updatedFilterYear()
    {
        $this->resetPage();
        $this->refreshKey++; // ADDED: Force re-render
    }

    public function updatedEntries()
    {
        $this->resetPage();
        $this->refreshKey++; // ADDED: Force re-render
    }

    public function mount()
    {
        $this->updateSelectedRange();

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

    private function updateSelectedRange()
    {
        switch ($this->ageRange) {
            case '0-4':
                $this->selectedRange = '0-59 Months';
                break;
            case '5-9':
                $this->selectedRange = '5-9 years old';
                break;
            case '10-14':
                $this->selectedRange = '10-14 years old';
                break;
            case '15-49':
                $this->selectedRange = '15-49 years old';
                break;
            default:
                $this->selectedRange = 'All Ages';
                break;
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->ageRange = '0-4';
        $this->selectedBrgy = '';
        $this->filterMonth = '';
        $this->filterYear = '';
        $this->updateSelectedRange();
        $this->resetPage();
        $this->refreshKey++; // ADDED: Force re-render
    }

    public function render()
    {
        $query = vaccination_masterlists::where('status', '!=', 'Archived');

        // Search filter
        if (!empty($this->search)) {
            $query->where('name_of_child', 'like', '%' . $this->search . '%');
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
        // for staff: scope to assigned areas
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
            // for nurse/admin: optional brgy filter
            if (!empty($this->selectedBrgy)) {
                $query->where('brgy_name', $this->selectedBrgy);
            }
        }

        // Age range filter
        if (!empty($this->ageRange)) {
            switch ($this->ageRange) {
                case '0-4':
                    $query->whereBetween('age', [0, 4]);
                    break;
                case '5-9':
                    $query->whereBetween('age', [5, 9]);
                    break;
                case '10-14':
                    $query->whereBetween('age', [10, 14]);
                    break;
                case '15-49':
                    $query->whereBetween('age', [15, 49]);
                    break;
            }
        }

        $vaccination_masterlist = $query->orderBy('name_of_child', 'ASC')
            ->paginate($this->entries);

        $brgys = brgy_unit::orderBy('brgy_unit', 'ASC')->where('status','Active')->get();
        $years = range(date('Y'), date('Y') - 10);

        return view('livewire.masterlist.vaccination', [
            'isActive' => true,
            'page' => 'VACCINATION',
            'pageHeader' => 'MASTERLIST',
            'vaccinationMasterlist' => $vaccination_masterlist,
            'brgys' => $brgys,
            'years' => $years,
        ]);
    }

    public function exportPdf()
    {
        $params = [
            'search' => $this->search,
            'selectedBrgy' => $this->selectedBrgy,
            'ageRange' => $this->ageRange,
            'filterMonth' => $this->filterMonth,
            'filterYear' => $this->filterYear,
            'selectedRange' => $this->selectedRange,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'entries' => $this->entries
        ];

        $url = route('vaccination-masterlist.pdf', $params);
        return redirect($url);
    }

    public function exportExcel()
    {
        $query = vaccination_masterlists::where('status', '!=', 'Archived');

        if (!empty($this->search))
            $query->where('name_of_child', 'like', '%' . $this->search . '%');

        if (!empty($this->selectedBrgy))
            $query->where('brgy_name', $this->selectedBrgy);

        if (!empty($this->filterMonth))
            $query->whereMonth('created_at', $this->filterMonth);

        if (!empty($this->filterYear))
            $query->whereYear('created_at', $this->filterYear);

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
        if (!empty($this->ageRange)) {
            switch ($this->ageRange) {
                case '0-4':
                    $query->whereBetween('age', [0, 4]);
                    break;
                case '5-9':
                    $query->whereBetween('age', [5, 9]);
                    break;
                case '10-14':
                    $query->whereBetween('age', [10, 14]);
                    break;
                case '15-49':
                    $query->whereBetween('age', [15, 49]);
                    break;
            }
        }

        $rows = $query->orderBy('name_of_child', 'ASC')->get();

        $filters = [
            'search'   => $this->search,
            'dateFrom' => $this->filterMonth && $this->filterYear
                ? $this->filterMonth . '/' . $this->filterYear
                : ($this->filterYear ?: ''),
            'dateTo'   => '',
            'purok'    => $this->selectedBrgy ?: 'all',
            'type'     => 'Vaccination - ' . $this->selectedRange,
        ];

        $columns = [
            ['label' => '#',            'key' => fn($r) => $rows->search(fn($i) => $i->id === $r->id) + 1],
            ['label' => 'Name of Child', 'key' => 'name_of_child'],
            ['label' => 'Address',      'key' => 'Address'],
            ['label' => 'Sex',          'key' => 'sex'],
            ['label' => 'Age',          'key' => fn($r) => $r->age_display ?? $r->age],
            ['label' => 'Date of Birth', 'key' => fn($r) => $r->date_of_birth
                ? \Carbon\Carbon::parse($r->date_of_birth)->format('Y-m-d') : '—'],
            ['label' => 'SE Status',    'key' => 'SE_status'],
            ['label' => 'BCG',          'key' => 'BCG'],
            ['label' => 'Hepa B',       'key' => fn($r) => $r->{'Hepatitis B'} ?? ''],
            ['label' => 'PENTA 1',      'key' => 'PENTA_1'],
            ['label' => 'PENTA 2',      'key' => 'PENTA_2'],
            ['label' => 'PENTA 3',      'key' => 'PENTA_3'],
            ['label' => 'OPV 1',        'key' => 'OPV_1'],
            ['label' => 'OPV 2',        'key' => 'OPV_2'],
            ['label' => 'OPV 3',        'key' => 'OPV_3'],
            ['label' => 'PCV 1',        'key' => 'PCV_1'],
            ['label' => 'PCV 2',        'key' => 'PCV_2'],
            ['label' => 'PCV 3',        'key' => 'PCV_3'],
            ['label' => 'IPV 1',        'key' => 'IPV_1'],
            ['label' => 'IPV 2',        'key' => 'IPV_2'],
            ['label' => 'MCV 1',        'key' => 'MCV_1'],
            ['label' => 'MCV 2',        'key' => 'MCV_2'],
            ['label' => 'Remarks',      'key' => 'remarks'],
        ];

        return Excel::download(
            new PatientRecordsExport($rows, $filters, 'Master List of ' . $this->selectedRange, $columns),
            'vaccination-masterlist-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
