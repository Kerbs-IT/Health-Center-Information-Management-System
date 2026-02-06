<?php

namespace App\Http\Controllers;

use App\Models\brgy_unit;
use App\Models\patient_addresses;
use App\Models\medical_record_cases;
use App\Models\staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HeatMapController extends Controller
{
    // Cache duration in minutes
    const CACHE_DURATION = 10;

    // Maximum points to return (prevent overwhelming frontend)
    const MAX_POINTS = 5000;

    /**
     * Display the health map page
     */
    public function index()
    {
        // Check if staff
        $handled_area = '';
        if (Auth::user()->role == 'staff') {
            $staffInfo = staff::findOrFail(Auth::user()->id);
            $brgyHandled = brgy_unit::findOrFail($staffInfo->assigned_area_id);
            $handled_area = $brgyHandled->brgy_unit;
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

        return view(
            'heatmap.heat-map',
            compact('puroks', 'caseTypes'),
            [
                'page' => 'HeatMap - Barangay Hugo Perez',
                'isActive' => true,
                'handledBrgy' => $handled_area ?? null
            ]
        );
    }

    /**
     * Get heatmap data with caching and optimization
     */
    public function getHeatmapData(Request $request)
    {
        $purok = $request->input('purok', 'all');
        $caseType = $request->input('case_type', 'all');

        // Create cache key based on filters
        $cacheKey = "heatmap_{$purok}_{$caseType}";

        // Try to get cached data
        $result = Cache::remember($cacheKey, self::CACHE_DURATION * 60, function () use ($purok, $caseType) {
            return $this->buildHeatmapData($purok, $caseType);
        });

        return response()->json($result);
    }

    /**
     * Build heatmap data (called when cache misses)
     */
    private function buildHeatmapData($purok, $caseType)
    {
        // Build optimized query
        $query = $this->buildBaseQuery($purok, $caseType);

        // Get addresses with case types
        $addresses = $query->get();

        // Process data
        $heatmapData = $this->processAddresses($addresses);

        // Get statistics
        $stats = $this->calculateStatistics($addresses);

        // Load purok coordinates
        $purokCoordinates = config('coordinates.hugo_perez.puroks');

        // Calculate center
        $center = $this->calculateCenter($purok, $purokCoordinates);

        return [
            'success' => true,
            'data' => $heatmapData,
            'stats' => $stats,
            'center' => $center,
            'total_available' => count($heatmapData),
            'cached_at' => now()->toISOString()
        ];
    }

    /**
     * Build optimized base query
     */
    private function buildBaseQuery($purok, $caseType)
    {
        $query = patient_addresses::query()
            ->select(
                'patient_addresses.latitude',
                'patient_addresses.longitude',
                'patient_addresses.purok',
                'medical_record_cases.type_of_case'
            )
            ->join('medical_record_cases', 'patient_addresses.patient_id', '=', 'medical_record_cases.patient_id')
            ->join('patients', 'patient_addresses.patient_id', '=', 'patients.id')
            ->where('patient_addresses.barangay', 'Hugo Perez')
            ->where('patients.status', '!=', 'Archived')
            ->where('medical_record_cases.status', '!=', 'Archived')
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

        return $query;
    }

    /**
     * Process addresses into heatmap format with optimization
     */
    private function processAddresses($addresses)
    {
        $heatmapData = [];

        foreach ($addresses as $address) {
            // Add small random jitter to prevent exact overlaps
            // This prevents color blending while keeping accuracy
            $lat = (float) $address->latitude + (mt_rand(-5, 5) / 1000000);
            $lng = (float) $address->longitude + (mt_rand(-5, 5) / 1000000);

            $heatmapData[] = [
                'lat' => round($lat, 6),
                'lng' => round($lng, 6),
                'purok' => $address->purok,
                'case_type' => $address->type_of_case,
            ];
        }

        // If data exceeds max points, sample it intelligently
        if (count($heatmapData) > self::MAX_POINTS) {
            $heatmapData = $this->stratifiedSample($heatmapData, self::MAX_POINTS);
        }

        return $heatmapData;
    }

    /**
     * Stratified sampling to maintain distribution
     */
    private function stratifiedSample($data, $maxPoints)
    {
        // Group by case type
        $grouped = [];
        foreach ($data as $point) {
            $caseType = $point['case_type'];
            if (!isset($grouped[$caseType])) {
                $grouped[$caseType] = [];
            }
            $grouped[$caseType][] = $point;
        }

        // Calculate points per case type (proportional)
        $total = count($data);
        $sampled = [];

        foreach ($grouped as $caseType => $points) {
            $proportion = count($points) / $total;
            $sampleSize = max(1, (int)($maxPoints * $proportion));

            // Sample from this case type
            $step = max(1, (int)(count($points) / $sampleSize));
            for ($i = 0; $i < count($points); $i += $step) {
                if (count($sampled) < $maxPoints) {
                    $sampled[] = $points[$i];
                }
            }
        }

        return $sampled;
    }

    /**
     * Calculate statistics efficiently
     */
    private function calculateStatistics($addresses)
    {
        $total = $addresses->count();

        // Group by purok
        $byPurok = [];
        foreach ($addresses as $address) {
            $purok = $address->purok;
            if (!isset($byPurok[$purok])) {
                $byPurok[$purok] = 0;
            }
            $byPurok[$purok]++;
        }

        // Group by case type
        $byCaseType = [];
        foreach ($addresses as $address) {
            $caseType = $address->type_of_case;
            if (!isset($byCaseType[$caseType])) {
                $byCaseType[$caseType] = 0;
            }
            $byCaseType[$caseType]++;
        }

        return [
            'total' => $total,
            'by_purok' => $byPurok,
            'by_case_type' => $byCaseType
        ];
    }

    /**
     * Calculate center point based on filter
     */
    private function calculateCenter($purok, $purokCoordinates)
    {
        if ($purok !== 'all' && isset($purokCoordinates[$purok])) {
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
     * Get list of puroks from config
     */
    private function getPuroks()
    {
        $purokCoordinates = config('coordinates.hugo_perez.puroks');
        return array_keys($purokCoordinates);
    }

    /**
     * Clear cache (can be called from admin panel if needed)
     */
    public function clearCache()
    {
        Cache::flush();
        return response()->json(['success' => true, 'message' => 'Cache cleared']);
    }
}
