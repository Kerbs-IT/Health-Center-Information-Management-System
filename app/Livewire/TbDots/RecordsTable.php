<?php

namespace App\Livewire\TbDots;

use App\Exports\PatientRecordsExport;
use App\Models\brgy_unit;
use App\Models\medical_record_cases;
use Illuminate\Support\Carbon;
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
        $this->patient_id = request()->get('patient_id');
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
    public function updatingPurok()
    {
        $this->resetPage();
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
        $allRecords = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'tb-dots')
            ->where('medical_record_cases.status', 'Active')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when($this->patient_id, function ($query) {
                $query->where('patients.id', $this->patient_id);
            })
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('tb_dots_medical_records', 'tb_dots_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('tb_dots_medical_records.health_worker_id', Auth::id());

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
            ->when(empty($this->patient_id), function ($query) {
                $query->whereDate('medical_record_cases.date_of_registration', '>=', $this->start_date)
                    ->whereDate('medical_record_cases.date_of_registration', '<=', $this->end_date);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $allRecords->transform(function ($record) {
            $record->checkup_status_info = $this->calculateCheckupStatus($record);
            return $record;
        });

        $sorted = $allRecords->sortBy(function ($record) {
            return $record->checkup_status_info['sort_priority'] ?? 3;
        })->values();

        $currentPage = $this->getPage();
        $perPage     = $this->entries;
        $total       = $sorted->count();

        $tbRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $sorted->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view(
            'livewire.tb-dots.records-table',
            ['isActive' => true, 'page' => 'RECORD', 'tbRecords' => $tbRecords]
        );
    }

    public function exportPdf()
    {
        return redirect()->route('tb-dots.pdf', [
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
            ->where('type_of_case', 'tb-dots')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', 'Active')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('tb_dots_medical_records', 'tb_dots_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('tb_dots_medical_records.health_worker_id', Auth::id());

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
            new PatientRecordsExport($rows, $filters, 'Tb-dots Patient Records', $columns),
            'tb-dots-records-' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    private function calculateCheckupStatus($medicalRecordCase)
    {
        try {
            $hasFinalCheckup = DB::table('tb_dots_check_ups')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->where('is_final', true)
                ->exists();

            if ($hasFinalCheckup) {
                return null;
            }

            $lastCheckup = DB::table('tb_dots_check_ups')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->whereNotNull('date_of_comeback')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastCheckup) {
                return null;
            }

            try {
                $comebackDate = Carbon::parse($lastCheckup->date_of_comeback)->startOfDay();
            } catch (\Exception $e) {
                return null;
            }

            $today = Carbon::today();

            if ($comebackDate->eq($today)) {
                return [
                    'status'        => 'due_today',
                    'badge'         => 'Checkup Due Today',
                    'class'         => 'table-success',
                    'badge_class'   => 'badge bg-success',
                    'comeback_date' => $comebackDate->format('M j, Y'),
                    'sort_priority' => 2,
                ];
            }

            if ($comebackDate->lt($today)) {
                $daysOverdue = (int) $today->diffInDays($comebackDate);

                return [
                    'status'        => 'overdue',
                    'badge'         => $daysOverdue . ($daysOverdue == 1 ? ' day' : ' days') . ' overdue',
                    'class'         => 'table-danger',
                    'badge_class'   => 'badge bg-danger',
                    'comeback_date' => $comebackDate->format('M j, Y'),
                    'days_overdue'  => $daysOverdue,
                    'sort_priority' => 1,
                ];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
