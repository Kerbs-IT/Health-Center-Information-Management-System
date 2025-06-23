<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecordsController extends Controller
{
    // vaccination
    public function vaccinationRecord(){
        return view('records.vaccination.vaccination', ['isActive' => true]);
    }
    public function viewDetails(){

        return view('records.vaccination.patientDetails', ['isActive' => true]);
    }
    public function vaccinationEditDetails(){

        return view('records.vaccination.editPatientDetails',['isActive' => true]);
    }
    public function vaccinationCase(){
        return view('records.vaccination.patientCase', ['isActive' => true]);
    }
    // prenatal
    public function prenatalRecord(){
        return view('records.prenatal.prenatal', ['isActive' => true]);
    }
    public function viewPrenatalDetail(){
        return view('records.prenatal.viewPatientDetails', ['isActive' => true]);
    }
}
