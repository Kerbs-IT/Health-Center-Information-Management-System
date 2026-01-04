<?php

namespace App\Livewire\Vaccination;

use App\Models\medical_record_cases;
use BcMath\Number;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search'];

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

    public function exportPdf()
    {
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
            $vaccineDoseConfig = [
                'BCG' => ['acronym' => 'BCG', 'maxDoses' => 1, 'description' => 'at birth', 'name' => 'BCG Vaccine'],
                'Hepatitis B' => ['acronym' => 'Hepatitis B', 'maxDoses' => 1, 'description' => 'at birth', 'name' => 'Hepatitis B Vaccine'],
                'PENTA' => ['acronym' => 'PENTA', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Pentavalent Vaccine (DPT-HEP B-HIB)'],
                'OPV' => ['acronym' => 'OPV', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Oral Polio Vaccine (OPV)'],
                'IPV' => ['acronym' => 'IPV', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Inactived Polio Vaccine (IPV)'],
                'PCV' => ['acronym' => 'PCV', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Pnueumococcal Conjugate Vaccine (PCV)'],
                'MMR' => ['acronym' => 'MMR', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Measles, Mumps, Rubella Vaccine (MMR)'],
                'MCV' => ['acronym' => 'MCV', 'maxDoses' => 1, 'description' => 'dose 1', 'name' => 'Measles Containing Vaccine (MCV) MR/MMR (Grade 1)'],
                'TD' => ['acronym' =>  'TD', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Tetanus Diphtheria (TD)'],
                'Human Papiliomavirus' => ['acronym' => 'Human Papiliomavirus', 'maxDoses' => 2, 'description' => 'doses 1-2', 'name' => 'Human Papiliomavirus Vaccine'],
                'Influenza Vaccine' => ['acronym' => 'Influenza Vaccine', 'maxDoses' => 3, 'description' => 'doses 1-3', 'name' => 'Influenza Vaccine'],
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

            // Parse vaccine types
            $vaccines = explode(',', $lastVaccinationCase->vaccine_type ?? '');
            $currentDose = $lastVaccinationCase->dose_number;
            $nextDosage = $currentDose + 1;

            // Check if ALL vaccines have reached their maximum doses
            $allVaccinesComplete = true;
            $dueVaccines = [];
            $vaccineCompleted = [];

            foreach ($vaccines as $vaccine) {
                $vaccineAcronym = trim($vaccine); // IMPORTANT: Added trim() here

                if (isset($vaccineDoseConfig[$vaccineAcronym])) {
                    $maxDoses = $vaccineDoseConfig[$vaccineAcronym]['maxDoses'];
                    $vaccineName = $vaccineDoseConfig[$vaccineAcronym]['acronym'];

                    // Check if this specific vaccine still has doses remaining
                    if ($currentDose < $maxDoses) {
                        $allVaccinesComplete = false;

                        // Check if next dose doesn't exist yet
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



            // If all vaccines are complete, show completion status
            if ($allVaccinesComplete && !empty($vaccineCompleted)) {
                // implode the completed vaccine
                $implodedVaccineCompleted = implode(",", $vaccineCompleted);
                return [
                    'status' => 'complete',
                    'badge' => 'Vaccination Complete',
                    'class' => 'table-light',
                    'badge_class' => 'badge bg-success',
                    'due_vaccines' => ["$implodedVaccineCompleted doses is completed. Proceed to another vaccination if needed."],
                    'sort_priority' => 3
                ];
            }

            // If no vaccines are actually due (all next doses already exist)
            if (empty($dueVaccines)) {
                return null;
            }

            // Determine status for vaccines that are still pending
            if ($comebackDate->isToday()) {
                return [
                    'status' => 'due_today',
                    'badge' => 'Due Today',
                    'class' => 'table-warning',
                    'badge_class' => 'badge bg-warning text-dark',
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
            Log::error('Vaccination status calculation error: ' . $e->getMessage());
            return null;
        }
    }
}
