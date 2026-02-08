<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PatientAccountController extends Controller
{
    public function search(Request $request){
        $search = $request ->input('search','');

        $users = User::with("user_address")
            ->whereNull('patient_record_id')
            ->where('role', 'patient')
            ->where(function ($query) use ($search) {
                $query->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%");
                   
            })
            ->limit(50)  // Add limit for performance
            ->get();

        return response()->json($users);

    }
}
