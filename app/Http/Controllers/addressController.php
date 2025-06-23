<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\City;
use App\Models\Province;
use App\Models\region;
use Illuminate\Http\Request;

class addressController extends Controller
{
    //
    public function getRegions(){
        $region = region::orderBy('name') -> get();
        return response() -> json(['region' => $region]);
    }
    public function getProvinces($regionCode) {
        return response()->json(
            Province::where('region_id', $regionCode)->orderBy('name')->get(['code', 'name'])
        );
    }
    // cities
    public function getCities($provinceCode) {
        return response()->json(
            City::where('province_id', $provinceCode)->orderBy('name')->get(['code', 'name'])
        );
    }
    public function getBrgy($cityCode) {
        return response()->json(
            Barangay::where('city_id', $cityCode)->orderBy('name')->get(['code', 'name'])
        );
    }
}
