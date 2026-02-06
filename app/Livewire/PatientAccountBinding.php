<?php

namespace App\Livewire;

use App\Models\brgy_unit;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\PatientRecord;
use App\Models\patients;
use App\Models\staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientAccountBinding extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all';
    public $showModal = false;
    public $selectedUser = null;
    public $recordSearch = '';
    public $patientRecords = [];
    public $selectedRecordId = null;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['manageUserRefreshTable'=> 'refresh'];

    public function render()
    {
        // Build the base query
        $query = User::where('role', 'patient')
                ->orderBy('status','asc');
            

        // Add restriction if the user is staff
        if (Auth::user()->role == 'staff') {
            $userId = Auth::user()->id;
            $staffInfo = Staff::where("user_id", $userId)->first();

            if ($staffInfo && $staffInfo->assigned_area_id) {
                $assignedArea = brgy_unit::findOrFail($staffInfo->assigned_area_id);

                // Join with user_addresses to filter by purok
                $query->join('users_addresses', 'users.id', '=', 'users_addresses.user_id')
                    ->where('users_addresses.purok', $assignedArea->brgy_unit)
                    ->select('users.*');
            }
        }

        // Apply search filter
        $query->where(function ($q) {
            $q->where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        });

        // Apply status filters
        $query->when($this->filterStatus === 'active', function ($q) {
            $q->where("status",'active');
        });

        $query->when($this->filterStatus === 'archived', function ($q) {
            $q->where('status', 'archived');
        });

        // Execute the query with pagination
        $users = $query->orderBy('users.created_at', 'asc')
            ->paginate(15);

        // Count unbound patients (apply same staff restriction)
        $unboundQuery = User::where('role', 'patient')
            ->where('status', '!=', 'archived')
            ->whereNull('patient_record_id');

        if (Auth::user()->role == 'staff') {
            $userId = Auth::user()->id;
            $staffInfo = Staff::where("user_id", $userId)->first();

            if ($staffInfo && $staffInfo->assigned_area_id) {
                $assignedArea = brgy_unit::findOrFail($staffInfo->assigned_area_id);
                $unboundQuery->join('users_addresses', 'users.id', '=', 'users_addresses.user_id')
                    ->where('users_addresses.purok', $assignedArea->brgy_unit);
            }
        }

        $unboundCount = $unboundQuery->count();

        return view('livewire.patient-account-binding', [
            'users' => $users,
            'unboundCount' => $unboundCount
        ]);
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
        // Add staff restriction
        if (Auth::user()->role == 'staff') {
            $staffId = Auth::id();

            // Get all patient IDs that this staff member has worked with
            $patientIds = collect();

            // Collect patient IDs from vaccination records
            $patientIds = $patientIds->merge(
                DB::table('vaccination_medical_records')
                    ->join('medical_record_cases', 'vaccination_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('vaccination_medical_records.health_worker_id', $staffId)
                    ->pluck('medical_record_cases.patient_id')
            );

            // Collect from prenatal records
            $patientIds = $patientIds->merge(
                DB::table('prenatal_medical_records')
                    ->join('medical_record_cases', 'prenatal_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('prenatal_medical_records.health_worker_id', $staffId)
                    ->pluck('medical_record_cases.patient_id')
            );

            // Collect from TB DOTS records
            $patientIds = $patientIds->merge(
                DB::table('tb_dots_medical_records')
                    ->join('medical_record_cases', 'tb_dots_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('tb_dots_medical_records.health_worker_id', $staffId)
                    ->pluck('medical_record_cases.patient_id')
            );

            // Collect from senior citizen records
            $patientIds = $patientIds->merge(
                DB::table('senior_citizen_medical_records')
                    ->join('medical_record_cases', 'senior_citizen_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('senior_citizen_medical_records.health_worker_id', $staffId)
                    ->pluck('medical_record_cases.patient_id')
            );

            // Collect from family planning records
            $patientIds = $patientIds->merge(
                DB::table('family_planning_medical_records')
                    ->join('medical_record_cases', 'family_planning_medical_records.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('family_planning_medical_records.health_worker_id', $staffId)
                    ->pluck('medical_record_cases.patient_id')
            );

            // Get unique patient IDs
            $uniquePatientIds = $patientIds->unique()->values();

            // Filter patients by these IDs
            $baseQuery->whereIn('id', $uniquePatientIds);
        }

        $this->patientRecords = $baseQuery->limit(20)->get();
    }

    // public function bind()
    // {
    //     if (!$this->selectedRecordId) {
    //         session()->flash('error', 'Please select a patient record.');
    //         return;
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $record = patients::find($this->selectedRecordId);

    //         // Double check not already bound
    //         if ($record->user_id) {
    //             session()->flash('error', 'This record is already bound to another account.');
    //             DB::rollBack();
    //             return;
    //         }

    //         // Bind both ways
    //         $this->selectedUser->patient_record_id = $this->selectedRecordId;
    //         $this->selectedUser->save();

    //         $record->user_id = $this->selectedUser->id;
    //         $record->save();

    //         DB::commit();

    //         session()->flash('success', 'Account successfully bound to patient record!');
    //         $this->closeModal();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         session()->flash('error', 'Binding failed: ' . $e->getMessage());
    //     }
    // }

    // public function unbind($userId)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $user = User::find($userId);
    //         if ($user->patient_record_id) {
    //             $record = patients::find($user->patient_record_id);
    //             if ($record) {
    //                 $record->user_id = null;
    //                 $record->save();
    //             }

    //             $user->patient_record_id = null;
    //             $user->save();
    //         }

    //         DB::commit();
    //         session()->flash('success', 'Account unbound successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         session()->flash('error', 'Unbind failed: ' . $e->getMessage());
    //     }
    // }

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
}
