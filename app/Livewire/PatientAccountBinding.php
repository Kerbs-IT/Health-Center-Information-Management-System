<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\PatientRecord;
use App\Models\patients;
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

    public function render()
    {
        $users = User::where('role', 'patient')
            ->where('status', '!=', 'archived')  // <-- Move this OUTSIDE
            ->where(function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
                   
            })
            ->when($this->filterStatus === 'bound', function ($query) {
                $query->whereNotNull('patient_record_id');
            })
            ->when($this->filterStatus === 'unbound', function ($query) {
                $query->whereNull('patient_record_id');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unboundCount = User::where('role', 'patient')
            ->where('status', '!=', 'archived')  // <-- Add here too
            ->whereNull('patient_record_id')
            ->count();

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
        $this->patientRecords = patients::whereNull('user_id')
            ->where('status', '!=', 'Archived')
            ->where(function ($query) {
                $searchTerm = str_replace(' ', '%', $this->recordSearch);

                $query->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('middle_initial', 'like', '%' . $searchTerm . '%')
                    ->orWhere('full_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('id', 'like', '%' . trim($this->recordSearch) . '%')
                    ->orWhere('contact_number', 'like', '%' . $this->recordSearch . '%');
            })
            ->limit(20)
            ->get();
    }

    public function bind()
    {
        if (!$this->selectedRecordId) {
            session()->flash('error', 'Please select a patient record.');
            return;
        }

        try {
            DB::beginTransaction();

            $record = patients::find($this->selectedRecordId);

            // Double check not already bound
            if ($record->user_id) {
                session()->flash('error', 'This record is already bound to another account.');
                DB::rollBack();
                return;
            }

            // Bind both ways
            $this->selectedUser->patient_record_id = $this->selectedRecordId;
            $this->selectedUser->save();

            $record->user_id = $this->selectedUser->id;
            $record->save();

            DB::commit();

            session()->flash('success', 'Account successfully bound to patient record!');
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Binding failed: ' . $e->getMessage());
        }
    }

    public function unbind($userId)
    {
        try {
            DB::beginTransaction();

            $user = User::find($userId);
            if ($user->patient_record_id) {
                $record = patients::find($user->patient_record_id);
                if ($record) {
                    $record->user_id = null;
                    $record->save();
                }

                $user->patient_record_id = null;
                $user->save();
            }

            DB::commit();
            session()->flash('success', 'Account unbound successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Unbind failed: ' . $e->getMessage());
        }
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
}
