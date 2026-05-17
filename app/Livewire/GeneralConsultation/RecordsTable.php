<?php

namespace App\Livewire\GeneralConsultation;

use App\Exports\PatientRecordsExport;
use App\Models\brgy_unit;
use App\Models\medical_record_cases;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public $purok = '';
    public $availablePuroks = [];
    public $isHealthWorker = false;

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');
        $this->patient_id = request()->get("patient_id");
        $this->search     = request()->get('search', '');

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
        $allRecords = medical_record_cases::select(
            'medical_record_cases.*',
            'patients.full_name',
            'patients.age',
            'patients.sex',
            'patients.contact_number'
        )
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'general-consultation')
            ->where('medical_record_cases.status', 'Active')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when($this->patient_id, fn($q) => $q->where('patients.id', $this->patient_id))
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('gc_medical_records', 'gc_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('gc_medical_records.health_worker_id', Auth::id());

                // Scope to specific assigned area if selected
                if (!empty($this->purok)) {
                    $query->join('patient_addresses', 'patient_addresses.patient_id', '=', 'patients.id')
                        ->where('patient_addresses.purok', $this->purok);
                } else {
                    // Scope to all their assigned areas
                    $assignedAreaIds = DB::table('staff_area_assignments')
                        ->where('staff_id', Auth::id())
                        ->pluck('area_id');
                    $assignedPuroks = brgy_unit::whereIn('id', $assignedAreaIds)->pluck('brgy_unit');
                    $query->join('patient_addresses', 'patient_addresses.patient_id', '=', 'patients.id')
                        ->whereIn('patient_addresses.purok', $assignedPuroks);
                }
            })
            ->when(Auth::user()->role != 'staff' && !empty($this->purok), function ($query) {
                $query->join('patient_addresses', 'patient_addresses.patient_id', '=', 'patients.id')
                    ->where('patient_addresses.purok', $this->purok);
            })
            ->when(
                empty($this->patient_id),
                fn($q) => $q
                    ->whereDate('medical_record_cases.date_of_registration', '>=', $this->start_date)
                    ->whereDate('medical_record_cases.date_of_registration', '<=', $this->end_date)
            )
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

        return view('livewire.general-consultation.records-table', compact(
            'generalConsultation'
        ));
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
        $rows = medical_record_cases::select(
            'medical_record_cases.*',
            'patients.full_name',
            'patients.age',
            'patients.sex',
            'patients.contact_number'
        )
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'general-consultation')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', 'Active')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('gc_medical_records', 'gc_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('gc_medical_records.health_worker_id', Auth::id());

                if (!empty($this->purok)) {
                    $query->join('patient_addresses', 'patient_addresses.patient_id', '=', 'patients.id')
                        ->where('patient_addresses.purok', $this->purok);
                } else {
                    $assignedAreaIds = DB::table('staff_area_assignments')
                        ->where('staff_id', Auth::id())
                        ->pluck('area_id');
                    $assignedPuroks = brgy_unit::whereIn('id', $assignedAreaIds)->pluck('brgy_unit');
                    $query->join('patient_addresses', 'patient_addresses.patient_id', '=', 'patients.id')
                        ->whereIn('patient_addresses.purok', $assignedPuroks);
                }
            })
            ->when(Auth::user()->role != 'staff' && !empty($this->purok), function ($query) {
                $query->join('patient_addresses', 'patient_addresses.patient_id', '=', 'patients.id')
                    ->where('patient_addresses.purok', $this->purok);
            })
            ->whereDate('medical_record_cases.date_of_registration', '>=', $this->start_date)
            ->whereDate('medical_record_cases.date_of_registration', '<=', $this->end_date)
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        // ... rest unchanged
    }
}
