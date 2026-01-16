<?php

namespace App\Http\Controllers;

use App\Models\addresses;
use App\Models\brgy_unit;
use App\Models\nurses;
use App\Models\staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class healthWorkerController extends Controller
{
    public function dashboard()
    {

        $healthWorker = user::where('status', 'active')->where('role', 'staff')->orderBy('id', 'ASC')->paginate(10);
        $pendingAccounts = user::where('status', 'pending')->where('role', 'staff')->orderBy('id', 'ASC')->get();
        $pendingAccountsCount = user::where('status', 'pending')->count();
        // get occupied areas
        $occupiedAreas = \Illuminate\Support\Facades\DB::table('users')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->where('users.status', 'active')
            ->pluck('staff.assigned_area_id')
            ->toArray();

        return view('dashboard.healthWorker', [
            'isActive' => true,
            'healthWorker' => $healthWorker,
            'pendingAccounts' => $pendingAccounts,
            'pendingAccountsCount' => $pendingAccountsCount,
            'occupied_assigned_areas' => $occupiedAreas,
            'page' => 'Health worker'
        ]);
    }
    public function destroy($id)
    {

        $user = User::findOrFail($id);
        $user->update([
            'status' => 'archived'
        ]);

        return response()->json(['message' => 'Health worker deleted successfully.']);
    }

    public function getInfo($id)
    {
        $user = User::findOrFail($id);

        $healthWorker = Staff::findOrFail($id);
        $address = addresses::findOrFail($healthWorker->address_id);
        // Convert both to arrays and merge
        $combined = array_merge($user->toArray(), $healthWorker->toArray(), $address->toArray());

        return response()->json(['response' => $combined]);
    }
    public function addHealthWorker(Request $request)
    {

        try {
            $data = $request->validate([

                'email' => 'required|email|unique:users,email',
                'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'role' => 'required|in:staff,patient',
                'first_name' => ['required', Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('first_name', $request->first_name)
                        ->where('last_name', $request->last_name);
                })],
                'middle_initial' => 'sometimes|nullable|string',
                'last_name' => ['required'],
                'assigned_area' => 'required',
                'add_date_of_birth' => 'required|date',
                'add_contact_number' => 'required|numeric|digits_between:7,12',
                'add_suffix' => 'sometimes|nullable|string',

            ]);



            $data['status'] = 'active';
            $data['password'] = Hash::make($data['password']);

            $middleInitial = $data['middle_initial'] ? ucwords(strtolower($data['middle_initial'])) : null;
            $middle = substr($data['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $newUser = User::create([

                'first_name' => ucwords(strtolower($data['first_name'])),
                'middle_initial' => $middleInitial,
                'last_name' => ucwords(strtolower($data['last_name'])),
                'patient_type' => 'none',
                'email' => $data['email'],
                'date_of_birth' => $data['add_date_of_birth'],
                'contact_number' => $data['add_contact_number'] ?? null,
                'password' => $data['password'],
                'role' => 'staff',
                'status' => $data['status'], // Mark as pending until verified
                'is_verified' => true,
                'suffix' => $data['add_suffix'] ?? null
            ]);
            $userId = $newUser->id;

            // address
            $address = addresses::create([
                'user_id' => $userId,
                'street' => null,
                'brgy_id' => null,
                'city_id' => null,
                'province_id' => null,
                'region_id' => null,
                'postal_code' => null,
                'role' => $newUser->role
            ]);

            // full name

            $parts = [
                strtolower($data['first_name']),
                $middle,
                strtolower($data['last_name']),
                $data['add_suffix'] ?? null
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));

            $age = Carbon::parse($data['add_date_of_birth'])->age;

            staff::create([
                'user_id' => $userId,
                'first_name' => ucwords(strtolower($data['first_name'])),
                'middle_initial' => $middleInitial,
                'last_name' => ucwords(strtolower($data['last_name'])),
                'full_name' => $fullName,
                'assigned_area_id' => $data['assigned_area'],
                'address_id' => $address->address_id,
                'profile_image' => 'images/default_profile.png',
                'age' => $age ?? null,
                'date_of_birth' => $data['add_date_of_birth'] ?? null,
                'sex' => null,
                'civil_status' => null,
                'contact_number' => $data['add_contact_number'],
                'nationality' => null,
                'suffix' => $data['add_suffix'] ?? null
            ]);

            return response()->json(['message' => 'New Health Worker has been added'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function update(Request $request, $id)
    {

        try {
            $user = User::findOrFail($id);
            $data = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'middle_initial' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric',
                'date_of_birth' => 'sometimes|nullable|date',
                'sex' => 'sometimes|nullable|string',
                'civil_status' => 'sometimes|nullable|string',
                'contact_number' => 'required|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',

                'email' => ['required', 'email'],
                'street' => 'sometimes|nullable|string',
                'region' => 'sometimes|nullable|string',
                'province' => 'sometimes|nullable|numeric',
                'city' => 'sometimes|nullable|numeric',
                'brgy' => 'sometimes|nullable|numeric',
                'postal_code' => 'sometimes|nullable|numeric',
                'password' => ['sometimes', 'nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'profile_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

                'edit_suffix' => 'sometimes|nullable|string'
            ]);

            if (!empty($data['password'])) {
                $user->update([

                    'email' => $data['email'],
                    'password' => Hash::make($data['password']) // hash the password
                ]);
            } else {
                $user->update([

                    'email' => $data['email'],
                ]);
            }


            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');

                // Generate unique filename
                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('images/profile_images');

                // Make sure the folder exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Delete the old image if it exists and is different
                $profileImagePath = $user->role == 'staff' ? $user->staff : $user->nurses;
                if (!empty($profileImagePath->profile_image) && $profileImagePath->profile_image !== 'images/default_profile.png') {
                    $oldImagePath = public_path(ltrim($profileImagePath->profile_image, '/'));
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete old file
                    }
                }

                // Move the new file
                $file->move($destinationPath, $filename);

                // Update staff profile image path in DB

                $user->staff->profile_image = 'images/profile_images/' . $filename;
                $user->staff->save();
            }


            // update the address table

            $user->addresses->update([
                // the address? is a nullsafety operatory (?) to check if there is a value or null that we not cause an error
                'brgy_id' => $data['brgy'] ?? $user->addresses?->brgy_id ?? null,
                'city_id' => $data['city'] ?? $user->addresses?->city_id ?? null,
                'province_id' => $data['province'] ?? $user->addresses?->province_id ?? null,
                'region_id' => $data['region'] ?? $user->addresses?->region_id ?? null,
                'street' => $data['street'] ?? $user->addresses?->street ?? null,
                'postal_code' => $data['postal_code'] ?? $user->addresses?->postal_code ?? null
            ]);


            $staff = $user->staff;
            $nurse = $user->nurses;

            $middle = substr($data['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $middleInitial = $data['middle_initial'] ? ucwords($data['middle_initial']) : '';
            $parts = [
                strtolower($data['first_name']),
                $middle,
                strtolower($data['last_name']),
                $data['edit_suffix'] ?? ''
            ];


            $fullName = ucwords(trim(implode(' ', array_filter($parts))));


            // dd($staff);
            $user->update([
                'first_name' => ucwords(strtolower($data['first_name'])),
                'middle_initial' => $middleInitial,
                'last_name' => ucwords(strtolower($data['last_name'])),
                'suffix' => $data['edit_suffix'] ?? $user->suffix
            ]);

            $staff->update([
                'first_name' => ucwords(strtolower($data['first_name'])),
                'middle_initial' => $middleInitial,
                'last_name' => ucwords(strtolower($data['last_name'])),
                'full_name' => $fullName,
                'suffix' => $data['edit_suffix'] ?? $staff->suffix,
                'age' => $data['age'],
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'sex' => $data['sex'] ?? null,
                'civil_status' => $data['civil_status'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'nationality' => $data['nationality'] ?? null,
            ]);

            return response()->json(['success' => 'Staff information has been updated']);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function healthWorkerList()
    {

        try {
            $healthWorkers = User::select('users.*')
                ->join('staff', 'users.id', '=', 'staff.user_id')
                ->where('users.role', 'staff')
                ->where('users.status', 'active')
                ->orderBy('staff.full_name', 'ASC')
                ->with('staff')
                ->get();

            return response()->json(['healthWorkers' => $healthWorkers]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e
            ]);
        }
    }

    public function getAssignedArea($staffId)
    {
        try {
            $staff = staff::where("user_id", $staffId)->first();

            $assignedArea = brgy_unit::where("id", $staff->assigned_area_id)->first();

            return response()->json([
                'assigned_area' => $assignedArea->brgy_unit
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 402);
        }
    }
    public function getHealthWorker(Request $request){
        try{
            $purok = $request->get("assigned_area");
            $assignedAreaId = brgy_unit::where("brgy_unit", $purok)->pluck("id");
            $healthWorkerId = staff::where("assigned_area_id", $assignedAreaId)->pluck("user_id")->first();
            return response()->json([
                'health_worker_id' => $healthWorkerId
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 402);
        }
       
    }
}
