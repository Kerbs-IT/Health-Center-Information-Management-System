<?php

namespace App\Livewire\SeniorCitizen;

use App\Models\medical_record_cases;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RecordsTable extends Component
{
    use WithPagination;
    // for sorting
    public $entries = 10;
    public $sortField = 'full_name';
    public $sortDirection = 'asc';
    // new property for searching
    public $search = '';

    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search', 'patient_id'];
    protected $paginationTheme = 'bootstrap';
    public $start_date;
    public $end_date;

    public $patient_id = null;

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');
        $this->patient_id = request()->get("patient_id");
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
            ->where('type_of_case', 'senior-citizen')
            ->where('medical_record_cases.status', 'Active')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when($this->patient_id, function ($query) {
                $query->where('patients.id', $this->patient_id);
            })
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('senior_citizen_medical_records', 'senior_citizen_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('senior_citizen_medical_records.health_worker_id', Auth::id());
            })
            ->whereDate('medical_record_cases.date_of_registration', '>=', $this->start_date)
            ->whereDate('medical_record_cases.date_of_registration', '<=', $this->end_date)
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        // Step 2: Calculate checkup status for ALL records
        $allRecords->transform(function ($record) {
            $record->checkup_status_info = $this->calculateCheckupStatus($record);
            return $record;
        });

        // Step 3: Sort ALL records by urgency priority across entire dataset
        $sorted = $allRecords->sortBy(function ($record) {
            return $record->checkup_status_info['sort_priority'] ?? 3;
        })->values();

        // Step 4: Manually paginate the sorted collection
        $currentPage = $this->getPage();
        $perPage     = $this->entries;
        $total       = $sorted->count();

        $seniorCitizenRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $sorted->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.senior-citizen.records-table', [
            'isActive'             => true,
            'page'                 => 'RECORD',
            'seniorCitizenRecords' => $seniorCitizenRecords,
        ]);
    }

    public function exportPdf()
    {
        return redirect()->route('senior-citizen.pdf', [
            'search'        => $this->search,
            'sortField'     => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'startDate'     => $this->start_date,
            'endDate'       => $this->end_date,
            'entries'       => $this->entries,
        ]);
    }

    private function calculateCheckupStatus($medicalRecordCase)
    {
        try {
            $lastCaseRecord = DB::table('senior_citizen_case_records')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->whereNotNull('date_of_comeback')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastCaseRecord) {
                return null;
            }

            $comebackDate = Carbon::parse($lastCaseRecord->date_of_comeback);

            if ($comebackDate->isFuture()) {
                return null;
            }

            $checkupExists = DB::table('senior_citizen_case_records')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->whereDate('created_at', '>=', $comebackDate)
                ->where('id', '!=', $lastCaseRecord->id)
                ->exists();

            if ($checkupExists) {
                return null;
            }

            $isFinal = (bool) $lastCaseRecord->is_final;

            if ($comebackDate->isToday()) {
                return [
                    'status'        => 'due_today',
                    'badge'         => 'Checkup Due Today',
                    'class'         => 'table-success',
                    'badge_class'   => 'badge bg-success',
                    'comeback_date' => $comebackDate->format('M j, Y'),
                    'sort_priority' => 2,
                ];
            } else {
                if ($isFinal) {
                    return null;
                }

                $daysOverdue = (int) $comebackDate->diffInDays(now(), false);

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
        } catch (\Exception $e) {
            return null;
        }
    }
}
