<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class masterListController extends Controller
{
    public function viewVaccinationMasterList(){
        return view('masterlist.vaccination',['isActive' => true, 'page' => 'VACCINATION', 'pageHeader' => 'MASTERLIST']);
    }
    public function viewWRAMasterList(){
        return view('masterlist.wra', ['isActive' => true, 'page' => 'WOMEN OF REPRODUCTIVE AGE', 'pageHeader' => 'MASTERLIST']);
    }
}
