<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class addPatientController extends Controller
{
    public function dashboard(){
        return view('add_patient.add_patient', ['isActive' => true, 'page' => 'ADD PATIENT']);
    }
}
