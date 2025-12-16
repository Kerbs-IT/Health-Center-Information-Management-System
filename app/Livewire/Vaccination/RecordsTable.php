<?php

namespace App\Livewire\Vaccination;

use App\Models\medical_record_cases;
use BcMath\Number;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class RecordsTable extends Component
{
    use WithPagination;

    public $entries = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'asc';
    // new property for searching
    public $search = '';

    protected $queryString = ['entries', 'sortField', 'sortDirection','search'];
  
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

    public function render()
    {
        $vaccinationRecord = medical_record_cases::select('medical_record_cases.*')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('type_of_case', 'vaccination')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('vaccination_medical_records', 'vaccination_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('vaccination_medical_records.health_worker_id', Auth::id());
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->entries);

        // Add vaccination status to each record
        $vaccinationRecord->getCollection()->transform(function ($record) {
            $record->vaccination_status_info = $this->calculateVaccinationStatus($record);
            return $record;
        });

      
        // Sort by priority
        $sortedCollection = $vaccinationRecord->getCollection()->sortBy(function ($record) {
            if ($record->vaccination_status_info) {
                return $record->vaccination_status_info['sort_priority'];
            }
            return 3;
        });

        $vaccinationRecord->setCollection($sortedCollection);

        return view('livewire.vaccination.records-table', [
            'vaccinationRecord' => $vaccinationRecord,
        ]);
    }

    public function exportPdf(){
        return redirect()->route('vaccination.pdf', [
            'search' => $this->search,              // Sends "Maria"
            'sortField' => $this->sortField,        // Sends "full_name"
            'sortDirection' => $this->sortDirection,
            'entries' => $this->entries, // Sends "desc"
        ]);
    }

    /**
     * Check if patient needs vaccination today or is overdue
     */
    private function calculateVaccinationStatus($medicalRecordCase)
    {
        try {
            $lastVaccinationCase = DB::table('vaccination_case_records')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->where('vaccination_status', 'completed')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastVaccinationCase) {
                return null;
            }

            $comebackDate = Carbon::parse($lastVaccinationCase->date_of_comeback);

            if ($comebackDate->isFuture()) {
                return null;
            }

            // FIX: Change 'dosage' to 'dose_number'
            if ($lastVaccinationCase->dose_number >= 3) {
                return null;
            }

            // FIX: Change 'dosage' to 'dose_number'
            $nextDosage = $lastVaccinationCase->dose_number + 1;

            // FIX: Change 'dosage' to 'dose_number'
            $nextDoseExists = DB::table('vaccination_case_records')
                ->where('medical_record_case_id', $medicalRecordCase->id)
                ->where('vaccine_type', $lastVaccinationCase->vaccine_type)
                ->where('status','!=','Archived')
                ->where('dose_number', $nextDosage)  // ← Changed here
                ->exists();

            if ($nextDoseExists) {
                return null;
            }

            // Parse vaccine types
            $vaccines = explode(',', $lastVaccinationCase->vaccine_type ?? '');
            $dueVaccines = [];

            foreach ($vaccines as $vaccine) {
                $vaccineName = trim($vaccine);
                if (!empty($vaccineName)) {
                    $dueVaccines[] = $vaccineName . ' Dose ' . $nextDosage;
                }
            }

            if (empty($dueVaccines)) {
                return null;
            }

            // Determine status
            if ($comebackDate->isToday()) {
                return [
                    'status' => 'due_today',
                    'badge' => 'Due Today',
                    'class' => 'table-success',
                    'badge_class' => 'badge bg-success',
                    'due_vaccines' => $dueVaccines,
                    'next_dosage' => $nextDosage,
                    'sort_priority' => 2
                ];
            } else {
                $daysOverdue = (int) $comebackDate->diffInDays(now(), false);

                return [
                    'status' => 'overdue',
                    'badge' => $daysOverdue . ($daysOverdue == 1 ? ' day' : ' days') . ' overdue',
                    'class' => 'table-danger',
                    'badge_class' => 'badge bg-danger',
                    'due_vaccines' => $dueVaccines,
                    'next_dosage' => $nextDosage,
                    'days_overdue' => $daysOverdue,
                    'sort_priority' => 1
                ];
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
