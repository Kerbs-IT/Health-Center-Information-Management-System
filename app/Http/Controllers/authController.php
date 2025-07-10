<?php

namespace App\Http\Controllers;

use App\Models\addresses;
use App\Models\nurses;
use App\Models\patients;
use App\Models\staff;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class authController extends Controller
{
    //

    public function login(){
        return view('auth.login');
    }

    public function register(){

        return view('auth.register');
    }
    public function store(Request $request){

        $data = $request -> validate([
            'username'=> 'required',
            'email' => ['required','email'],
            'password' => ['required','confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'role' => 'required|in:staff,patient',
            'first_name' => 'required',
            'middle_initial' => ['required','max:2'],
            'last_name' => ['required'],
            'department' => 'required_if:role,nurse',
            'assigned_area' => 'required_if:role,staff',
            'recovery_question' => ['required'],
            'recovery_answer' => ['required'],
            'patient_type' => 'required_if:role,patient',

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

       $userId = $newUser -> id;

       $address = addresses::create([
            'user_id' => $userId,
            'street' => null,
            'brgy_id' => null,
            'city_id' => null,
            'province_id' => null,
            'region_id'=> null,
            'postal_code'=> null,
            'role' => $newUser -> role
       ]);

       switch($newUser->role){
        case 'nurse':
            nurses::create([
                'user_id' => $userId,
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'],
                'last_name' => $data['last_name'],
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'address_id' => $address -> address_id,
                'department_id' => $data['department'],
                'profile_image' => 'images/default_profile.png',
                'age' => null,
                'date_of_birth' => null,
                'sex' => null,
                'civil_status' => null,
                'contact_number' => null,
                'nationality' => null,        
            ]);
            break;
        case 'staff':
           staff::create([
                'user_id' => $userId,
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'],
                'last_name' => $data['last_name'],
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'assigned_area_id' => $data['assigned_area'],
                'address_id' => $address -> address_id,
                'profile_image' => 'images/default_profile.png',
                'age' => null,
                'date_of_birth' => null,
                'sex' => null,
                'civil_status' => null,
                'contact_number' => null,
                'nationality' => null,
           ]);
           break;
        case 'patient':{
            patients::create([
                'user_id' => $userId,
                'patient_type' => $data['patient_type'],
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'],
                'last_name' => $data['last_name'],
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'address_id' => $address -> address_id,
                'profile_image' => 'images/default_profile.png',
                'age' => null,
                'date_of_birth' => null,
                'sex' => null,
                'civil_status' => null,
                'contact_number' => null,
                'nationality' => null,
           ]);
           break;
        }
           break;
       }

       Alert::success('Congrats', 'Registration Succesfully');

        return redirect() -> route('register') -> with('reg_success',true);

    }

    public function update(Request $request){
        $user = Auth::user();
        $data = $request -> validate([
            'first_name' => 'sometimes|nullable|string',
            'last_name' => 'sometimes|nullable|string',
            'middle_initial' => 'sometimes|nullable|string',
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
            'profile_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
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
            if (!empty( $profileImagePath ->profile_image)) {
                $oldImagePath = public_path(ltrim($profileImagePath -> profile_image,'/'));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete old file
                }
            }

            // Move the new file
            $file->move($destinationPath, $filename);

            // Update user profile image path in DB
            switch($user-> role){
                case 'staff':
                    $user-> staff ->profile_image = 'images/profile_images/' . $filename;
                    $user->staff ->save();
                    break;
                case 'nurse':
                    $user-> nurses ->profile_image = 'images/profile_images/' . $filename;
                    $user-> nurses ->save();
                    break;

            }
            
        }


        // update the address table

        $user -> addresses -> update([
            'brgy_id' => $data['brgy'] ?? $user -> addresses -> brgy_id ?? null,
            'city_id' => $data['city'] ?? $user -> addresses -> city_id ?? null,
            'province_id' => $data['province'] ?? $user -> addresses -> province_id ?? null,
            'region_id' => $data['region'] ?? $user -> addresses -> region_id ?? null,
            'street' => $data['street'] ?? $user -> addresses -> street ?? null,
            'postal_code' => $data['postal_code'] ?? $user -> addresses -> postal_code ?? null
        ]);


        $staff = $user-> staff;
        $nurse = $user -> nurses;

        // dd($staff);
        switch(Auth::user() -> role){
            case 'staff':
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
                ]);


                break;
            case 'nurse':
                $nurse -> update([
                    'first_name' => $data['first_name']?? null,
                    'middle_initial' => $data['middle_initial']?? null,
                    'last_name' => $data['last_name']?? null,
                    'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                    'age' => $data['age']?? null,
                    'date_of_birth' => $data['date_of_birth']?? null,
                    'sex' => $data['sex']?? null,
                    'civil_status' => $data['civil_status']?? null,
                    'contact_number' => $data['contact_number']?? null,
                    'nationality' => $data['nationality']?? null,
                ]);

        }


        Alert::success('Congrats', 'Profile updated successfully');


        return redirect() -> route('page.profile');

    }

    public function updateStatus($id, $decision){
        $user = User::where('id', $id);
        if($decision == 'accept'){
            $user -> update([
                'status' => 'active'
            ]);
        }
        if ($decision == 'reject') {
            $user->update([
                'status' => 'reject'
            ]);
        }
    }
    
}
