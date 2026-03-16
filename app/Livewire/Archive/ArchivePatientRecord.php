<?php

namespace App\Livewire\Archive;

use App\Models\medical_record_cases;
use App\Models\nurses;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\User;
use App\Models\vaccination_case_records;
use App\Models\vaccination_masterlists;
use App\Models\vaccines;
use App\Models\wra_masterlists;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ArchivePatientRecord extends Component
{
    use WithPagination;

    public $entries = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'asc';
    public $search = '';
    public $patient_id = null;
    public $type_of_case = null; // Can be null for "All Records"
    public $showAllTypes = false; // NEW: Flag to show all types

    public $elementId;

    protected $queryString = ['entries', 'sortField', 'sortDirection', 'search', 'patient_id', 'type_of_case', 'showAllTypes'];
    protected $paginationTheme = 'bootstrap';

    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');

        // Get parameters from URL
        $this->patient_id = request()->get('patient_id');
        $this->type_of_case = request()->get('type_of_case');
        $this->showAllTypes = request()->get('showAllTypes', false); // Check if showing all types
        $this->search = request()->get('search', '');

        // If showAllTypes is true, don't filter by type
        if ($this->showAllTypes) {
            $this->type_of_case = null;
        }

        $elementIdMap = [
            'prenatal' => 'record_prenatal',
            'family-planning' => 'record_family_planning',
            'tb-dots' => 'record_tb_dots',
            'senior-citizen' => 'record_senior_citizen',
            'vaccination' => 'record_vaccination'
        ];

        $this->elementId = $elementIdMap[$this->type_of_case] ?? '';
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
        $this->search = '';
        $this->resetPage();
    }

    #[On('dateRangeChanged')]
    public function updateDateRange($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->resetPage();
    }

    public function render()
    {
        $query = medical_record_cases::select('medical_record_cases.*')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('medical_record_cases.status', 'Archived') // ← base filter on CASE status, not patient
            ->where('patients.full_name', 'like', '%' . $this->search . '%');

        // Only filter by type if NOT showing all types
        if (!$this->showAllTypes && $this->type_of_case) {
            $query->where('medical_record_cases.type_of_case', $this->type_of_case);
        }

        $archivedRecords = $query
            ->when($this->patient_id, function ($query) {
                $query->where('patients.id', $this->patient_id);
            })
            ->when(Auth::user()->role == 'staff' && !$this->showAllTypes, function ($query) {
                $this->applyHealthWorkerFilter($query);
            })
            ->when($this->sortField === 'age', function ($query) {
                $query->orderBy('patients.age', $this->sortDirection)
                    ->orderBy('patients.age_in_months', $this->sortDirection);
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->whereDate('patients.created_at', '>=', $this->start_date)
            ->whereDate('patients.created_at', '<=', $this->end_date)
            ->paginate($this->entries);

        return view('livewire.archive.archive-patient-record', [
            'archivedRecords' => $archivedRecords,
            'type_of_case' => $this->type_of_case,
            'showAllTypes' => $this->showAllTypes,
        ]);
    }           
    private function applyHealthWorkerFilter($query)
    {
        switch ($this->type_of_case) {
            case 'vaccination':
                $query->join('vaccination_medical_records', 'vaccination_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('vaccination_medical_records.health_worker_id', Auth::id());
                break;

            case 'prenatal':
                $query->join('prenatal_medical_records', 'prenatal_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('prenatal_medical_records.health_worker_id', Auth::id());
                break;

            case 'senior-citizen':
                $query->join('senior_citizen_medical_records', 'senior_citizen_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('senior_citizen_medical_records.health_worker_id', Auth::id());
                break;

            case 'tb-dots':
                $query->join('tb_dots_medical_records', 'tb_dots_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('tb_dots_medical_records.health_worker_id', Auth::id());
                break;

            case 'family-planning':
                $query->join('family_planning_medical_records', 'family_planning_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('family_planning_medical_records.health_worker_id', Auth::id());
                break;
        }
    }

    public function activatePatient($patientId, $caseId)
    {
        $patient = patients::findOrFail($patientId);
        $case    = medical_record_cases::findOrFail($caseId);

        // Check for duplicate active patient
        $duplicatePatient = patients::where('status', '!=', 'Archived')
            ->where('first_name', $patient->first_name)
            ->where('last_name', $patient->last_name)
            ->where('id', '!=', $patient->id)
            ->exists();

        if ($duplicatePatient) {
            $this->dispatch('activationError', [
                'message' => "Cannot restore: An active patient with the name '{$patient->first_name} {$patient->last_name}' already exists."
            ]);
            return;
        }

        // Restore the specific case
        $case->update(['status' => 'Active']);

        // --- Side effects per case type ---

        $wraMasterlist = wra_masterlists::where('patient_id', $patientId)->first();
        if ($wraMasterlist) {
            $wraMasterlist->update(['status' => 'Active']);
        } else {
            Log::warning("WRA masterlist missing on recovery for patient_id: {$patientId}. Type: {$case->type_of_case}.");
        }

        if ($case->type_of_case === 'vaccination') {
            $vaccinationMasterlist = vaccination_masterlists::where('medical_record_case_id', $case->id)->first();

            if ($vaccinationMasterlist) {
                $vaccinationMasterlist->update(['status' => 'Active']);
            } else {
                // Masterlist missing — recreate it
                $patientAddress = patient_addresses::where('patient_id', $patientId)->firstOrFail();

                $medicalCase = medical_record_cases::with('vaccination_medical_record')
                    ->where('patient_id', $patientId)
                    ->where('type_of_case', 'vaccination')
                    ->firstOrFail();

                $fullAddress   = "$patientAddress->house_number $patientAddress->street $patientAddress->purok $patientAddress->barangay $patientAddress->city $patientAddress->province";
                $ageInMonths   = ($patient->age == 0 && $patient->date_of_birth)
                    ? $this->calculateAgeInMonths($patient->date_of_birth)
                    : null;

                $nurse         = User::where('role', 'nurse')->first();
                $nurseInfo     = nurses::where('user_id', $nurse->id)->first();
                $nurseFullname = ucwords($nurseInfo->full_name);

                $vaccinationMasterlist = vaccination_masterlists::create([
                    'brgy_name'              => $patientAddress->purok,
                    'midwife'                => "Nurse " . $nurseFullname ?? null,
                    'health_worker_id'       => $medicalCase->vaccination_medical_record->handled_by,
                    'medical_record_case_id' => $medicalCase->id,
                    'name_of_child'          => $patient->full_name,
                    'patient_id'             => $patientId,
                    'address_id'             => $patientAddress->id,
                    'Address'                => trim($fullAddress),
                    'sex'                    => $patient->sex,
                    'age'                    => $patient->age,
                    'age_in_months'          => $ageInMonths,
                    'date_of_birth'          => $patient->date_of_birth,
                ]);

                $validVaccineColumns  = [
                    'BCG',
                    'Hepatitis B',
                    'PENTA_1',
                    'PENTA_2',
                    'PENTA_3',
                    'OPV_1',
                    'OPV_2',
                    'OPV_3',
                    'PCV_1',
                    'PCV_2',
                    'PCV_3',
                    'IPV_1',
                    'IPV_2',
                    'MCV_1',
                    'MCV_2',
                ];
                $noDoseSuffixVaccines = ['Hepatitis B', 'BCG'];

                $vaccinationCaseRecords = vaccination_case_records::where('medical_record_case_id', $medicalCase->id)
                    ->where('status', '!=', 'Archived')
                    ->get();

                foreach ($vaccinationCaseRecords as $caseRecord) {
                    $vaccineTypes = explode(',', $caseRecord->vaccine_type);

                    foreach ($vaccineTypes as $type) {
                        $type    = trim($type);
                        $vaccine = vaccines::where('vaccine_acronym', $type)->first();

                        if (!$vaccine) continue;

                        $itemColumn = in_array($vaccine->vaccine_acronym, $noDoseSuffixVaccines)
                            ? $vaccine->vaccine_acronym
                            : Str::upper($vaccine->vaccine_acronym) . '_' . $caseRecord->dose_number;

                        if (in_array($itemColumn, $validVaccineColumns)) {
                            $vaccinationMasterlist->update([
                                $itemColumn => $caseRecord->date_of_vaccination,
                            ]);
                        }
                    }
                }
            }
        }

        // --- Restore patient status only if no archived cases remain ---
        $remainingArchivedCases = medical_record_cases::where('patient_id', $patientId)
            ->where('status', 'Archived')
            ->count();

        if ($remainingArchivedCases === 0) {
            $patient->update(['status' => 'Active']);
        }

        $this->dispatch('patientActivated');
    }
}
