<?php

namespace App\Livewire\GeneralConsultation;

use App\Exports\PatientRecordsExport;
use App\Models\medical_record_cases;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class RecordsTable extends Component
{
    use WithPagination;
    // for sorting
    public $entries = 10;
    public $sortField = 'full_name';
    public $sortDirection = 'asc';
    // new property for searching
    public $search = '';

    public $patient_id = null;
    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search', 'patient_id'];
    protected $paginationTheme = 'bootstrap';
    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');
        $this->patient_id = request()->get("patient_id");

        $this->search = request()->get('search', '');
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
        $this->resetPage();
    }
    public function clearFilter()
    {
        $this->patient_id = null;
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        // Step 1: Fetch ALL records (no pagination yet)
        $allRecords = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'general-consultation')
            ->where('medical_record_cases.status', 'Active')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when($this->patient_id, function ($query) {
                $query->where('patients.id', $this->patient_id);
            })
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('gc_medical_records', 'gc_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('gc_medical_records.health_worker_id', Auth::id());
            })
            ->when(empty($this->patient_id), function ($query) {
                $query->whereDate('medical_record_cases.date_of_registration', '>=', $this->start_date)
                    ->whereDate('medical_record_cases.date_of_registration', '<=', $this->end_date);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $currentPage = $this->getPage();
        $perPage     = $this->entries;
        $total       = $allRecords->count();

        $generalConsultation = new \Illuminate\Pagination\LengthAwarePaginator(
            $allRecords->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.general-consultation.records-table',compact('generalConsultation'));
    }


    public function exportPdf()
    {
        return redirect()->route('general-consultation.pdf', [
            'search'        => $this->search,
            'sortField'     => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'startDate'     => $this->start_date,
            'endDate'       => $this->end_date,
            'entries'       => $this->entries,
        ]);
    }
    public function exportExcel()
    {
        $rows = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'general-consultation')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', 'Active')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->whereDate('medical_record_cases.date_of_registration', '>=', $this->start_date)
            ->whereDate('medical_record_cases.date_of_registration', '<=', $this->end_date)
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $filters = [
            'search'   => $this->search,
            'dateFrom' => $this->start_date,
            'dateTo'   => $this->end_date,
        ];

        $columns = [
            ['label' => '#',               'key' => fn($r) => $rows->search(fn($i) => $i->id === $r->id) + 1],
            ['label' => 'Full Name',       'key' => 'full_name'],
            ['label' => 'Age',             'key' => 'age'],
            ['label' => 'Sex',             'key' => 'sex'],
            ['label' => 'Contact Number',  'key' => 'contact_number'],
            ['label' => 'Date Registered', 'key' => fn($r) => $r->date_of_registration
                ? \Carbon\Carbon::parse($r->date_of_registration)->format('Y-m-d') : '—'],
        ];

        return Excel::download(
            new PatientRecordsExport($rows, $filters, 'General Consultation Patient Records', $columns),
            'General Consultation-records-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
