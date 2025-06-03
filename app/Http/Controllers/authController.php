<?php

namespace App\Http\Controllers;

use App\Models\addresses;
use App\Models\nurses;
use App\Models\staff;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RealRashid\SweetAlert\Facades\Alert;

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
            'role' => 'required|in:nurse,staff,other_roles',
            'first_name' => 'required',
            'middle_initial' => ['required','max:2'],
            'last_name' => ['required'],
    
            'department' => 'required_if:role,nurse',
            'assigned_area' => 'required_if:role,staff',
        ]);

       $data['password'] = Hash::make($data['password']);
       $newUser = User::create($data);

       $userId = $newUser-> id;

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
       }

       Alert::success('Congrats', 'Registration Succesfully');

        return redirect() -> route('register') -> with('reg_success',true);

    }
    
}
