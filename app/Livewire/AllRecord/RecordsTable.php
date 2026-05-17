<?php

namespace App\Livewire\AllRecord;

use App\Exports\PatientRecordsExport;
use App\Models\patients;
use App\Models\staff;
use App\Models\brgy_unit;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class RecordsTable extends Component
{
    use WithPagination;

    public $entries = 10;
    public $sortField = 'full_name';
    public $sortDirection = 'asc';
    public $search = '';
    public $start_date;
    public $end_date;

    // New filter properties
    public $type_of_patient = '';
    public $purok = '';

    // For health worker
    public $isHealthWorker = false;
    public $assignedPurok = null;
    public $availablePuroks = [];

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'entries',
        'sortField',
        'sortDirection',
        'search',
        'type_of_patient',
        'purok'
    ];

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');

        if (Auth::check() && Auth::user()->role === 'staff') {
            $this->isHealthWorker = true;

            $assignedAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', Auth::id())
                ->pluck('area_id');

            $assignedAreas = brgy_unit::whereIn('id', $assignedAreaIds)
                ->orderBy('brgy_unit')
                ->get();

            if ($assignedAreas->isNotEmpty()) {
                $this->availablePuroks = $assignedAreas->pluck('brgy_unit', 'brgy_unit')->toArray();
                // Don't auto-lock to one purok — let them filter within their areas
                $this->purok = '';
            }
        } else {
            $this->availablePuroks = brgy_unit::where('status', 'Active')
                ->orderBy('brgy_unit')
                ->pluck('brgy_unit', 'brgy_unit')
                ->toArray();
        }
    }

    public function updatingEntries()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeOfPatient()
    {
        $this->resetPage();
    }

    public function updatingPurok()
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

    private function getQuery()
    {
        $query = patients::with(['medical_record_case' => function ($q) {
            $q->where('status', 'Active');

            if (!empty($this->type_of_patient)) {
                $q->where('type_of_case', $this->type_of_patient);
            }
        }, 'address'])
            ->whereHas('medical_record_case', function ($q) {
                $q->where('status', 'Active');

                if (!empty($this->type_of_patient)) {
                    $q->where('type_of_case', $this->type_of_patient);
                }
            })
            ->where('full_name', 'like', '%' . $this->search . '%')
            ->where('status', 'Active')
            ->whereDate('patients.created_at', '>=', $this->start_date)
            ->whereDate('patients.created_at', '<=', $this->end_date);

        if ($this->isHealthWorker) {
            // Staff: always scope to their assigned areas
            $assignedAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', Auth::id())
                ->pluck('area_id');

            $assignedPuroks = brgy_unit::whereIn('id', $assignedAreaIds)
                ->pluck('brgy_unit');

            if (!empty($this->purok)) {
                // Staff filtered to a specific one of their areas
                $query->whereHas('address', fn($q) => $q->where('purok', $this->purok));
            } else {
                // Staff sees all their assigned areas
                $query->whereHas('address', fn($q) => $q->whereIn('purok', $assignedPuroks));
            }
        } elseif (!empty($this->purok)) {
            // Nurse/admin filtered to a specific purok
            $query->whereHas('address', fn($q) => $q->where('purok', $this->purok));
        }

        return $query->orderBy($this->sortField, $this->sortDirection)->latest();
    }

    public function exportPdf()
    {
        // Get all records matching current filters (no pagination)
        $records = $this->getQuery()->get();

        // Prepare data for PDF
        $data = [
            'records' => $records,
            'filters' => [
                'search' => $this->search,
                'type_of_patient' => $this->type_of_patient,
                'purok' => $this->purok,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'date_generated' => Carbon::now()->format('F j, Y g:i A'),
            ],
            'isHealthWorker' => $this->isHealthWorker,
        ];

        // Generate PDF in portrait orientation
        $pdf = Pdf::loadView('pdf.allRecords.all-patient-record-table', $data)
            ->setPaper('a4', 'portrait');

        // Download PDF
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'patient-records-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $records = $this->getQuery()->paginate($this->entries);

        // Calculate starting number for continuous pagination
        $startingNumber = ($records->currentPage() - 1) * $records->perPage() + 1;

        // Get available patient types for filter dropdown
        $patientTypes = [
            'family-planning' => 'Family Planning',
            'vaccination' => 'Vaccination',
            'tb-dots' => 'Tb dots',
            'prenatal' => 'Prenatal',
            'senior-citizen' => 'Senior Citizen',
            'general-consultation' => 'General Consultation'
            
        ];

        return view('livewire.all-record.records-table', compact(
            'records',
            'startingNumber',
            'patientTypes'
        ));
    }

    public function exportExcel()
    {
        $records = $this->getQuery()->get();

        $filters = [
            'search'   => $this->search,
            'dateFrom' => $this->start_date,
            'dateTo'   => $this->end_date,
            'purok'    => $this->purok ?: 'all',
            'type'     => $this->type_of_patient ?: 'all',
        ];

        // Flatten: one row per (patient + case), same as the PDF loop
        $flatRows = collect();
        foreach ($records as $record) {
            if ($record->medical_record_case->isNotEmpty()) {
                foreach ($record->medical_record_case as $case) {
                    $flatRows->push((object)[
                        'full_name'      => $record->full_name,
                        'age'            => $record->age_display ?? $record->age,
                        'sex'            => $record->sex,
                        'contact_number' => $record->contact_number,
                        'type_of_case'   => $case->type_of_case,
                        'purok'          => $record->address->purok ?? '—',
                        'date_registered' => $case->created_at
                            ? $case->created_at->format('Y-m-d') : '—',
                    ]);
                }
            } else {
                $flatRows->push((object)[
                    'full_name'      => $record->full_name,
                    'age'            => $record->age_display ?? $record->age,
                    'sex'            => $record->sex,
                    'contact_number' => $record->contact_number,
                    'type_of_case'   => null,
                    'purok'          => $record->address->purok ?? '—',
                    'date_registered' => $record->created_at
                        ? $record->created_at->format('Y-m-d') : '—',
                ]);
            }
        }

        $columns = [
            ['label' => '#',               'key' => fn($r) => $flatRows->search(fn($i) => $i === $r) + 1],
            ['label' => 'Full Name',       'key' => 'full_name'],
            ['label' => 'Age',             'key' => 'age'],
            ['label' => 'Sex',             'key' => 'sex'],
            ['label' => 'Contact Number',  'key' => 'contact_number'],
            ['label' => 'Type of Patient', 'key' => fn($r) => $r->type_of_case
                ? Str::title(str_replace('-', ' ', $r->type_of_case)) : '—'],
            ['label' => 'Purok',           'key' => 'purok'],
            ['label' => 'Date Registered', 'key' => 'date_registered'],
        ];

        return Excel::download(
            new PatientRecordsExport($flatRows, $filters, 'Patient Records', $columns),
            'all-patient-records-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
