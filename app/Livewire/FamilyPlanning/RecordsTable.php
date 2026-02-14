<?php

namespace App\Livewire\FamilyPlanning;

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
    public $sortField = 'created_at';
    public $sortDirection = 'asc';
    // new property for searching
    public $search = '';

    // for redirect to specific page
    public $patient_id = null;

    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search','patient_id'];
    protected $paginationTheme = 'bootstrap';
    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');
        $this->patient_id = request()->get('patient_id');

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
        $this->resetPage(); // Reset to first page when filtering
    }
    public function clearFilter()
    {
        $this->patient_id = null;
        $this->search = '';
        $this->resetPage();
    }
    public function render()
    {
        $familyPlanning = medical_record_cases::select('medical_record_cases.*', 'patients.full_name', 'patients.age', 'patients.sex', 'patients.contact_number')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'family-planning')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status','Active')
            ->when($this->patient_id,function($query){
                $query -> where('patients.id', $this->patient_id);
            })
            ->when(Auth::user()->role == 'staff', function ($query) {
                // Add join to vaccination_medical_records to filter by health_worker_id
                $query->join('family_planning_medical_records', 'family_planning_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('family_planning_medical_records.health_worker_id', Auth::id());
            })
            ->whereDate('patients.created_at', '>=', $this->start_date)
            ->whereDate('patients.created_at', '<=', $this->end_date)
            ->orderBy("patients.$this->sortField", $this->sortDirection)
            ->paginate($this->entries);

        // Add follow-up visit status to each record
        $familyPlanning->getCollection()->transform(function ($record) {
            $record->followup_status_info = $this->calculateFollowUpStatus($record);
            return $record;
        });

        // Sort by priority (overdue first, then due today, then others)
        $sortedCollection = $familyPlanning->getCollection()->sortBy(function ($record) {
            if ($record->followup_status_info) {
                return $record->followup_status_info['sort_priority'];
            }
            return 3; // No status = lowest priority
        });

        $familyPlanning->setCollection($sortedCollection);

        return view(
            'livewire.family-planning.records-table',
            ['isActive' => true, 'page' => 'RECORD', 'familyPlanningRecords' => $familyPlanning]
        );
    }
    public function exportPdf()
    {
        return redirect()->route('family-planning.pdf', [
            'search' => $this->search,              
            'sortField' => $this->sortField,       
            'sortDirection' => $this->sortDirection,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'entries' => $this->entries, // Sends "desc"
        ]);
    }

    private function calculateFollowUpStatus($medicalRecordCase)
    {
        try {
            // Get the most recent active record with follow-up visit date
            $lastRecord = DB::table('family_planning_side_b_records')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->whereNotNull('date_of_follow_up_visit')
                ->orderBy('created_at', 'desc')
                ->first();

            // No record history = no status
            if (!$lastRecord) {
                return null;
            }

            $followUpDate = Carbon::parse($lastRecord->date_of_follow_up_visit);

            // Only process if follow-up date is today or past
            if ($followUpDate->isFuture()) {
                return null;
            }

            // Check if follow-up visit already done for this date
            $visitExists = DB::table('family_planning_side_b_records')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->whereDate('created_at', '>=', $followUpDate)
                ->where('id', '!=', $lastRecord->id) // Exclude the record that set this follow-up date
                ->exists();

            if ($visitExists) {
                return null;
            }

            // Determine status
            if ($followUpDate->isToday()) {
                return [
                    'status' => 'due_today',
                    'badge' => 'Follow-up Due Today',
                    'class' => 'table-success',
                    'badge_class' => 'badge bg-success',
                    'followup_date' => $followUpDate->format('M j, Y'),
                    'sort_priority' => 2
                ];
            } else {
                $daysOverdue = (int) $followUpDate->diffInDays(now(), false);

                return [
                    'status' => 'overdue',
                    'badge' => $daysOverdue . ($daysOverdue == 1 ? ' day' : ' days') . ' overdue',
                    'class' => 'table-danger',
                    'badge_class' => 'badge bg-danger',
                    'followup_date' => $followUpDate->format('M j, Y'),
                    'days_overdue' => $daysOverdue,
                    'sort_priority' => 1
                ];
            }
        } catch (\Exception $e) {
            // Log error but don't break the page
            // \Log::error('Family planning follow-up status calculation error: ' . $e->getMessage());
            return null;
        }
    }
}
