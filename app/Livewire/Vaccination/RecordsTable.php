<?php

namespace App\Livewire\Vaccination;

use App\Models\medical_record_cases;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

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
        $vaccinationRecord = medical_record_cases::select('medical_record_cases.*')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('medical_record_cases.type_of_case', 'vaccination')
            ->where('patients.full_name', 'like', '%' . $this->search . '%')
            ->where('patients.status', '!=', 'Archived')
            ->when($this->patient_id, function ($query) {
                $query->where('patients.id', $this->patient_id);
            })
            ->when(Auth::user()->role == 'staff', function ($query) {
                $query->join('vaccination_medical_records', 'vaccination_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('vaccination_medical_records.health_worker_id', Auth::id());
            })
            ->whereDate('patients.created_at', '>=', $this->start_date)
            ->whereDate('patients.created_at', '<=', $this->end_date)
            ->orderByRaw("
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM vaccination_case_records vcr
                        WHERE vcr.medical_record_case_id = medical_record_cases.id
                        AND vcr.status != 'Archived'
                        AND vcr.vaccination_status = 'completed'
                        AND DATE(vcr.date_of_comeback) < CURDATE()
                    ) THEN 1
                    WHEN EXISTS (
                        SELECT 1 FROM vaccination_case_records vcr
                        WHERE vcr.medical_record_case_id = medical_record_cases.id
                        AND vcr.status != 'Archived'
                        AND vcr.vaccination_status = 'completed'
                        AND DATE(vcr.date_of_comeback) = CURDATE()
                    ) THEN 2
                    ELSE 3
                END ASC
            ")
            ->when($this->sortField === 'age', function ($query) {
                $query->orderBy('patients.age', $this->sortDirection)
                    ->orderBy('patients.age_in_months', $this->sortDirection);
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate($this->entries);

        $vaccinationRecord->getCollection()->transform(function ($record) {
            $record->vaccination_status_info = $this->calculateVaccinationStatus($record);
            return $record;
        });

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

    private function calculateVaccinationStatus($medicalRecordCase)
    {
        try {
            $vaccineDoseConfig = [
                'BCG'                  => ['acronym' => 'BCG',                  'maxDoses' => 1, 'description' => 'at birth',  'name' => 'BCG Vaccine'],
                'Hepatitis B'          => ['acronym' => 'Hepatitis B',          'maxDoses' => 1, 'description' => 'at birth',  'name' => 'Hepatitis B Vaccine'],
                'PENTA'                => ['acronym' => 'PENTA',                'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Pentavalent Vaccine (DPT-HEP B-HIB)'],
                'OPV'                  => ['acronym' => 'OPV',                  'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Oral Polio Vaccine (OPV)'],
                'IPV'                  => ['acronym' => 'IPV',                  'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Inactived Polio Vaccine (IPV)'],
                'PCV'                  => ['acronym' => 'PCV',                  'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Pnueumococcal Conjugate Vaccine (PCV)'],
                'MMR'                  => ['acronym' => 'MMR',                  'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Measles, Mumps, Rubella Vaccine (MMR)'],
                'MCV'                  => ['acronym' => 'MCV',                  'maxDoses' => 1, 'description' => 'dose 1',    'name' => 'Measles Containing Vaccine (MCV) MR/MMR (Grade 1)'],
                'TD'                   => ['acronym' => 'TD',                   'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Tetanus Diphtheria (TD)'],
                'Human Papiliomavirus' => ['acronym' => 'Human Papiliomavirus', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Human Papiliomavirus Vaccine'],
                'Influenza Vaccine'    => ['acronym' => 'Influenza Vaccine',    'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Influenza Vaccine'],
                'Pnuemococcal Vaccine' => ['acronym' => 'Pnuemococcal Vaccine', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Pnuemococcal Vaccine'],
            ];

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
                        if ($currentDose >= $maxDoses) {
                            $vaccineCompleted[] = $vaccine;
                        }
                    }
                }
            }

            if ($allVaccinesComplete && !empty($vaccineCompleted)) {
                $implodedVaccineCompleted = implode(",", $vaccineCompleted);
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

            if ($comebackDate->isToday()) {
                return [
                    'status'        => 'due_today',
                    'badge'         => 'Due Today',
                    'class'         => 'table-warning',
                    'badge_class'   => 'badge bg-warning text-dark',
                    'due_vaccines'  => $dueVaccines,
                    'next_dosage'   => $nextDosage,
                    'sort_priority' => 2,
                ];
            } else {
                $daysOverdue = (int) $comebackDate->diffInDays(now(), false);
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
            }
        } catch (\Exception $e) {
            Log::error('Vaccination status calculation error: ' . $e->getMessage());
            return null;
        }
    }
}
