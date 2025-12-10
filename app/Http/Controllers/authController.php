<?php

namespace App\Http\Controllers;

use App\Models\addresses;
use App\Models\nurses;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\staff;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class authController extends Controller
{
    //

    public function login(){
        return view('auth.login');
    }

    public function register(){
        $occupiedAreas = \Illuminate\Support\Facades\DB::table('users')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->where('users.status', 'active')
            ->pluck('staff.assigned_area_id')
            ->toArray();

        return view('auth.register',['occupied_assigned_areas' => $occupiedAreas]);
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
            'assigned_area' => 'required_if:role,staff',
            'recovery_question' => ['required'],
            'recovery_answer' => ['required'],
            'blk_n_street'=> 'required_if:role,patient',
            'patient_purok_dropdown' => 'required_if:role,patient'

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
            $patient = patients::create([
                'user_id' => $userId ?? null,
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'],
                'last_name' => $data['last_name'],
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'profile_image' => 'images/default_profile.png',
                'age' => null,
                'date_of_birth' => null,
                'sex' => null,
                'civil_status' => null,
                'contact_number' => null,
                'nationality' => null,
           ]);
        //    add the user address
            // dd($patient->id);
            $blk_n_street = explode(',' ,$data['blk_n_street']);
            // dd($blk_n_street);
            patient_addresses::create([
                'patient_id' => $patient->id,
                'house_number' => $blk_n_street[0] ?? $data['blk_n_street'],
                'street'=> $blk_n_street[1] ?? null,
                'purok' => $data['patient_purok_dropdown'],
                'postal_code' => '4109',
                'latitude' => null,
                'longitude' => null,
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
            // the address? is an nullsafe operator to safely check if there is a brgy_id or null
            'brgy_id' => $data['brgy'] ?? $user -> addresses?->brgy_id ?? null,
            'city_id' => $data['city'] ?? $user -> addresses?->city_id ?? null,
            'province_id' => $data['province'] ?? $user -> addresses?->province_id ?? null,
            'region_id' => $data['region'] ?? $user -> addresses?->region_id ?? null,
            'street' => $data['street'] ?? $user -> addresses?->street ?? null,
            'postal_code' => $data['postal_code'] ?? $user-> addresses?-> postal_code ?? null
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
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],

        ]);
        $remember = $request->has('remember');


        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $data = Auth::user();

            if (Auth::user()->status !== 'active') {
                $status = Auth::user()->status;
                Auth::logout(); // force logout if not active
                return back()->withErrors([
                    'email' => 'Your account is ' . $data->status  . '. Please wait for approval.',
                ])->onlyInput('email');
            }

            // gets the whole information of the user
            $role = $data->role; // this gets the role of the user from the table

            if ($remember) {
                Cookie::queue('last_email', $credentials['email']);
                Cookie::queue('last_password', $credentials['password']);
                Cookie::queue('remember_me', true, 60 * 24 * 30);
            } else {
                Cookie::queue('remember_me', false, 60 * 24 * 30);
            }


            switch ($role) {

                case 'patient':
                    return redirect()->route('dashboard.patient');
                case 'nurse':
                    return redirect()->route('dashboard.nurse');
                case 'staff':
                    return redirect()->route('dashboard.staff');
                default:
            }


            // return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
            'password' => 'The provided credentials do not match our records.',
        ])->onlyInput('email', 'password');
    }
    
}
