<?php

namespace App\Http\Controllers;

use App\Models\brgy_unit;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HealthCenterDashboard extends Controller
{
    public function info(){

        if(Auth::user()->role == 'nurse' ){
            try {
                
                $baseQuery = medical_record_cases::query()
                    ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                    ->where('patients.status', '!=', 'Archived')
                    ->where('medical_record_cases.status', '!=', 'Archived');

                $totalPatient = (clone $baseQuery)
                            ->count();

                $types = (clone $baseQuery)
                    ->select('medical_record_cases.type_of_case', DB::raw('COUNT(*) as total'))
                    ->groupBy('medical_record_cases.type_of_case')
                    ->get();
                $vaccination = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'vaccination')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $prenatal = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'prenatal')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $tbDots = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'tb-dots')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $familyPlanning = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'family-planning')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $seniorCitizen = (clone $baseQuery)
                    ->where('medical_record_cases.type_of_case', 'senior-citizen')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');


                return response()->json([
                    'overallPatients' => $totalPatient,
                    'vaccinationCount' => $vaccination,
                    'prenatalCount' => $prenatal,
                    'tbDotsCount' => $tbDots,
                    'seniorCitizenCount' => $seniorCitizen,
                    'familyPlanningCount' => $familyPlanning,
                    'types' => $types
                ],200);
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ],422);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        if (Auth::user()->role == 'staff') {

            try {

                $staffId = Auth::user()->id;
                $baseQuery = medical_record_cases::query()
                    ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                    ->where('patients.status', '!=', 'Archived')
                    ->where('medical_record_cases.status', '!=', 'Archived');


                $types = (clone $baseQuery)
                    ->select('medical_record_cases.type_of_case', DB::raw('COUNT(*) as total'))
                    ->groupBy('medical_record_cases.type_of_case')
                    ->get();
                $vaccination = (clone $baseQuery)
                    ->join('vaccination_medical_records as v', 'v.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('v.health_worker_id', $staffId) // filter by staff
                    ->where('medical_record_cases.type_of_case', 'vaccination')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $prenatal = (clone $baseQuery)
                    ->join('prenatal_medical_records as p', 'p.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('p.health_worker_id', $staffId)
                    ->where('medical_record_cases.type_of_case', 'prenatal')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $tbDots = (clone $baseQuery)
                    ->join('tb_dots_medical_records as t', 't.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('t.health_worker_id', $staffId)
                    ->where('medical_record_cases.type_of_case', 'tb-dots')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $familyPlanning = (clone $baseQuery)
                    ->join('family_planning_medical_records as f', 'f.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('f.health_worker_id', $staffId)
                    ->where('medical_record_cases.type_of_case', 'family-planning')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $seniorCitizen = (clone $baseQuery)
                    ->join('senior_citizen_medical_records as s', 's.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('s.health_worker_id', $staffId)
                    ->where('medical_record_cases.type_of_case', 'senior-citizen')
                    ->distinct('medical_record_cases.id')
                    ->count('medical_record_cases.id');

                $totalPatient = $vaccination + $prenatal +  $tbDots + $familyPlanning + $seniorCitizen;

                return response()->json([
                    'overallPatients' => $totalPatient,
                    'vaccinationCount' => $vaccination,
                    'prenatalCount' => $prenatal,
                    'tbDotsCount' => $tbDots,
                    'seniorCitizenCount' => $seniorCitizen,
                    'familyPlanningCount' => $familyPlanning,
                    'types' => $types
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ], 422);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        
    }

    public function monthlyPatientStats()
    {
        $user = Auth::user();
        $staffId = $user->id; // change if your staff table uses different FK

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $caseMap = [
            'vaccination'     => 'vaccination',
            'prenatal'        => 'prenatal',
            'senior'          => 'senior-citizen',
            'tb'              => 'tb-dots',
            'family_planning' => 'family-planning',
        ];

        // Base query
        $baseQuery = medical_record_cases::query()
            ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', '!=', 'Archived')
            ->whereYear('medical_record_cases.created_at', now()->year);

        /**
         * ✅ Only apply joins + filters if user is STAFF
         */
        if ($user->role === 'staff') {

            $baseQuery->leftJoin('vaccination_medical_records as v', function ($join) use ($staffId) {
                $join->on('v.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('v.health_worker_id', '=', $staffId);
            });

            $baseQuery->leftJoin('prenatal_medical_records as p', function ($join) use ($staffId) {
                $join->on('p.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('p.health_worker_id', '=', $staffId);
            });

            $baseQuery->leftJoin('senior_citizen_medical_records as s', function ($join) use ($staffId) {
                $join->on('s.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('s.health_worker_id', '=', $staffId);
            });

            $baseQuery->leftJoin('tb_dots_medical_records as t', function ($join) use ($staffId) {
                $join->on('t.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('t.health_worker_id', '=', $staffId);
            });

            $baseQuery->leftJoin('family_planning_medical_records as f', function ($join) use ($staffId) {
                $join->on('f.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('f.health_worker_id', '=', $staffId);
            });

            // ✅ Important: filter to only records where staff handled at least one case
            $baseQuery->where(function ($q) {
                $q->whereNotNull('v.id')
                    ->orWhereNotNull('p.id')
                    ->orWhereNotNull('s.id')
                    ->orWhereNotNull('t.id')
                    ->orWhereNotNull('f.id');
            });
        }

        // Final query
        $raw = $baseQuery
            ->selectRaw("
            MONTH(medical_record_cases.created_at) as month,
            medical_record_cases.type_of_case,
            COUNT(DISTINCT medical_record_cases.id) as total
        ")
            ->groupByRaw("MONTH(medical_record_cases.created_at), medical_record_cases.type_of_case")
            ->get();

        // Result skeleton
        $result = [
            'all' => ['label' => 'All Patients', 'data' => array_fill(0, 12, 0)],
            'vaccination' => ['label' => 'Vaccination', 'data' => array_fill(0, 12, 0)],
            'prenatal' => ['label' => 'Prenatal Care', 'data' => array_fill(0, 12, 0)],
            'senior' => ['label' => 'Senior Citizen', 'data' => array_fill(0, 12, 0)],
            'tb' => ['label' => 'TB Treatment', 'data' => array_fill(0, 12, 0)],
            'family_planning' => ['label' => 'Family Planning', 'data' => array_fill(0, 12, 0)],
        ];

        // Fill values
        foreach ($raw as $row) {
            $monthIndex = $row->month - 1;
            $type = $row->type_of_case;

            $result['all']['data'][$monthIndex] += $row->total;

            $key = array_search($type, $caseMap);
            if ($key !== false) {
                $result[$key]['data'][$monthIndex] = $row->total;
            }
        }

        return response()->json($result);
    }


    public function patientCountPerArea(){
        try{
           
            $data = [];
            $query = patient_addresses::query()
                ->select('patient_addresses.purok', DB::raw('count(*) as count'))
                ->join('medical_record_cases', 'patient_addresses.patient_id', '=', 'medical_record_cases.patient_id')
                ->join('patients', 'patient_addresses.patient_id', '=', 'patients.id')
                ->where('medical_record_cases.status', '!=', 'Archived')
                ->where('patient_addresses.barangay', 'Hugo Perez')
                ->where('patients.status', '!=', 'Archived')
                ->whereNotNull('patient_addresses.purok');

            $brgyUnits = brgy_unit::get();

            if(Auth::user()->role == 'nurse'){
                foreach ($brgyUnits as $unit) {
                    $areaData = (clone $query)
                        ->where('patient_addresses.purok', $unit->brgy_unit)
                        ->count();

                    $data[$unit->brgy_unit] = $areaData;
                }
            }

            if(Auth::user()->role == 'staff'){
                $user = Auth::user();
                $staffId = $user->id;
                foreach ($brgyUnits as $unit) {

                    $staffQuery = (clone $query)
                        ->leftJoin('vaccination_medical_records as v', 'v.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->leftJoin('prenatal_medical_records as p', 'p.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->leftJoin('senior_citizen_medical_records as s', 's.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->leftJoin('tb_dots_medical_records as t', 't.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->leftJoin('family_planning_medical_records as f', 'f.medical_record_case_id', '=', 'medical_record_cases.id')
                        ->where(function ($q) use ($staffId) {
                            $q->where('v.health_worker_id', $staffId)
                                ->orWhere('p.health_worker_id', $staffId)
                                ->orWhere('s.health_worker_id', $staffId)
                                ->orWhere('t.health_worker_id', $staffId)
                                ->orWhere('f.health_worker_id', $staffId);
                        });

                    $areaData = $staffQuery
                        ->where('patient_addresses.purok', $unit->brgy_unit)
                        ->distinct('patient_addresses.patient_id')
                        ->count('patient_addresses.patient_id');

                    $data[$unit->brgy_unit] = $areaData;
                }
            }
            

            return response() -> json([
                'data' => $data
            ]);

        }catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
    public function patientAddedToday()
    {
        try {
            $user = Auth::user();
            $today = Carbon::today();

            // Base query (shared)
            $baseQuery = medical_record_cases::query()
                ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                ->where('patients.status', '!=', 'Archived')
                ->where('medical_record_cases.status', '!=', 'Archived')
                ->whereDate('medical_record_cases.created_at', $today);

            /**
             * ✅ If STAFF: filter by health_worker_id across tables
             */
            if ($user->role === 'staff') {
                $staffId = $user->id;

                $baseQuery
                    ->leftJoin('vaccination_medical_records as v', function ($join) use ($staffId) {
                        $join->on('v.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('v.health_worker_id', $staffId);
                    })
                    ->leftJoin('prenatal_medical_records as p', function ($join) use ($staffId) {
                        $join->on('p.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('p.health_worker_id', $staffId);
                    })
                    ->leftJoin('senior_citizen_medical_records as s', function ($join) use ($staffId) {
                        $join->on('s.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('s.health_worker_id', $staffId);
                    })
                    ->leftJoin('tb_dots_medical_records as t', function ($join) use ($staffId) {
                        $join->on('t.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('t.health_worker_id', $staffId);
                    })
                    ->leftJoin('family_planning_medical_records as f', function ($join) use ($staffId) {
                        $join->on('f.medical_record_case_id', '=', 'medical_record_cases.id')
                            ->where('f.health_worker_id', $staffId);
                    })
                    ->where(function ($q) {
                        $q->whereNotNull('v.id')
                            ->orWhereNotNull('p.id')
                            ->orWhereNotNull('s.id')
                            ->orWhereNotNull('t.id')
                            ->orWhereNotNull('f.id');
                    });
            }

            // ✅ Overall
            $totalPatient = (clone $baseQuery)->count();

            // ✅ Grouped types
            $types = (clone $baseQuery)
                ->select('medical_record_cases.type_of_case', DB::raw('COUNT(DISTINCT medical_record_cases.id) as total'))
                ->groupBy('medical_record_cases.type_of_case')
                ->get();

            // ✅ Individual counts
            $vaccination = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'vaccination')
                ->count('medical_record_cases.id');

            $prenatal = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'prenatal')
                ->count('medical_record_cases.id');

            $tbDots = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'tb-dots')
                ->count('medical_record_cases.id');

            $familyPlanning = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'family-planning')
                ->count('medical_record_cases.id');

            $seniorCitizen = (clone $baseQuery)
                ->where('medical_record_cases.type_of_case', 'senior-citizen')
                ->count('medical_record_cases.id');

            return response()->json([
                'overallPatients'     => $totalPatient,
                'vaccinationCount'    => $vaccination,
                'prenatalCount'       => $prenatal,
                'tbDotsCount'         => $tbDots,
                'seniorCitizenCount'  => $seniorCitizen,
                'familyPlanningCount' => $familyPlanning,
                'types'               => $types,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }
}
