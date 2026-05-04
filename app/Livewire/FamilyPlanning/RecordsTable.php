<?php

namespace App\Livewire\FamilyPlanning;

use App\Exports\PatientRecordsExport;
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

    // for redirect to specific page
    public $patient_id = null;

    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search', 'patient_id'];
    protected $paginationTheme = 'bootstrap';
    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');
        $this->patient_id = request()->get('patient_id');
        $this->search     = request()->get('search', '');
    }

    public function updatingEntries()
    {
        $this->resetPage();
    }

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
        $this->end_date   = $end_date;
        $this->resetPage();
    }

    public function clearFilter()
    {
        $this->patient_id = null;
        $this->search     = '';
        $this->resetPage();
    }

    public function render()
    {
        // Step 1: Fetch ALL records (no pagination yet)
        $allRecords = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'family-planning')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', 'Active')
            ->when($this->patient_id, function ($query) {
                $query->where('patients.id', $this->patient_id);
            })
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('family_planning_medical_records', 'family_planning_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('family_planning_medical_records.health_worker_id', Auth::id());
            })
            ->when(empty($this->patient_id), function ($query) {
                $query->whereDate('medical_record_cases.date_of_registration', '>=', $this->start_date)
                    ->whereDate('medical_record_cases.date_of_registration', '<=', $this->end_date);
            })
            ->orderBy("patients.$this->sortField", $this->sortDirection)
            ->get();

        // Step 2: Calculate follow-up status for ALL records
        $allRecords->transform(function ($record) {
            $record->followup_status_info = $this->calculateFollowUpStatus($record);
            return $record;
        });

        // Step 3: Sort ALL records by urgency priority across entire dataset
        $sorted = $allRecords->sortBy(function ($record) {
            return $record->followup_status_info['sort_priority'] ?? 3;
        })->values();

        // Step 4: Manually paginate the sorted collection
        $currentPage = $this->getPage();
        $perPage     = $this->entries;
        $total       = $sorted->count();

        $familyPlanning = new \Illuminate\Pagination\LengthAwarePaginator(
            $sorted->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.family-planning.records-table', [
            'isActive'              => true,
            'page'                  => 'RECORD',
            'familyPlanningRecords' => $familyPlanning,
        ]);
    }

    public function exportPdf()
    {
        return redirect()->route('family-planning.pdf', [
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
            ->where('type_of_case', 'family-planning')
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
            new PatientRecordsExport($rows, $filters, 'Family Planning Patient Records', $columns),
            'family-planning-records-' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    private function calculateFollowUpStatus($medicalRecordCase)
    {
        try {
            $lastRecord = DB::table('family_planning_side_b_records')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->whereNotNull('date_of_follow_up_visit')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastRecord) {
                return null;
            }

            // If the last record is final, no follow-up is needed
            if ((bool) $lastRecord->is_final) {
                return null;
            }

            $followUpDate = Carbon::parse($lastRecord->date_of_follow_up_visit)->startOfDay();
            $today = Carbon::today();

            if ($followUpDate->eq($today)) {
                return [
                    'status'        => 'due_today',
                    'badge'         => 'Follow-up Due Today',
                    'class'         => 'table-success',
                    'badge_class'   => 'badge bg-success',
                    'followup_date' => $followUpDate->format('M j, Y'),
                    'sort_priority' => 2,
                ];
            }

            if ($followUpDate->lt($today)) {
                $daysOverdue = (int) $followUpDate->diffInDays($today);

                return [
                    'status'        => 'overdue',
                    'badge'         => $daysOverdue . ($daysOverdue == 1 ? ' day' : ' days') . ' overdue',
                    'class'         => 'table-danger',
                    'badge_class'   => 'badge bg-danger',
                    'followup_date' => $followUpDate->format('M j, Y'),
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
