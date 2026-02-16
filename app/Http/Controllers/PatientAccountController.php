<?php

namespace App\Http\Controllers;

use App\Models\brgy_unit;
use App\Models\staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientAccountController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->input('search', '');

        $query = User::with("user_address")
            ->whereNull('patient_record_id')
            ->where('role', 'patient')
            ->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%");
            });

        // ── Health-worker scope ───────────────────────────────────────────
        if (Auth::user()->role === 'staff') {
            $staff = staff::findOrFail(Auth::user()->id);
            $assignedArea = brgy_unit::where('id', $staff->assigned_area_id)->first();

            // Join with user_address table to filter by purok
            $query->join('user_addresses', 'users.id', '=', 'user_addresses.user_id')
                ->where('user_addresses.purok', $assignedArea->brgy_unit)
                ->select('users.*'); // Ens ure only user columns are selected
        }

        $users = $query->limit(50)->get();

        return response()->json($users);
    }
}
