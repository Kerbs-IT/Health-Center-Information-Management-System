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
            ->where('status','active')
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

            // Use whereHas to filter through the relationship
            $query->whereHas('user_address', function ($q) use ($assignedArea) {
                $q->where('purok', $assignedArea->brgy_unit);
            });
        }

        $users = $query->limit(50)->get();

        return response()->json($users);
    }

    public function searchGuardian(Request $request){
        $search = $request->input('search', '');

        $query = User::with("user_address")
            ->where('status','active')
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

            // Use whereHas to filter through the relationship
            $query->whereHas('user_address', function ($q) use ($assignedArea) {
                $q->where('purok', $assignedArea->brgy_unit);
            });
        }

        $users = $query->limit(50)->get();

        return response()->json($users);
    }
}
