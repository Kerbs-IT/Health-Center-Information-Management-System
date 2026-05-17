<?php

namespace App\Livewire;

use App\Models\staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ArchivedHealthWorker extends Component
{
    use WithPagination;

    public $search = '';
    public $entries = 10;

    protected $listeners = ['archivedHealthWorkerRefresh' => '$refresh'];

    public function render()
    {
        $archivedWorkers = User::where('status', 'archived')
            ->where('role', 'staff')
            ->when($this->search, function ($query) {
                $query->whereHas('staff', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('contact_number', 'like', '%' . $this->search . '%');
                });
            })
            ->with('staff.assigned_areas') // ← updated from assigned_area
            ->orderBy('updated_at', 'DESC')
            ->paginate($this->entries);

        return view('livewire.archived-health-worker', [
            'archivedWorkers' => $archivedWorkers,
        ]);
    }

    public function restoreHealthWorker($userId)
    {
        DB::beginTransaction();
        try {
            $staff = Staff::where('user_id', $userId)->firstOrFail();
            $user  = User::findOrFail($userId);

            // Get this worker's previously assigned areas from pivot
            $workerAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', $userId)
                ->pluck('area_id')
                ->toArray();

            // Check if ANY of their areas are now taken by an active worker
            if (!empty($workerAreaIds)) {
                $conflictingAreas = DB::table('staff_area_assignments')
                    ->join('staff', 'staff_area_assignments.staff_id', '=', 'staff.user_id')
                    ->join('users', 'users.id', '=', 'staff.user_id')
                    ->whereIn('staff_area_assignments.area_id', $workerAreaIds)
                    ->where('users.status', 'active')
                    ->where('staff.status', 'Active')
                    ->where('staff_area_assignments.staff_id', '!=', $userId)
                    ->pluck('staff_area_assignments.area_id')
                    ->toArray();

                if (!empty($conflictingAreas)) {
                    // Get area names for the error message
                    $areaNames = DB::table('brgy_unit')
                        ->whereIn('id', $conflictingAreas)
                        ->pluck('brgy_unit')
                        ->implode(', ');

                    $this->dispatch(
                        'restorationError',
                        message: "Cannot restore. The following areas are already assigned to active health workers: {$areaNames}"
                    );

                    DB::rollBack();
                    return;
                }
            }

            // Restore the user and staff
            $user->update(['status' => 'active']);
            $staff->update(['status' => 'Active']);

            // Transfer records from OTHER archived staff in the same areas
            if (!empty($workerAreaIds)) {
                foreach ($workerAreaIds as $areaId) {
                    $this->transferFromArchivedStaff($userId, $areaId);
                }
            }

            DB::commit();

            $this->dispatch(
                'healthWorkerRestored',
                message: "Health worker has been restored successfully."
            );
            $this->dispatch('archivedHealthWorkerRefresh');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch(
                'restorationError',
                message: 'Failed to restore: ' . $e->getMessage()
            );
        }
    }

    private function transferFromArchivedStaff($restoredStaffUserId, $areaId)
    {
        // Find OTHER archived staff who had this area (exclude the one being restored)
        $archivedStaffIds = DB::table('staff_area_assignments')
            ->join('staff', 'staff_area_assignments.staff_id', '=', 'staff.user_id')
            ->join('users', 'users.id', '=', 'staff.user_id')
            ->where('users.status', 'archived')
            ->where('staff_area_assignments.area_id', $areaId)
            ->where('staff_area_assignments.staff_id', '!=', $restoredStaffUserId)
            ->pluck('staff_area_assignments.staff_id')
            ->toArray();

        if (empty($archivedStaffIds)) {
            return;
        }

        $tables = [
            'family_planning_case_records',
            'family_planning_medical_records',
            'family_planning_side_b_records',
            'pregnancy_checkups',
            'prenatal_case_records',
            'prenatal_medical_records',
            'senior_citizen_case_records',
            'senior_citizen_medical_records',
            'tb_dots_case_records',
            'tb_dots_medical_records',
            'tb_dots_check_ups',
            'vaccination_case_records',
            'vaccination_masterlists',
            'vaccination_medical_records',
            'wra_masterlists',
        ];

        foreach ($tables as $table) {
            DB::table($table)
                ->whereIn('health_worker_id', $archivedStaffIds)
                ->update(['health_worker_id' => $restoredStaffUserId]);
        }
    }
}
