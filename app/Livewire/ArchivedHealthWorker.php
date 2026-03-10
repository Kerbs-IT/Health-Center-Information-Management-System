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
            ->orderBy('updated_at', 'DESC')
            ->paginate($this->entries);

        return view('livewire.archived-health-worker', [
            'archivedWorkers' => $archivedWorkers,
        ]);
    }
    /**
     * Restore archived health worker
     */
    /**
     * Restore archived health worker
     */
    public function restoreHealthWorker($userId)
    {
        DB::beginTransaction();
        try {
            $staff = Staff::where('user_id', $userId)->firstOrFail();
            $user = User::findOrFail($userId);

            // Check if area is already occupied by active staff
            $areaOccupied = DB::table('users')
                ->join('staff', 'users.id', '=', 'staff.user_id')
                ->where('users.status', 'active')
                ->where('staff.assigned_area_id', $staff->assigned_area_id)
                ->exists();

            if ($areaOccupied) {
                // ✅ SIMPLE MESSAGE
                $this->dispatch(
                    'restorationError',
                    message: "Cannot restore. There's an active health worker with the same assigned area."
                );

                DB::rollBack();
                return;
            }

            // Restore the staff
            $user->status = 'active';
            $user->save();

            // Transfer records from OTHER archived staff in this area
            $this->transferFromArchivedStaff($userId, $staff->assigned_area_id);

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
                message: 'Failed to restore health worker.'
            );
        }
    }

    /**
     * Transfer records from other archived staff in the same area
     */
    private function transferFromArchivedStaff($restoredStaffUserId, $areaId)
    {
        // Find OTHER archived staff who had this area (exclude the one being restored)
        $archivedStaffIds = DB::table('users')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->where('users.status', 'archived')
            ->where('staff.assigned_area_id', $areaId)
            ->where('users.id', '!=', $restoredStaffUserId)
            ->pluck('staff.user_id')
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
