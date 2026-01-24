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
    // UNCHANGED - For overall patient count cards (no date filtering)
    public function info()
    {

        if (Auth::user()->role == 'nurse') {
            try {

                $baseQuery = medical_record_cases::query()
                    ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                    ->where('patients.status', 'Active')
                    ->where('medical_record_cases.status','Active');

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
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ], 422);
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
                    ->where('v.health_worker_id', $staffId)
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

    // NEW METHOD - For pie chart with date range filtering
    public function pieChartData(Request $request)
    {
        // Get date range from request, default to current year
        $startDate = $request->input('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfYear()->toDateString());

        if (Auth::user()->role == 'nurse') {
            try {

                $baseQuery = medical_record_cases::query()
                    ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                    ->where('patients.status', '!=', 'Archived')
                    ->where('medical_record_cases.status', '!=', 'Archived')
                    ->whereBetween('medical_record_cases.created_at', [$startDate, $endDate]);

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
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ], 422);
            }
        }

        if (Auth::user()->role == 'staff') {

            try {

                $staffId = Auth::user()->id;
                $baseQuery = medical_record_cases::query()
                    ->join('patients', 'medical_record_cases.patient_id', '=', 'patients.id')
                    ->where('patients.status', '!=', 'Archived')
                    ->where('medical_record_cases.status', '!=', 'Archived')
                    ->whereBetween('medical_record_cases.created_at', [$startDate, $endDate]);


                $types = (clone $baseQuery)
                    ->select('medical_record_cases.type_of_case', DB::raw('COUNT(*) as total'))
                    ->groupBy('medical_record_cases.type_of_case')
                    ->get();

                $vaccination = (clone $baseQuery)
                    ->join('vaccination_medical_records as v', 'v.medical_record_case_id', '=', 'medical_record_cases.id')
                    ->where('v.health_worker_id', $staffId)
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
            }
        }
    }

    // MODIFIED - Add date range support for bar chart
    public function monthlyPatientStats(Request $request)
    {
        $user = Auth::user();
        $staffId = $user->id;

        // Get date range from request, default to current year
        $startDate = $request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfYear()->format('Y-m-d'));

        $caseMap = [
            'vaccination'     => 'vaccination',
            'prenatal'        => 'prenatal',
            'senior'          => 'senior-citizen',
            'tb'              => 'tb-dots',
            'family_planning' => 'family-planning',
        ];

        try {
            // Build base query
            $query = medical_record_cases::with('patient')
                ->whereHas('patient', function ($q) {
                    $q->where('status', '!=', 'Archived');
                })
                ->where('status', '!=', 'Archived')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);

            // If staff, filter by health worker
            if ($user->role === 'staff') {
                $query->where(function ($q) use ($staffId) {
                    $q->whereHas('vaccination_medical_record', function ($subQ) use ($staffId) {
                        $subQ->where('health_worker_id', $staffId);
                    })
                        ->orWhereHas('prenatal_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('senior_citizen_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('tb_dots_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        })
                        ->orWhereHas('family_planning_medical_record', function ($subQ) use ($staffId) {
                            $subQ->where('health_worker_id', $staffId);
                        });
                });
            }

            // Get all records
            $records = $query->get();

            // Process data in PHP
            $monthlyData = [];

            foreach ($records as $record) {
                $yearMonth = Carbon::parse($record->created_at)->format('Y-m');
                $type = $record->type_of_case;

                if (!isset($monthlyData[$yearMonth])) {
                    $monthlyData[$yearMonth] = [];
                }

                if (!isset($monthlyData[$yearMonth][$type])) {
                    $monthlyData[$yearMonth][$type] = 0;
                }

                $monthlyData[$yearMonth][$type]++;
            }

            // Generate all months in range
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $uniqueMonths = [];
            $monthLabels = [];

            while ($start <= $end) {
                $yearMonth = $start->format('Y-m');
                $uniqueMonths[] = $yearMonth;
                $monthLabels[] = $start->format('M Y');
                $start->addMonth();
            }

            // Initialize result structure
            $result = [
                'all' => [
                    'label' => 'All Patients',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'vaccination' => [
                    'label' => 'Vaccination',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'prenatal' => [
                    'label' => 'Prenatal Care',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'senior' => [
                    'label' => 'Senior Citizen',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'tb' => [
                    'label' => 'TB Treatment',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
                'family_planning' => [
                    'label' => 'Family Planning',
                    'data' => array_fill(0, count($uniqueMonths), 0),
                    'months' => $monthLabels
                ],
            ];

            // Fill in the data from processed results
            foreach ($uniqueMonths as $index => $yearMonth) {
                if (isset($monthlyData[$yearMonth])) {
                    foreach ($monthlyData[$yearMonth] as $type => $count) {
                        // Add to total
                        $result['all']['data'][$index] += $count;

                        // Add to specific type
                        $key = array_search($type, $caseMap);
                        if ($key !== false) {
                            $result[$key]['data'][$index] = $count;
                        }
                    }
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load monthly stats',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    // UNCHANGED - Keep as is
    public function patientCountPerArea(Request $request)
    {
        try {

            // get the date range
            $startDate = $request->input('startDate', Carbon::now()->subYear()->startOfYear()->format('Y-m-d'));
            $endDate = $request->input("endDate", Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));

            $data = [];
            $query = patient_addresses::query()
                ->select('patient_addresses.purok', DB::raw('count(*) as count'))
                ->join('medical_record_cases', 'patient_addresses.patient_id', '=', 'medical_record_cases.patient_id')
                ->join('patients', 'patient_addresses.patient_id', '=', 'patients.id')
                ->where('medical_record_cases.status', '!=', 'Archived')
                ->where('patient_addresses.barangay', 'Hugo Perez')
                ->where('patients.status', '!=', 'Archived')
                ->whereNotNull('patient_addresses.purok')
                ->whereDate('patients.created_at', '>=', $startDate)
                ->whereDate('patients.created_at', '<=', $endDate);

            $brgyUnits = brgy_unit::get();

            if (Auth::user()->role == 'nurse') {
                foreach ($brgyUnits as $unit) {
                    $areaData = (clone $query)
                        ->where('patient_addresses.purok', $unit->brgy_unit)
                        ->count();

                    $data[$unit->brgy_unit] = $areaData;
                }
            }

            if (Auth::user()->role == 'staff') {
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
                        // ->distinct('patient_addresses.patient_id')
                        ->count('patient_addresses.patient_id');

                    $data[$unit->brgy_unit] = $areaData;
                }
            }


            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    // UNCHANGED - Keep as is
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
    public function getAgeDistribution(Request $request)
    {
        $user = Auth::user();
        $staffId = $user->id;

        $startDate = $request->input('start_date', Carbon::now()->startOfYear());
        $endDate = $request->input('end_date', Carbon::now()->endOfYear());

        // Start building the query
        $query = medical_record_cases::with('patient')
            ->join('patients', 'patients.id', '=', 'medical_record_cases.patient_id')
            ->where('patients.status', 'Active')
            ->where('medical_record_cases.status', 'Active')
            ->whereBetween('patients.created_at', [$startDate, $endDate]);

        // Apply staff filter BEFORE calling get()
        if ($user->role === 'staff') {
            $query->where(function ($q) use ($staffId) {
                $q->whereHas('vaccination_medical_record', function ($subQ) use ($staffId) {
                    $subQ->where('health_worker_id', $staffId);
                })
                    ->orWhereHas('prenatal_medical_record', function ($subQ) use ($staffId) {
                        $subQ->where('health_worker_id', $staffId);
                    })
                    ->orWhereHas('senior_citizen_medical_record', function ($subQ) use ($staffId) {
                        $subQ->where('health_worker_id', $staffId);
                    })
                    ->orWhereHas('tb_dots_medical_record', function ($subQ) use ($staffId) {
                        $subQ->where('health_worker_id', $staffId);
                    })
                    ->orWhereHas('family_planning_medical_record', function ($subQ) use ($staffId) {
                        $subQ->where('health_worker_id', $staffId);
                    });
            });
        }

        // Now execute the query
        $records = $query->get();

        // Initialize result structure
        $ageDistribution = [
            'vaccination' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
            'prenatal' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
            'seniorCitizen' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
            'tbDots' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
            'familyPlanning' => ['0-11' => 0, '1-5' => 0, '6-17' => 0, '18-59' => 0, '60+' => 0],
        ];

        // Process each record
        foreach ($records as $record) {
            if (!$record->patient || !$record->patient->date_of_birth) {
                continue;
            }

            $age = Carbon::parse($record->patient->date_of_birth)->age;
            $ageGroup = $this->getAgeGroup($age);
            $caseType = $this->mapCaseType($record->type_of_case);

            if ($caseType && isset($ageDistribution[$caseType][$ageGroup])) {
                $ageDistribution[$caseType][$ageGroup]++;
            }
        }

        return response()->json($ageDistribution);
    }
    private function getAgeGroup($age)
    {
        if ($age < 1) {
            return '0-11'; // 0-11 months
        } elseif ($age >= 1 && $age <= 5) {
            return '1-5';
        } elseif ($age >= 6 && $age <= 17) {
            return '6-17';
        } elseif ($age >= 18 && $age <= 59) {
            return '18-59';
        } else {
            return '60+';
        }
    }

    /**
     * Map database case type to camelCase format
     */
    private function mapCaseType($typeOfCase)
    {
        $mapping = [
            'vaccination' => 'vaccination',
            'prenatal' => 'prenatal',
            'senior-citizen' => 'seniorCitizen',
            'tb-dots' => 'tbDots',
            'family-planning' => 'familyPlanning',
        ];

        return $mapping[$typeOfCase] ?? null;
    }

    public function overdueCounts()
    {
        try{
            $user = Auth::user();
            $today = Carbon::now()->format('Y-m-d');
            $isStaff = $user->role === 'staff';
            $staff = null;
            if($isStaff){
                $staff = $user->staff;
            }
            $counts = [];

            // 1. VACCINATION OVERDUE COUNT
            $lastVaccinationSubquery = DB::table('vaccination_case_records as vcr')
                ->select('vcr.medical_record_case_id', DB::raw('MAX(vcr.id) as last_record_id'))
                ->where('vcr.status', '!=', 'Archived')
                ->where('vcr.vaccination_status', 'completed')
                ->groupBy('vcr.medical_record_case_id');

            $vaccinationBaseQuery = DB::table('vaccination_case_records as vcr')
                ->joinSub($lastVaccinationSubquery, 'last_vcr', function ($join) {
                    $join->on('vcr.id', '=', 'last_vcr.last_record_id');
                })
                ->join('medical_record_cases as mrc', 'mrc.id', '=', 'vcr.medical_record_case_id')
                ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
                ->whereDate('vcr.date_of_comeback', '<', $today)
                ->where('p.status', '!=', 'Archived');

            if ($isStaff) {
                $vaccinationBaseQuery->join('vaccination_medical_records as vmr', 'vmr.medical_record_case_id', '=', 'mrc.id')
                    ->where('vmr.health_worker_id', $staff->user_id);
            }

            $vaccinationCases = $vaccinationBaseQuery
                ->select('vcr.*', 'mrc.id as medical_record_case_id', 'p.id as patient_id')
                ->get();

            $countedPatients = [];
            $vaccineDoseConfig = [
                'BCG' => ['maxDoses' => 1],
                'Hepatitis B' => ['maxDoses' => 1],
                'PENTA' => ['maxDoses' => 3],
                'OPV' => ['maxDoses' => 3],
                'IPV' => ['maxDoses' => 2],
                'PCV' => ['maxDoses' => 3],
                'MMR' => ['maxDoses' => 2],
                'MCV' => ['maxDoses' => 2],
                'TD' => ['maxDoses' => 2],
                'Human Papiliomavirus' => ['maxDoses' => 2],
                'Influenza Vaccine' => ['maxDoses' => 3],
                'Pnuemococcal Vaccine' => ['maxDoses' => 3],
            ];

            foreach ($vaccinationCases as $case) {
                if (in_array($case->patient_id, $countedPatients)) {
                    continue;
                }

                $vaccines = explode(',', $case->vaccine_type ?? '');
                $currentDose = $case->dose_number;
                $nextDosage = $currentDose + 1;
                $allVaccinesComplete = true;
                $hasPendingDose = false;

                foreach ($vaccines as $vaccine) {
                    $vaccineAcronym = trim($vaccine);

                    if (isset($vaccineDoseConfig[$vaccineAcronym])) {
                        $maxDoses = $vaccineDoseConfig[$vaccineAcronym]['maxDoses'];

                        if ($currentDose < $maxDoses) {
                            $allVaccinesComplete = false;

                            $nextDoseExists = DB::table('vaccination_case_records')
                                ->where('medical_record_case_id', $case->medical_record_case_id)
                                ->where('vaccine_type', 'LIKE', '%' . $vaccineAcronym . '%')
                                ->where('status', '!=', 'Archived')
                                ->where('dose_number', $nextDosage)
                                ->exists();

                            if (!$nextDoseExists) {
                                $hasPendingDose = true;
                                break;
                            }
                        }
                    }
                }

                if (!$allVaccinesComplete && $hasPendingDose) {
                    $countedPatients[] = $case->patient_id;
                }
            }

            $counts['vaccination'] = count($countedPatients);

            // 2. PRENATAL OVERDUE COUNT
            $lastPrenatalSubquery = DB::table('pregnancy_checkups as pc')
                ->select('pc.medical_record_case_id', DB::raw('MAX(pc.id) as last_record_id'))
                ->where('pc.status', '!=', 'Archived')
                ->whereNotNull('pc.date_of_comeback')
                ->groupBy('pc.medical_record_case_id');

            $prenatalBaseQuery = DB::table('pregnancy_checkups as pc')
                ->joinSub($lastPrenatalSubquery, 'last_pc', function ($join) {
                    $join->on('pc.id', '=', 'last_pc.last_record_id');
                })
                ->join('medical_record_cases as mrc', 'mrc.id', '=', 'pc.medical_record_case_id')
                ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
                ->whereDate('pc.date_of_comeback', '<', $today)
                ->where('p.status', '!=', 'Archived');

            if ($isStaff) {
                $prenatalBaseQuery->join('prenatal_medical_records as pmr', 'pmr.medical_record_case_id', '=', 'mrc.id')
                    ->where('pmr.health_worker_id', $staff->user_id);
            }

            $prenatalCases = $prenatalBaseQuery
                ->select('pc.*', 'mrc.id as medical_record_case_id')
                ->get();

            $prenatalCount = 0;
            foreach ($prenatalCases as $case) {
                $checkupExists = DB::table('pregnancy_checkups')
                    ->where('medical_record_case_id', $case->medical_record_case_id)
                    ->where('status', '!=', 'Archived')
                    ->whereDate('created_at', '>=', $case->date_of_comeback)
                    ->where('id', '!=', $case->id)
                    ->exists();

                if (!$checkupExists) {
                    $prenatalCount++;
                }
            }

            $counts['prenatal'] = $prenatalCount;

            // 3. SENIOR CITIZEN OVERDUE COUNT
            $seniorCitizenSubquery = DB::table('senior_citizen_case_records as sccr')
                ->select('sccr.medical_record_case_id', DB::raw('MAX(sccr.id) as last_record_id'))
                ->where('sccr.status', '!=', 'Archived')
                ->whereNotNull('sccr.date_of_comeback')
                ->groupBy('sccr.medical_record_case_id');

            $seniorCitizenQuery = DB::table('senior_citizen_case_records as sccr')
                ->joinSub($seniorCitizenSubquery, 'last_sccr', function ($join) {
                    $join->on('sccr.id', '=', 'last_sccr.last_record_id');
                })
                ->join('medical_record_cases as mrc', 'mrc.id', '=', 'sccr.medical_record_case_id')
                ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
                ->whereDate('sccr.date_of_comeback', '<', $today)
                ->where('p.status', '!=', 'Archived');

            if ($isStaff) {
                $seniorCitizenQuery->join('senior_citizen_medical_records as scmr', 'scmr.medical_record_case_id', '=', 'mrc.id')
                    ->where('scmr.health_worker_id', $staff->user_id);
            }

            $counts['senior_citizen'] = $seniorCitizenQuery->distinct()->count('p.id');

            // 4. TB DOTS OVERDUE COUNT
            $tbDotsSubquery = DB::table('tb_dots_check_ups as tdcu')
                ->select('tdcu.medical_record_case_id', DB::raw('MAX(tdcu.id) as last_record_id'))
                ->where('tdcu.status', '!=', 'Archived')
                ->whereNotNull('tdcu.date_of_comeback')
                ->groupBy('tdcu.medical_record_case_id');

            $tbDotsQuery = DB::table('tb_dots_check_ups as tdcu')
                ->joinSub($tbDotsSubquery, 'last_tdcu', function ($join) {
                    $join->on('tdcu.id', '=', 'last_tdcu.last_record_id');
                })
                ->join('medical_record_cases as mrc', 'mrc.id', '=', 'tdcu.medical_record_case_id')
                ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
                ->whereDate('tdcu.date_of_comeback', '<', $today)
                ->where('p.status', '!=', 'Archived');

            if ($isStaff) {
                $tbDotsQuery->join('tb_dots_medical_records as tdmr', 'tdmr.medical_record_case_id', '=', 'mrc.id')
                    ->where('tdmr.health_worker_id', $staff->user_id);
            }

            $counts['tb_dots'] = $tbDotsQuery->distinct()->count('p.id');

            // 5. FAMILY PLANNING OVERDUE COUNT
            $familyPlanningSubquery = DB::table('family_planning_side_b_records as fpsbr')
                ->select('fpsbr.medical_record_case_id', DB::raw('MAX(fpsbr.id) as last_record_id'))
                ->where('fpsbr.status', '!=', 'Archived')
                ->whereNotNull('fpsbr.date_of_follow_up_visit')
                ->groupBy('fpsbr.medical_record_case_id');

            $familyPlanningQuery = DB::table('family_planning_side_b_records as fpsbr')
                ->joinSub($familyPlanningSubquery, 'last_fpsbr', function ($join) {
                    $join->on('fpsbr.id', '=', 'last_fpsbr.last_record_id');
                })
                ->join('medical_record_cases as mrc', 'mrc.id', '=', 'fpsbr.medical_record_case_id')
                ->join('patients as p', 'p.id', '=', 'mrc.patient_id')
                ->whereDate('fpsbr.date_of_follow_up_visit', '<=', $today)
                ->where('p.status', '!=', 'Archived');

            if ($isStaff) {
                $familyPlanningQuery->join('family_planning_medical_records as fpmr', 'fpmr.medical_record_case_id', '=', 'mrc.id')
                    ->where('fpmr.health_worker_id', $staff->user_id);
            }

            $counts['family_planning'] = $familyPlanningQuery->distinct()->count('p.id');

            return $counts;

        }catch(\Exception $e){
            return response() -> json([
                'errors' => $e->getMessage()
            ],403);
        }
    }

    public function getOverDueCounts(){
        try{
            // call the function

            $overDueCount = $this->overdueCounts();

            return response()->json($overDueCount, 200);

        }catch(\Exception $e){
            return response()->json([
                'errors' => $e->getMessage()
            ], 403);
        }
    }
}
