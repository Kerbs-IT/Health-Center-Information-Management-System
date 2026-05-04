<?php

namespace App\Livewire\Vaccination;

use App\Exports\PatientRecordsExport;
use App\Models\medical_record_cases;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class RecordsTable extends Component
{
    use WithPagination;

    public $entries = 10;
    public $sortField = 'full_name';
    public $sortDirection = 'asc';
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

    public function clearFilter()
    {
        $this->patient_id = null;
        $this->search     = '';
        $this->resetPage();
    }

    #[On('dateRangeChanged')]
    public function updateDateRange($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
        $this->resetPage();
    }

    public function render()
    {
        // Step 1: Fetch ALL records (no pagination yet)
        $allRecords = medical_record_cases::select('medical_record_cases.*')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'vaccination')
            ->where('medical_record_cases.status','Active')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when($this->patient_id, function ($query) {
                $query->where('patients.id', $this->patient_id);
            })
            ->when($this->sortField === 'age', function ($query) {
                $query->orderBy('patients.age', $this->sortDirection)
                    ->orderBy('patients.age_in_months', $this->sortDirection);
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->when(empty($this->patient_id), function ($query) {
                $query->whereDate('medical_record_cases.date_of_registration', '>=', $this->start_date)
                    ->whereDate('medical_record_cases.date_of_registration', '<=', $this->end_date);
            })
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('vaccination_medical_records', 'vaccination_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('vaccination_medical_records.health_worker_id', Auth::id());
            })
            
            ->get();

        // Step 2: Calculate vaccination status for ALL records
        $allRecords->transform(function ($record) {
            $record->vaccination_status_info = $this->calculateVaccinationStatus($record);
            return $record;
        });

        // Step 3: Sort ALL records by urgency priority across entire dataset
        $sorted = $allRecords->sortBy(function ($record) {
            return $record->vaccination_status_info['sort_priority'] ?? 4;
        })->values();

        // Step 4: Manually paginate the sorted collection
        $currentPage = $this->getPage();
        $perPage     = $this->entries;
        $total       = $sorted->count();

        $vaccinationRecord = new \Illuminate\Pagination\LengthAwarePaginator(
            $sorted->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.vaccination.records-table', [
            'vaccinationRecord' => $vaccinationRecord,
        ]);
    }

    public function exportPdf()
    {
        return redirect()->route('vaccination.pdf', [
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
            ->where('type_of_case', 'vaccination')
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
            new PatientRecordsExport($rows, $filters, 'Vaccination Patient Records', $columns),
            'vaccination-records-' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    private function calculateVaccinationStatus($medicalRecordCase)
    {
        try {
            // Load vaccine config dynamically from DB, only Active ones
            $vaccineDoseConfig = DB::table('vaccines')
                ->where('status', 'Active')
                ->get()
                ->keyBy('vaccine_acronym')
                ->map(fn($v) => [
                    'acronym'     => $v->vaccine_acronym,
                    'maxDoses'    => $v->max_doses,
                    'name'        => $v->type_of_vaccine,
                ])
                ->toArray();

            $lastVaccinationCase = DB::table('vaccination_case_records')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->where('vaccination_status', 'completed')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastVaccinationCase) {
                return null;
            }

            $comebackDate = Carbon::parse($lastVaccinationCase->date_of_comeback)->startOfDay();
            $today        = Carbon::today();

            if ($comebackDate->gt($today)) {
                return null;
            }

            $vaccines    = explode(',', $lastVaccinationCase->vaccine_type ?? '');
            $currentDose = $lastVaccinationCase->dose_number;
            $nextDosage  = $currentDose + 1;

            $allVaccinesComplete = true;
            $dueVaccines         = [];
            $vaccineCompleted    = [];

            foreach ($vaccines as $vaccine) {
                $vaccineAcronym = trim($vaccine);

                if (isset($vaccineDoseConfig[$vaccineAcronym])) {
                    $maxDoses    = $vaccineDoseConfig[$vaccineAcronym]['maxDoses'];
                    $vaccineName = $vaccineDoseConfig[$vaccineAcronym]['acronym'];

                    if ($currentDose < $maxDoses) {
                        $allVaccinesComplete = false;

                        $nextDoseExists = DB::table('vaccination_case_records')
                            ->where('medical_record_case_id', $medicalRecordCase->id)
                            ->where('vaccine_type', 'LIKE', '%' . $vaccineAcronym . '%')
                            ->where('status', '!=', 'Archived')
                            ->where('dose_number', $nextDosage)
                            ->exists();

                        if (!$nextDoseExists) {
                            $dueVaccines[] = $vaccineName . ' Dose ' . $nextDosage;
                        }
                    } else {
                        $vaccineCompleted[] = $vaccine;
                    }
                }
            }

            if ($allVaccinesComplete && !empty($vaccineCompleted)) {
                $implodedVaccineCompleted = implode(', ', $vaccineCompleted);
                return [
                    'status'        => 'complete',
                    'badge'         => 'Vaccination Complete',
                    'class'         => 'table-light',
                    'badge_class'   => 'badge bg-success',
                    'due_vaccines'  => ["$implodedVaccineCompleted doses is completed. Proceed to another vaccination if needed."],
                    'sort_priority' => 3,
                ];
            }

            if (empty($dueVaccines)) {
                return null;
            }

            if ($comebackDate->eq($today)) {
                return [
                    'status'        => 'due_today',
                    'badge'         => 'Due Today',
                    'class'         => 'table-success',
                    'badge_class'   => 'badge bg-success',
                    'due_vaccines'  => $dueVaccines,
                    'next_dosage'   => $nextDosage,
                    'sort_priority' => 2,
                ];
            }

            $daysOverdue = (int) $today->diffInDays($comebackDate);

            return [
                'status'        => 'overdue',
                'badge'         => $daysOverdue . ($daysOverdue == 1 ? ' day' : ' days') . ' overdue',
                'class'         => 'table-danger',
                'badge_class'   => 'badge bg-danger',
                'due_vaccines'  => $dueVaccines,
                'next_dosage'   => $nextDosage,
                'days_overdue'  => $daysOverdue,
                'sort_priority' => 1,
            ];
        } catch (\Exception $e) {
            Log::error('Vaccination status calculation error: ' . $e->getMessage());
            return null;
        }
    }
}
