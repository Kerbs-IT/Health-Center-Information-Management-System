<?php

namespace App\Http\Controllers;

use App\Models\addresses;
use App\Models\nurses;
use App\Models\staff;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class healthWorkerController extends Controller
{   
    public function dashboard(){

        $healthWorker = user::where('status', 'active') -> where('role','staff') -> orderBy('id', 'ASC')-> paginate(10);
        $pendingAccounts = user::where('status','pending') -> where('role', 'staff') -> orderBy('id', 'ASC') -> get();
        $pendingAccountsCount = user::where('status', 'pending') -> count();
        // get occupied areas
        $occupiedAreas = \Illuminate\Support\Facades\DB::table('users')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->where('users.status', 'active')
            ->pluck('staff.assigned_area_id')
            ->toArray();

        return view('dashboard.healthWorker',['isActive' => true,
                                                'healthWorker' => $healthWorker,
                                                'pendingAccounts' => $pendingAccounts, 
                                                'pendingAccountsCount' => $pendingAccountsCount,
                                                'occupied_assigned_areas' => $occupiedAreas ]);
    }
    public function destroy($id){
        
        $user = User::findOrFail($id);
        $user -> delete();

        return response()->json(['message' => 'Health worker deleted successfully.']);
    }

    public function getInfo($id){
        $user = User::findOrFail($id);

        $healthWorker = Staff::findOrFail($id);
        $address = addresses::findOrFail($healthWorker -> address_id);
        // Convert both to arrays and merge
        $combined = array_merge($user->toArray(), $healthWorker->toArray(), $address -> toArray());

        return response() -> json(['response' => $combined]);
    }
    public function addHealthWorker(Request $request){

        try{
            $data = $request->validate([
                'username' => 'required',
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'role' => 'required|in:staff,patient',
                'first_name' => 'required',
                'middle_initial' => ['required', 'max:2'],
                'last_name' => ['required'],
                'assigned_area' => 'required',
                'recovery_question' => ['required'],
                'recovery_answer' => ['required'],

            ]);

            $data['recovery_answer'] = Hash::make($data['recovery_answer']);
            $data['password'] = Hash::make($data['password']);

            switch ($data['role']) {
                case 'nurse':
                case 'staff':
                    $data['status'] = 'pending';  // Needs admin or nurse approval
                    break;

                case 'patient':
                    $data['status'] = 'active';   // Patients get access immediately
                    break;

                default:
                    $data['status'] = 'pending';  // fallback
                    break;
            }
            $newUser = User::create($data);
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

            staff::create([
                'user_id' => $userId,
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'],
                'last_name' => $data['last_name'],
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'assigned_area_id' => $data['assigned_area'],
                'address_id' => $address->address_id,
                'profile_image' => 'images/default_profile.png',
                'age' => null,
                'date_of_birth' => null,
                'sex' => null,
                'civil_status' => null,
                'contact_number' => null,
                'nationality' => null,
            ]);

            return response()->json(['message' => 'New Health Worker has been added'],201);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
       
    }
    public function update(Request $request, $id){
        
        try{
            $user = User::findOrFail($id);
            $data = $request -> validate([
                'first_name' => 'sometimes|nullable|string',
                'last_name' => 'sometimes|nullable|string',
                'middle_initial' => 'sometimes|nullable|string|max:2',
                'age' => 'sometimes|nullable|numeric',
                'date_of_birth' => 'sometimes|nullable|date',
                'sex' => 'sometimes|nullable|string',
                'civil_status' => 'sometimes|nullable|string',
                'contact_number' => 'sometimes|nullable|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'username' => 'required',
                'email' => ['required','email'],
                'street' => 'sometimes|nullable|string',
                'region' => 'sometimes|nullable|string',
                'province' => 'sometimes|nullable|numeric',
                'city' => 'sometimes|nullable|numeric',
                'brgy' => 'sometimes|nullable|numeric',
                'postal_code' => 'sometimes|nullable|numeric',
                'password' => ['sometimes', 'nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'profile_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
                'update_assigned_area' => 'required'
            ]);

            if(!empty($data['password'])){
                $user -> update([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']) // hash the password
                ]);
            }else{
                $user -> update([
                    'username' => $data['username'],
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
                $profileImagePath = $user -> role == 'staff'? $user -> staff: $user -> nurses;
                if (!empty( $profileImagePath ->profile_image) && $profileImagePath->profile_image !== 'images/default_profile.png') {
                    $oldImagePath = public_path(ltrim($profileImagePath -> profile_image,'/'));
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete old file
                    }
                }

                // Move the new file
                $file->move($destinationPath, $filename);

                // Update staff profile image path in DB
            
                $user-> staff ->profile_image = 'images/profile_images/' . $filename;
                $user->staff ->save();
                    
                
            }


            // update the address table

            $user -> addresses -> update([
                // the address? is a nullsafety operatory (?) to check if there is a value or null that we not cause an error
                'brgy_id' => $data['brgy'] ?? $user -> addresses?-> brgy_id ?? null,
                'city_id' => $data['city'] ?? $user -> addresses?-> city_id ?? null,
                'province_id' => $data['province'] ?? $user -> addresses?-> province_id ?? null,
                'region_id' => $data['region'] ?? $user -> addresses?-> region_id ?? null,
                'street' => $data['street'] ?? $user -> addresses?-> street ?? null,
                'postal_code' => $data['postal_code'] ?? $user -> addresses?-> postal_code ?? null
            ]);


            $staff = $user-> staff;
            $nurse = $user -> nurses;

            // dd($staff);
            
            $staff -> update([
                'first_name' => $data['first_name']?? null,
                'middle_initial' => $data['middle_initial']?? null,
                'last_name' => $data['last_name']?? null,
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'age' => $data['age'],
                'date_of_birth' => $data['date_of_birth']?? null,
                'sex' => $data['sex']?? null,
                'civil_status' => $data['civil_status']?? null,
                'contact_number' => $data['contact_number']?? null,
                'nationality' => $data['nationality']?? null,
                'assigned_area_id' => $data['update_assigned_area']
            ]);

            return response() -> json(['success' => 'Staff information has been updated']);
        }catch(ValidationException $e){
            return response() -> json([
                'errors' => $e -> errors()
            ],422);
        }
    }
}
