<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ManageHealthWorker extends Component
{
    protected $listeners = ['healthWorkerTableRefresh' => '$refresh'];

    public function render()
    {
        $healthWorker = User::where('status', 'active')->where('role', 'staff')->orderBy('id', 'ASC')->paginate(10);

        // get occupied areas
        $occupiedAreas = \Illuminate\Support\Facades\DB::table('users')
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
