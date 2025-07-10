<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class patientController extends Controller
{
    public function dashboard(){
        return view('dashboard.patient', ['isActive' => true,'page'=> 'DASHBOARD']);
    }
    public function medicalRecord(){
        return view('patient-info.patient-records', ['isActive' => true, 'page' => 'RECORD'] );
    }
}
