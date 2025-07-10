<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class nurseDashboardController extends Controller
{
    //

    public function dashboard(){
        $staffCount = User::where('role', 'staff') -> count();

        return view('dashboard.nurse', compact('staffCount'), ['isActive' => true, 'page' => 'DASHBOARD']);
    }
}
