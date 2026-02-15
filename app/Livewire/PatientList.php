<?php

namespace App\Livewire;

use App\Models\brgy_unit;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\staff;
use App\Models\vaccination_masterlists;
use App\Models\wra_masterlists;
use App\Models\user_addresses;
use App\Models\User;
use App\Models\users_address;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PatientList extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────
    public $search       = '';
    public $statusFilter = 'Active';
    public $purokFilter  = 'all';
    public $typeFilter   = 'all';
    public $perPage      = 15;
    public $start_date;
    public $end_date;

    // ── Edit modal ───────────────────────────────────────────────────────
    public $editPatientId       = null;
    public $edit_first_name     = '';
    public $edit_middle_initial = '';
    public $edit_last_name      = '';
    public $edit_suffix         = '';
    public $edit_date_of_birth  = '';
    public $edit_sex            = '';
    public $edit_contact_number = '';
    public $edit_civil_status   = '';
    public $edit_nationality    = '';
    public $edit_place_of_birth = '';
    // address — "street" holds "blk 1 lot 2, street name" combined
    public $edit_street = '';  // e.g. "Blk 1 Lot 2, Rizal St"
    public $edit_brgy   = '';  // purok column (e.g. "Gawad Kalinga")

    protected $rules = [
        'edit_first_name'     => 'required|string|max:255',
        'edit_last_name'      => 'required|string|max:255',
        'edit_date_of_birth'  => 'required|date',
        'edit_contact_number' => 'required|digits_between:1,12',
        'edit_sex'            => 'required|string',
        'edit_brgy'           => 'required|string',
        'edit_middle_initial' => 'nullable|string|max:255',
        'edit_suffix'         => 'nullable|string|max:255',
        'edit_civil_status'   => 'nullable|string|max:255',
        'edit_nationality'    => 'nullable|string|max:255',
        'edit_place_of_birth' => 'nullable|string|max:255',
        'edit_street'         => 'nullable|string|max:255',
    ];

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshPatients' => '$refresh'];

    public function mount()
    {
        $this->start_date = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->end_date   = Carbon::now()->format('Y-m-d');

        // Lock purok filter for staff — they can only see their assigned area
        if (Auth::user()->role === 'staff') {
            $staffRecord       = staff::findOrFail(Auth::user()->id);
            $assignedArea      = brgy_unit::where('id', $staffRecord->assigned_area_id)->first();
            $this->purokFilter = $assignedArea->brgy_unit;
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Reset page on any filter change                                     */
    /* ------------------------------------------------------------------ */
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
    public function updatingTypeFilter()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingPurokFilter()
    {
        // Staff are locked to their assigned purok — silently re-lock and ignore
        if (Auth::user()->role === 'staff') {
            $staffRecord       = staff::findOrFail(Auth::user()->id);
            $assignedArea      = brgy_unit::where('id', $staffRecord->assigned_area_id)->first();
            $this->purokFilter = $assignedArea->brgy_unit;
            return;
        }
        $this->resetPage();
    }

    /* ------------------------------------------------------------------ */
    /*  Date range — same pattern as vaccination RecordsTable               */
    /* ------------------------------------------------------------------ */
    #[On('dateRangeChanged')]
    public function updateDateRange($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
        $this->resetPage();
    }

    /* ------------------------------------------------------------------ */
    /*  Archive / Activate                                                  */
    /* ------------------------------------------------------------------ */
    public function archivePatient($patientId)
    {
        $patient = patients::findOrFail($patientId);
        $patient->update(['status' => 'Archived']);

        $this->syncMasterlistStatus($patientId, 'Archived');
        $this->dispatch('patientArchived');
    }

    public function activatePatient($patientId)
    {
        $patient = patients::findOrFail($patientId);
        $patient->update(['status' => 'Active']);

        $this->syncMasterlistStatus($patientId, 'Active');
        $this->dispatch('patientActivated');
    }

    private function syncMasterlistStatus(int $patientId, string $status): void
    {
        $records = medical_record_cases::where('patient_id', $patientId)->get();

        foreach ($records as $record) {
            if (in_array($record->type_of_case, ['prenatal', 'family-planning'])) {
                wra_masterlists::where('medical_record_case_id', $record->id)
                    ->update(['status' => $status]);
            } elseif ($record->type_of_case === 'vaccination') {
                vaccination_masterlists::where('medical_record_case_id', $record->id)
                    ->update(['status' => $status]);
            }
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Build the base query (shared by render & PDF download)             */
    /* ------------------------------------------------------------------ */
    private function buildQuery()
    {
        /*
         * One row per (patient, type_of_case) — achieved with a join on
         * medical_record_cases. Patients with NO case record appear with
         * type_of_case = NULL (LEFT JOIN).
         */
        $query = patients::query()
            ->leftJoin('medical_record_cases', 'medical_record_cases.patient_id', '=', 'patients.id')
            ->join('patient_addresses', 'patient_addresses.patient_id', '=', 'patients.id')
            ->select(
                'patients.*',
                'medical_record_cases.type_of_case',
                'patient_addresses.purok'
            );

        // ── Health-worker scope ───────────────────────────────────────────
        if (Auth::user()->role === 'staff') {
            $staff          = staff::findOrFail(Auth::user()->id);
            $assignedArea   = brgy_unit::where('id', $staff->assigned_area_id)->first();

            $query->where('patient_addresses.purok', $assignedArea->brgy_unit);
        }

        // ── Search ────────────────────────────────────────────────────────
        if ($this->search) {
            $term = '%' . str_replace(' ', '%', $this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('patients.full_name',      'like', $term)
                    ->orWhere('patients.first_name',   'like', $term)
                    ->orWhere('patients.last_name',    'like', $term)
                    ->orWhere('patients.contact_number', 'like', $term);
            });
        }

        // ── Status filter ─────────────────────────────────────────────────
        if ($this->statusFilter !== 'all') {
            $query->where('patients.status', ucfirst(strtolower($this->statusFilter)));
        }

        // ── Purok filter ──────────────────────────────────────────────────
        if ($this->purokFilter !== 'all') {
            $query->where('patient_addresses.purok', $this->purokFilter);
        }

        // ── Type-of-patient filter ────────────────────────────────────────
        if ($this->typeFilter !== 'all') {
            $query->where('medical_record_cases.type_of_case', $this->typeFilter);
        }

        // ── Date range (patients.created_at) ─────────────────────────────
        $query->whereDate('patients.created_at', '>=', $this->start_date)
            ->whereDate('patients.created_at', '<=', $this->end_date);

        return $query->latest('patients.created_at');
    }

    /* ------------------------------------------------------------------ */
    /*  Edit Modal                                                          */
    /* ------------------------------------------------------------------ */
    public function openEditModal($patientId)
    {
        $patient = patients::findOrFail($patientId);
        $address = patient_addresses::where('patient_id', $patientId)->first();

        $this->editPatientId       = $patientId;
        $this->edit_first_name     = $patient->first_name;
        $this->edit_middle_initial = $patient->middle_initial;
        $this->edit_last_name      = $patient->last_name;
        $this->edit_suffix         = $patient->suffix;
        $this->edit_date_of_birth  = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->format('Y-m-d')
            : '';
        $this->edit_sex            = $patient->sex;
        $this->edit_contact_number = $patient->contact_number;
        $this->edit_civil_status   = $patient->civil_status;
        $this->edit_nationality    = $patient->nationality;
        $this->edit_place_of_birth = $patient->place_of_birth;

        // Reconstruct the combined street field: "house_number, street" or just one of them
        $streetParts = array_filter([$address->house_number ?? '', $address->street ?? '']);
        $this->edit_street = implode(', ', $streetParts);

        // Staff: always lock brgy (purok) to their assigned area
        if (Auth::user()->role === 'staff') {
            $staffRecord      = staff::findOrFail(Auth::user()->id);
            $assignedArea     = brgy_unit::where('id', $staffRecord->assigned_area_id)->first();
            $this->edit_brgy  = $assignedArea->brgy_unit;
        } else {
            $this->edit_brgy = $address->purok ?? '';
        }

        $this->dispatch('openEditModal');
    }

    public function updatePatient()
    {
        $this->validate();

        $patient = patients::findOrFail($this->editPatientId);

        // ── Build name parts (same pattern you use) ───────────────────────
        $middle     = substr($this->edit_middle_initial ?? '', 0, 1);
        $middle     = $middle ? strtoupper($middle) . '.' : null;

        $parts      = array_filter([
            strtolower($this->edit_first_name),
            $middle,
            strtolower($this->edit_last_name),
            $this->edit_suffix ?? null,
        ]);
        $fullName   = ucwords(trim(implode(' ', $parts)));

        $firstName  = ucwords(strtolower($this->edit_first_name));
        $lastName   = ucwords(strtolower($this->edit_last_name));
        $middleName = $this->edit_middle_initial
            ? ucwords(strtolower($this->edit_middle_initial))
            : null;

        $age       = Carbon::parse($this->edit_date_of_birth)->age;
        $ageMonths = Carbon::parse($this->edit_date_of_birth)->diffInMonths(now());

        // ── 1. Update patients table ──────────────────────────────────────
        $patient->update([
            'first_name'     => $firstName,
            'middle_initial' => $middleName,
            'last_name'      => $lastName,
            'full_name'      => $fullName,
            'suffix'         => $this->edit_suffix ?: null,
            'date_of_birth'  => $this->edit_date_of_birth,
            'sex'            => $this->edit_sex,
            'contact_number' => $this->edit_contact_number,
            'civil_status'   => $this->edit_civil_status ?: null,
            'nationality'    => $this->edit_nationality   ?: null,
            'place_of_birth' => $this->edit_place_of_birth ?: null,
            'age'            => $age,
            'age_in_months'  => $ageMonths,
        ]);

        // ── 2. Update patient_addresses ───────────────────────────────────
        $address = patient_addresses::where('patient_id', $this->editPatientId)->first();

        $blk_n_street = explode(',', $this->edit_street, 2);
        $address->update([
            'house_number' => trim($blk_n_street[0]),
            'street'       => isset($blk_n_street[1]) ? trim($blk_n_street[1]) : '',
            'purok'        => $this->edit_brgy ?? $address->purok,
        ]);
        $address->refresh(); // pulls in DB defaults (barangay, city, province)

        $fullAddress = collect([
            $address->house_number,
            $address->street,
            $address->purok,
            $address->barangay ?? null,
            $address->city     ?? null,
            $address->province ?? null,
        ])->filter()->join(', ');

        // ── 3. Cascade updates ────────────────────────────────────────────
        $this->cascadePatientUpdate($patient, $fullName, $fullAddress, $age, $ageMonths);

        // ── 4. Update linked user if exists ──────────────────────────────
        if ($patient->user_id) {
            User::where('id', $patient->user_id)->update([
                'first_name'     => $firstName,
                'middle_initial' => $middleName,
                'last_name'      => $lastName,
                'full_name'      => $fullName,
                'suffix'         => $this->edit_suffix ?: null,
                'date_of_birth'  => $this->edit_date_of_birth,
                'contact_number' => $this->edit_contact_number,
                'address'        => $fullAddress,
            ]);

            $userAddress = users_address::where('user_id', $patient->user_id)->first();
            if ($userAddress) {
                $userAddress->update([
                    'house_number' => $address->house_number,
                    'street'       => $address->street,
                    'purok'        => $address->purok,
                ]);
            }
        }

        $this->editPatientId = null;
        $this->dispatch('patientUpdated');
        $this->dispatch('closeEditModal');
    }

    private function cascadePatientUpdate($patient, string $fullName, string $fullAddress, int $age, int $ageMonths): void
    {
        $cases = medical_record_cases::where('patient_id', $patient->id)->get();

        foreach ($cases as $case) {
            switch ($case->type_of_case) {

                case 'family-planning':
                    if (DB::table('family_planning_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('family_planning_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update([
                                'client_name'           => $fullName,
                                'client_date_of_birth'  => $this->edit_date_of_birth,
                                'client_age'            => $age,
                                'client_address'        => $fullAddress,
                                'client_contact_number' => $this->edit_contact_number,
                                'client_civil_status'   => $this->edit_civil_status,
                                'client_suffix'         => $this->edit_suffix,
                            ]);
                    }

                    if (DB::table('family_planning_medical_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('family_planning_medical_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (wra_masterlists::where('medical_record_case_id', $case->id)->exists()) {
                        wra_masterlists::where('medical_record_case_id', $case->id)
                            ->update([
                                'name_of_wra'   => $fullName,
                                'address'       => $fullAddress,
                                'date_of_birth' => $this->edit_date_of_birth,
                            ]);
                    }
                    break;

                case 'prenatal':
                    if (DB::table('pregnancy_checkups')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('pregnancy_checkups')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('pregnancy_plans')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('pregnancy_plans')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('prenatal_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('prenatal_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (wra_masterlists::where('medical_record_case_id', $case->id)->exists()) {
                        wra_masterlists::where('medical_record_case_id', $case->id)
                            ->update([
                                'name_of_wra'   => $fullName,
                                'address'       => $fullAddress,
                                'date_of_birth' => $this->edit_date_of_birth,
                            ]);
                    }
                    break;

                case 'senior-citizen':
                    if (DB::table('senior_citizen_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('senior_citizen_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('senior_citizen_medical_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('senior_citizen_medical_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }
                    break;

                case 'tb-dots':
                    if (DB::table('tb_dots_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('tb_dots_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('tb_dots_check_ups')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('tb_dots_check_ups')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (DB::table('tb_dots_medical_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('tb_dots_medical_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }
                    break;

                case 'vaccination':
                    if (DB::table('vaccination_case_records')
                        ->where('medical_record_case_id', $case->id)
                        ->exists()
                    ) {
                        DB::table('vaccination_case_records')
                            ->where('medical_record_case_id', $case->id)
                            ->update(['patient_name' => $fullName]);
                    }

                    if (vaccination_masterlists::where('medical_record_case_id', $case->id)->exists()) {
                        vaccination_masterlists::where('medical_record_case_id', $case->id)
                            ->update([
                                'name_of_child' => $fullName,
                                'Address'       => $fullAddress,
                                'sex'           => $this->edit_sex,
                                'date_of_birth' => $this->edit_date_of_birth,
                                'age'           => $age,
                                'age_in_months' => $age === 0 ? $ageMonths : null,
                            ]);
                    }
                    break;
            }
        }
    }

    /* ------------------------------------------------------------------ */
    /* ------------------------------------------------------------------ */
    public function downloadPdf()
    {
        // Collect ALL matching rows (no pagination limit for PDF)
        $rows = $this->buildQuery()->get();

        $filters = [
            'status'    => $this->statusFilter,
            'purok'     => $this->purokFilter,
            'type'      => $this->typeFilter,
            'dateFrom'  => $this->start_date,
            'dateTo'    => $this->end_date,
        ];

        // We redirect to a dedicated controller route that renders dompdf
        // and passes the data via session (flash).
        session()->flash('pdf_rows',    $rows);
        session()->flash('pdf_filters', $filters);

        return redirect()->route('patients.pdf');
    }

    /* ------------------------------------------------------------------ */
    /*  Render                                                              */
    /* ------------------------------------------------------------------ */
    public function render()
    {
        $patients = $this->buildQuery()->paginate($this->perPage);

        $isStaff      = Auth::user()->role === 'staff';
        $assignedPurok = null;

        if ($isStaff) {
            $staffRecord   = staff::findOrFail(Auth::user()->id);
            $assignedArea  = brgy_unit::where('id', $staffRecord->assigned_area_id)->first();
            $assignedPurok = $assignedArea->brgy_unit;
        }

        $puroks = brgy_unit::orderBy('brgy_unit')->pluck('brgy_unit');

        $caseTypes = [
            'vaccination',
            'prenatal',
            'family-planning',
            'senior-citizen',
            'tb-dots',
        ];

        return view('livewire.patient-list', [
            'patients'      => $patients,
            'puroks'        => $puroks,
            'caseTypes'     => $caseTypes,
            'isStaff'       => $isStaff,
            'assignedPurok' => $assignedPurok,
        ]);
    }
}
