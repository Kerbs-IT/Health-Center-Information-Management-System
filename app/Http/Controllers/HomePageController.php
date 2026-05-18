<?php

namespace App\Http\Controllers;

use App\Models\CarouselImage;
use App\Models\nurses;
use App\Models\staff;
use App\Models\User;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index()
    {
        $healthWorkers = staff::with('assigned_areas')
            ->where('status', 'Active')
            ->orderBy('first_name')
            ->get();

        $carouselImages = CarouselImage::orderBy('order')->get();

        return view('homepage', compact('healthWorkers', 'carouselImages'));
    }
}