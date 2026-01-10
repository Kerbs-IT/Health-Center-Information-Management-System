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
        if (Auth::user()->role == 'staff') {
            $query->where('health_worker_id', Auth::id());
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

        $brgys = brgy_unit::orderBy('brgy_unit', 'ASC')->get();
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
}
