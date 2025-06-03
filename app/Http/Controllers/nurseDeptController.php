<?php

namespace App\Http\Controllers;

use App\Models\nurse_department;
use Illuminate\Http\Request;

class nurseDeptController extends Controller
{
    //
    public function show(){
        $department = nurse_department::orderBy('department','asc')->get();

        return response()-> json($department);
    }
}
