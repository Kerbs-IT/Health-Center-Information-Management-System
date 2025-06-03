<?php

namespace App\Http\Controllers;

use App\Models\brgy_unit;
use Illuminate\Http\Request;

class brgyUnitController extends Controller
{
    //
    public function showBrgyUnit(){
        $data = brgy_unit::orderBy('brgy_unit','asc')->get();

        return response()->json($data);
    }
}
