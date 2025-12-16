<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientList extends Controller
{
    //

    public function index(){
        return view('patient-list.patient-list',['isActive' => true, 'page' => 'PATIENT LIST']);
    }
}
