<?php

namespace App\Http\Controllers;

use App\Models\nurses;
use App\Models\staff;
use App\Models\User;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index()
    {
        // Get all active nurses
        $nurses = nurses::with('user')
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->get();

        // Get all active health workers (staff) with their assigned area
        $healthWorkers = User::with(['staff', 'staff.assigned_area'])
            ->where('role', 'staff')
            ->where('status', 'active')
            ->get();

        return view('homepage', compact('nurses', 'healthWorkers'));
    }
}