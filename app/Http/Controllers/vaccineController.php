<?php

namespace App\Http\Controllers;

use App\Models\vaccines;
use Illuminate\Http\Request;

class vaccineController extends Controller
{
    //
    public function getVaccines(){
        try{
            $vaccines = vaccines::get();

            return response() -> json([
                'vaccines'=> $vaccines
            ],200);
        }catch(\Exception $e){
            return response()-> json([
                'error'=> $e->getMessage()
            ],500);
        }
       


    }
}
