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
    public $filterStatus = 'all';
    public $filterPatientType = 'all';
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

        if (Auth::user()->role == 'staff') {
            $staffInfo = Staff::where('user_id', Auth::id())->first();
            if ($staffInfo && $staffInfo->assigned_area_id) {
                $assignedArea = brgy_unit::findOrFail($staffInfo->assigned_area_id);
                $query->join('users_addresses', 'users.id', '=', 'users_addresses.user_id')
                    ->where('users_addresses.purok', $assignedArea->brgy_unit)
                    ->select('users.*');
            }
        }

        $query->where(function ($q) {
            $q->where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        });

        $query->when($this->filterStatus === 'active',   fn($q) => $q->where('status', 'active'));
        $query->when($this->filterStatus === 'archived', fn($q) => $q->where('status', 'archived'));
        $query->when($this->filterPatientType !== 'all', fn($q) => $q->where('patient_type', $this->filterPatientType));

        $users = $query->orderBy('users.created_at', 'asc')->paginate(15);

        $unboundQuery = User::where('role', 'patient')
            ->where('status', '!=', 'archived')
            ->whereNull('patient_record_id');

        if (Auth::user()->role == 'staff') {
            $staffInfo = Staff::where('user_id', Auth::id())->first();
            if ($staffInfo && $staffInfo->assigned_area_id) {
                $assignedArea = brgy_unit::findOrFail($staffInfo->assigned_area_id);
                $unboundQuery->join('users_addresses', 'users.id', '=', 'users_addresses.user_id')
                    ->where('users_addresses.purok', $assignedArea->brgy_unit);
            }
        }

        return view('livewire.patient-account-binding', [
            'users'        => $users,
            'unboundCount' => $unboundQuery->count(),
        ]);
    }

    public function restoreUser(int $userId): void
    {
        $user = User::find($userId);

        if (!$user || $user->status !== 'archived') {
            $this->dispatch('restoreError', message: 'Patient account not found or is not archived.');
            return;
        }

        // Check duplicate name
        $nameConflict = User::where('role', 'patient')
            ->where('status', 'active')
            ->where('id', '!=', $userId)
            ->whereRaw('LOWER(first_name) = ?', [strtolower($user->first_name)])
            ->whereRaw('LOWER(last_name) = ?',  [strtolower($user->last_name)])
            ->exists();

        // Check duplicate email
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
            $staffId = Auth::id();
            $patientIds = collect();
            foreach (['vaccination', 'prenatal', 'tb_dots', 'senior_citizen', 'family_planning'] as $type) {
                $patientIds = $patientIds->merge(
                    DB::table("{$type}_medical_records")
                        ->join('medical_record_cases', "{$type}_medical_records.medical_record_case_id", '=', 'medical_record_cases.id')
                        ->where("{$type}_medical_records.health_worker_id", $staffId)
                        ->pluck('medical_record_cases.patient_id')
                );
            }
            $baseQuery->whereIn('id', $patientIds->unique()->values());
        }

        $this->patientRecords = $baseQuery->limit(20)->get();
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
}
