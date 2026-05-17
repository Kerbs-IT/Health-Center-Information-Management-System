<?php

namespace App\Livewire;

use App\Models\brgy_unit;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\patients;
use App\Models\staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientAccountBinding extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'active';
    public $filterPatientType = 'all';
    public $filterPurok = 'all';
    public $showModal = false;
    public $selectedUser = null;
    public $recordSearch = '';
    public $patientRecords = [];
    public $selectedRecordId = null;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['manageUserRefreshTable' => 'refresh'];

    public function render()
    {
        $query = User::where('role', 'patient');

        // Get assigned puroks for staff restriction and dropdown
        $assignedPuroks = collect();

        if (Auth::user()->role == 'staff') {
            // staff_id in staff_area_assignments = staff table's user_id (Auth::id())
            $assignedAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', Auth::id())
                ->pluck('area_id');

            $assignedPuroks = brgy_unit::whereIn('id', $assignedAreaIds)
                ->pluck('brgy_unit');

            if ($assignedPuroks->isNotEmpty()) {
                $query->whereHas(
                    'user_address',
                    fn($q) => $q->whereIn('purok', $assignedPuroks)
                );
            }
        }

        // Search
        $query->where(function ($q) {
            $q->where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        });

        // Filters
        $query->when($this->filterStatus === 'active',   fn($q) => $q->where('status', 'active'));
        $query->when($this->filterStatus === 'archived', fn($q) => $q->where('status', 'archived'));
        $query->when($this->filterPatientType !== 'all', fn($q) => $q->where('patient_type', $this->filterPatientType));
        $query->when($this->filterPurok !== 'all',       fn($q) => $q->whereHas('user_address', fn($q) => $q->where('purok', $this->filterPurok)));

        $users = $query->orderBy('full_name', 'asc')->paginate(15);

        // Unbound count
        $unboundQuery = User::where('role', 'patient')
            ->where('status', '!=', 'archived')
            ->whereNull('patient_record_id');

        if (Auth::user()->role == 'staff' && $assignedPuroks->isNotEmpty()) {
            $unboundQuery->whereHas(
                'user_address',
                fn($q) => $q->whereIn('purok', $assignedPuroks)
            );
        }

        // Purok dropdown — scoped to assigned areas for staff, all for nurse
        if (Auth::user()->role == 'staff') {
            $assignedAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', Auth::id())
                ->pluck('area_id');

            $puroks = brgy_unit::where('status', 'Active')
                ->whereIn('id', $assignedAreaIds)
                ->orderBy('brgy_unit')
                ->get();
        } else {
            $puroks = brgy_unit::where('status', 'Active')
                ->orderBy('brgy_unit')
                ->get();
        }

        return view('livewire.patient-account-binding', [
            'users'        => $users,
            'unboundCount' => $unboundQuery->count(),
            'puroks'       => $puroks,
        ]);
    }

    public function restoreUser(int $userId): void
    {
        $user = User::find($userId);

        if (!$user || $user->status !== 'archived') {
            $this->dispatch('restoreError', message: 'Patient account not found or is not archived.');
            return;
        }

        $nameConflict = User::where('role', 'patient')
            ->where('status', 'active')
            ->where('id', '!=', $userId)
            ->whereRaw('LOWER(first_name) = ?', [strtolower($user->first_name)])
            ->whereRaw('LOWER(last_name) = ?',  [strtolower($user->last_name)])
            ->exists();

        $emailConflict = User::where('role', 'patient')
            ->where('status', 'active')
            ->where('id', '!=', $userId)
            ->whereRaw('LOWER(email) = ?', [strtolower($user->email)])
            ->exists();

        if ($nameConflict || $emailConflict) {
            $reasons = [];
            if ($nameConflict) $reasons[] = 'name (' . $user->full_name . ')';
            if ($emailConflict) $reasons[] = 'email (' . $user->email . ')';

            $this->dispatch(
                'restoreError',
                message: 'An active account with the same ' . implode(' and ', $reasons) . ' already exists.'
            );
            return;
        }

        $user->status = 'active';
        $user->save();

        $this->dispatch('restoreSuccess');
    }

    public function openBindModal($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->showModal = true;
        $this->recordSearch = $this->selectedUser->full_name;
        $this->searchRecords();
    }

    public function searchRecords()
    {
        $this->patientRecords = [];

        $baseQuery = patients::whereNull('user_id')
            ->where('status', '!=', 'Archived')
            ->where(function ($query) {
                $searchTerm = str_replace(' ', '%', $this->recordSearch);
                $query->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('middle_initial', 'like', '%' . $searchTerm . '%')
                    ->orWhere('full_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('id', 'like', '%' . trim($this->recordSearch) . '%')
                    ->orWhere('contact_number', 'like', '%' . $this->recordSearch . '%');
            });

        if (Auth::user()->role == 'staff') {
            $patientIds = collect();

            foreach (['vaccination', 'prenatal', 'tb_dots', 'senior_citizen', 'family_planning'] as $type) {
                $patientIds = $patientIds->merge(
                    DB::table("{$type}_medical_records")
                        ->join('medical_record_cases', "{$type}_medical_records.medical_record_case_id", '=', 'medical_record_cases.id')
                        ->where("{$type}_medical_records.health_worker_id", Auth::id())
                        ->pluck('medical_record_cases.patient_id')
                );
            }

            $baseQuery->whereIn('id', $patientIds->unique()->values());
        }

        $this->patientRecords = $baseQuery->limit(20)->get();
    }

    public function updatedRecordSearch()
    {
        $this->searchRecords();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedUser = null;
        $this->recordSearch = '';
        $this->patientRecords = [];
        $this->selectedRecordId = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterPatientType()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterPurok()
    {
        $this->resetPage();
    }
}
