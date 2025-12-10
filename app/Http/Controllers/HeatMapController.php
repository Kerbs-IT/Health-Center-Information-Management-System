<?php

namespace App\Http\Controllers;

use App\Models\brgy_unit;
use App\Models\patient_addresses;
use App\Models\medical_record_cases;
use App\Models\staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HeatMapController extends Controller
{
    /**
     * Display the health map page
     */
    public function index()
    {
        //check if staff
        $handled_area = '';
        if(Auth::user()->role == 'staff'){
            $staffInfo = staff::findOrFail(Auth::user()->id);
            $brgyHandled = brgy_unit::findOrFail($staffInfo->assigned_area_id);

            $handled_area = $brgyHandled-> brgy_unit;
        }
        // Get all unique puroks for dropdown
        $puroks = $this->getPuroks();

        // Get all case types for dropdown
        $caseTypes = [
            'vaccination',
            'prenatal',
            'senior-citizen',
            'tb-dots',
            'family-planning'
        ];

        return view('heatmap.heat-map', compact('puroks', 'caseTypes'),
        ['page'=> 'Health Map - Barangay Hugo Perez',
        'isActive'=>true,
        'handledBrgy' => $handled_area??null ]);
    }

    public function getHeatmapData(Request $request)
    {
        $purok = $request->input('purok', 'all');
        $caseType = $request->input('case_type', 'all');

        // Get patient counts per purok
        $patientCounts = $this->getPatientCountsByPurok($purok, $caseType);

        // Get individual patient addresses with coordinates for heatmap
        $query = patient_addresses::query()
            ->select('patient_addresses.latitude', 'patient_addresses.longitude', 'patient_addresses.purok')
            ->join('medical_record_cases', 'patient_addresses.patient_id', '=', 'medical_record_cases.patient_id')
            ->join('patients', 'patient_addresses.patient_id', '=', 'patients.id')
            ->where('patient_addresses.barangay', 'Hugo Perez')
            ->where('patients.status', '!=', 'Archived')
            ->whereNotNull('patient_addresses.purok')
            ->whereNotNull('patient_addresses.latitude')
            ->whereNotNull('patient_addresses.longitude');

        // Filter by purok
        if ($purok !== 'all') {
            $query->where('patient_addresses.purok', $purok);
        }

        // Filter by case type
        if ($caseType !== 'all') {
            $query->where('medical_record_cases.type_of_case', $caseType);
        }

        // Get all geocoded addresses
        $addresses = $query->get();

        // Map to heatmap format - directly use stored coordinates
        $heatmapData = $addresses->map(function ($address) {
            return [
                'lat' => (float) $address->latitude,
                'lng' => (float) $address->longitude,
                'purok' => $address->purok,
            ];
        })->toArray();

        // Get statistics - count patients per purok
        $patientCounts = $addresses->groupBy('purok')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();
       
        // Get statistics
        $stats = $this->getStatistics($purok, $caseType, $patientCounts);
        // Load purok coordinates from config
        $purokCoordinates = config('coordinates.hugo_perez.puroks');
        // Calculate center point
        $center = $this->calculateCenter($purok, $purokCoordinates);

        return response()->json([
            'success' => true,
            'data' => $heatmapData,
            'stats' => $stats,
            'center' => $center
        ]);
    }

    /**
     * Get patient counts grouped by purok
     */
    private function getPatientCountsByPurok($purok, $caseType)
    {
        $query = patient_addresses::query()
            ->select('patient_addresses.purok', DB::raw('count(*) as count'))
            ->join('medical_record_cases', 'patient_addresses.patient_id', '=', 'medical_record_cases.patient_id')
            ->join('patients', 'patient_addresses.patient_id', '=', 'patients.id')
            ->where('medical_record_cases.status', '!=', 'Archived')
            ->where('patient_addresses.barangay', 'Hugo Perez')
            ->where('patients.status','!=','Archived')
            ->whereNotNull('patient_addresses.purok');

        // Filter by purok
        if ($purok !== 'all') {
            $query->where('patient_addresses.purok', $purok);
        }

        // Filter by case type
        if ($caseType !== 'all') {
            $query->where('medical_record_cases.type_of_case', $caseType);
        }

        $results = $query->groupBy('patient_addresses.purok')->get();

        // Convert to associative array
        $counts = [];
        foreach ($results as $result) {
            $counts[$result->purok] = $result->count;
        }

        return $counts;
    }

    /**
     * Generate jittered point within boundary polygon
     */
    private function generateBoundedPoint($centerLat, $centerLng, $boundary, $maxAttempts = 50)
    {
        // Calculate bounding box
        $minLat = min(array_column($boundary, 0));
        $maxLat = max(array_column($boundary, 0));
        $minLng = min(array_column($boundary, 1));
        $maxLng = max(array_column($boundary, 1));

        // Try to generate point within boundary
        for ($i = 0; $i < $maxAttempts; $i++) {
            $randomLat = $minLat + mt_rand() / mt_getrandmax() * ($maxLat - $minLat);
            $randomLng = $minLng + mt_rand() / mt_getrandmax() * ($maxLng - $minLng);

            if ($this->isPointInPolygon($randomLat, $randomLng, $boundary)) {
                return [
                    'latitude' => round($randomLat, 6),
                    'longitude' => round($randomLng, 6)
                ];
            }
        }

        // Fallback to center with small offset
        return [
            'latitude' => round($centerLat + (mt_rand(-20, 20) / 100000), 6),
            'longitude' => round($centerLng + (mt_rand(-20, 20) / 100000), 6)
        ];
    }

    /**
     * Generate simple jittered point
     */
    private function generateJitteredPoint($lat, $lng, $minOffset, $maxOffset)
    {
        $latOffset = $minOffset + mt_rand() / mt_getrandmax() * ($maxOffset - $minOffset);
        $lngOffset = $minOffset + mt_rand() / mt_getrandmax() * ($maxOffset - $minOffset);

        $latOffset *= (mt_rand(0, 1) ? 1 : -1);
        $lngOffset *= (mt_rand(0, 1) ? 1 : -1);

        return [
            'latitude' => round($lat + $latOffset, 6),
            'longitude' => round($lng + $lngOffset, 6)
        ];
    }

    /**
     * Check if point is inside polygon (Ray casting algorithm)
     */
    private function isPointInPolygon($lat, $lng, $polygon)
    {
        $numVertices = count($polygon);
        $inside = false;

        for ($i = 0, $j = $numVertices - 1; $i < $numVertices; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $lng) != ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * Calculate center point based on filter
     */
    private function calculateCenter($purok, $purokCoordinates)
    {
        if ($purok !== 'all' && isset($purokCoordinates[$purok])) {
            // Center on selected purok
            return [
                'lat' => $purokCoordinates[$purok]['latitude'],
                'lng' => $purokCoordinates[$purok]['longitude'],
                'zoom' => 17.3
            ];
        }

        // Default: center on Hugo Perez
        return [
            'lat' => $purokCoordinates[$purok]['latitude'] ?? 14.281205011111709,
            'lng' => $purokCoordinates[$purok]['longitude'] ?? 120.88813802186077,
            'zoom' => 16
        ];
    }

    /**
     * Get statistics for current filters
     */
    private function getStatistics($purok, $caseType, $patientCounts)
    {
        $total = array_sum($patientCounts);

        return [
            'total' => $total,
            'by_purok' => $patientCounts
        ];
    }

    /**
     * Get list of puroks from config
     */
    private function getPuroks()
    {
        $purokCoordinates = config('coordinates.hugo_perez.puroks');
        return array_keys($purokCoordinates);
    }
}
