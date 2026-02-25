<?php

namespace App\Http\Controllers;

use App\Models\brgy_unit;
use App\Models\patients;
use App\Models\staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PatientRecord extends Controller
{
    //

    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $patientId = $request->input('patient_id', null);

        try {
            // Build base query
            $query = patients::where('status', 'Active')
                ->with(['medical_record_case' => function ($q) {
                    $q->where('status', 'Active');
                }, 'address', 'user']);

            // If searching by ID, return that specific patient
            if ($patientId) {
                $patient = $query->where('id', $patientId)->first();
                return response()->json($patient ? [$patient] : []);
            }

            // If no search term, return empty
            if (empty($search)) {
                return response()->json([]);
            }

            // Search by name
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "{$search}%")
                    ->orWhere('first_name', 'LIKE', "{$search}%")
                    ->orWhere('last_name', 'LIKE', "{$search}%")
                    ->orWhere('id', $search);
            });

            // Filter by staff assigned area
            if (Auth::user()->role === 'staff') {
                $staff = staff::findOrFail(Auth::user()->id);
                $assignedArea = brgy_unit::where('id', $staff->assigned_area_id)->first();

                if ($assignedArea) {
                    $query->whereHas('address', function ($q) use ($assignedArea) {
                        $q->where('purok', $assignedArea->brgy_unit);
                    });
                }
            }

            $patients = $query->limit(50)->get();

            return response()->json($patients);
        } catch (\Exception $e) {
            Log::error('Patient search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}
