<?php

namespace App\Http\Controllers;

use App\Models\pregnancy_timeline_records;
use App\Models\prenatal_case_records;
use App\Models\staff;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    //

    public function viewCase($typeOfRecord,$id)
    {
        try {

            if($typeOfRecord == 'prenatal'){
                $case = prenatal_case_records::with('pregnancy_timeline_records', 'prenatal_assessment')->findOrFail($id);
                $healthwoker = staff::where('user_id', $case->health_worker_id)->findOrFail();
                return response()->json([
                    'caseInfo' => $case,
                    
                ], 200);
            }
           
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
