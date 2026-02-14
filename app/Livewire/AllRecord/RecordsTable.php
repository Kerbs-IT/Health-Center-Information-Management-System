<?php

namespace App\Livewire\AllRecord;

use App\Models\patients;
use App\Models\staff;
use App\Models\brgy_unit;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RecordsTable extends Component
{
    use WithPagination;

    public $entries = 10;
    public $sortField = 'created_at';
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

        // Check if user is health worker (staff)
        if (Auth::check() && Auth::user()->role === 'staff') {
            $this->isHealthWorker = true;

            // Get staff record using user_id
            $staff = staff::where('user_id', Auth::user()->id)->first();

            if ($staff && $staff->assigned_area_id) {
                $assignedArea = brgy_unit::find($staff->assigned_area_id);

                if ($assignedArea) {
                    $this->assignedPurok = $assignedArea->brgy_unit;
                    // For health workers, only show their assigned purok in dropdown
                    $this->availablePuroks = [$assignedArea->brgy_unit => $assignedArea->brgy_unit];
                    // Auto-select their assigned purok
                    $this->purok = $assignedArea->brgy_unit;
                }
            }
        } else {
            // For admin/other roles, load all puroks
            $this->availablePuroks = brgy_unit::orderBy('brgy_unit', 'asc')
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
            $q->where("status", 'Active');
        }, 'address'])
            ->whereHas('medical_record_case', function ($q) {
                $q->where('status', 'Active');

                // Filter by type of patient if selected
                if (!empty($this->type_of_patient)) {
                    $q->where('type_of_case', $this->type_of_patient);
                }
            })
            ->where('full_name', 'like', '%' . $this->search . '%')
            ->where('status', 'Active')
            ->whereDate('patients.created_at', '>=', $this->start_date)
            ->whereDate('patients.created_at', '<=', $this->end_date);

        // Health-worker scope - filter by assigned purok
        if ($this->isHealthWorker && $this->assignedPurok) {
            $query->whereHas('address', function ($q) {
                $q->where('purok', $this->assignedPurok);
            });
        }
        // Admin/other roles - filter by selected purok if any
        elseif (!empty($this->purok)) {
            $query->whereHas('address', function ($q) {
                $q->where('purok', $this->purok);
            });
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
            
        ];

        return view('livewire.all-record.records-table', compact(
            'records',
            'startingNumber',
            'patientTypes'
        ));
    }
}
