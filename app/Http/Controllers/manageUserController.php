<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class manageUserController extends Controller
{
    
    public function viewUsers(){
        $patients = User::where('role', 'patient') -> orderBy('id', 'ASC') -> get();
        return view('manageUsers.manageUsers',['isActive' => true, 'page' => 'MANAGE USERS', 'patients' => $patients]);
    }
}
