<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManageHealthWorker extends Component
{
    protected $listeners = ['healthWorkerTableRefresh' => '$refresh'];

    public function render()
    {
        $healthWorker = User::with('staff.assigned_areas')  // ← change here
            ->where('role', 'staff')
            ->where('status', 'active')
            ->paginate(10);

        // get occupied areas
        $occupiedAreas = DB::table('users')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->where('users.status', 'active')
            ->pluck('staff.assigned_area_id')
            ->toArray();

        return view('livewire.manage-health-worker', [
            'healthWorker' => $healthWorker,
            'occupied_assigned_areas' => $occupiedAreas,
        ]);
    }
}
