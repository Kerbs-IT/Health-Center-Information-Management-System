<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class manageInterfaceController extends Controller
{
    //
    public function manageInterface(){
        return view('manage_interface.manageInterface',['isActive' => true, 'page' => 'MANAGE INTERFACE']);
    }
}
