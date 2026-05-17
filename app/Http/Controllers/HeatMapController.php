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
        $handled_areas = collect();

        if (Auth::user()->role == 'staff') {
            $assignedAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', Auth::id())
                ->pluck('area_id');

            $handled_areas = brgy_unit::whereIn('id', $assignedAreaIds)
                ->pluck('brgy_unit');
        }

        $puroks = $this->getPuroks();

        $caseTypes = [
            'vaccination',
            'prenatal',
            'senior-citizen',
            'tb-dots',
            'family-planning',
            'general-consultation'
        ];

        return view(
            'heatmap.heat-map',
            compact('puroks', 'caseTypes'),
            [
                'page'          => 'HeatMap - Barangay Hugo Perez',
                'isActive'      => true,
                'handledAreas'  => $handled_areas, // now a collection, not a single string
            ]
        );
    }

    /**
     * Get heatmap data with caching and optimization
     */
    public function getHeatmapData(Request $request)
    {
        $purok    = $request->input('purok', 'all');
        $caseType = $request->input('case_type', 'all');

        if (Auth::user()->role == 'staff') {
            $assignedAreaIds = DB::table('staff_area_assignments')
                ->where('staff_id', Auth::id())
                ->pluck('area_id');

            $allowedPuroks = brgy_unit::whereIn('id', $assignedAreaIds)
                ->pluck('brgy_unit');

            if ($purok === 'all' || $purok === 'assigned_all') {
                $purok = $allowedPuroks->toArray();
            } elseif (!$allowedPuroks->contains($purok)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized area.'], 403);
            }
        }

        // Build cache key — fix: sort in-place correctly
        if (is_array($purok)) {
            $sortedPurok = $purok;
            sort($sortedPurok);
            $cacheKey = "heatmap_staff_" . Auth::id() . "_" . md5(implode(',', $sortedPurok)) . "_{$caseType}";
        } else {
            $cacheKey = "heatmap_{$purok}_{$caseType}";
        }

        $result = Cache::remember($cacheKey, self::CACHE_DURATION * 60, function () use ($purok, $caseType) {
            return $this->buildHeatmapData($purok, $caseType);
        });

        return response()->json($result);
    }

    // Helper to sort for consistent cache keys regardless of area order
    private function sorted(array $arr): array
    {
        sort($arr);
        return $arr;
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

        // Supports both a single string and an array of puroks
        if (is_array($purok)) {
            $query->whereIn('patient_addresses.purok', $purok);
        } elseif ($purok !== 'all') {
            $query->where('patient_addresses.purok', $purok);
        }

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
        // Single purok selected — zoom into it
        if (!is_array($purok) && $purok !== 'all' && isset($purokCoordinates[$purok])) {
            return [
                'lat'  => $purokCoordinates[$purok]['latitude'],
                'lng'  => $purokCoordinates[$purok]['longitude'],
                'zoom' => 17.3
            ];
        }

        // Array of puroks (staff "all assigned") — average their coordinates
        if (is_array($purok) && count($purok) > 0) {
            $lats = [];
            $lngs = [];
            foreach ($purok as $p) {
                if (isset($purokCoordinates[$p])) {
                    $lats[] = $purokCoordinates[$p]['latitude'];
                    $lngs[] = $purokCoordinates[$p]['longitude'];
                }
            }
            if (!empty($lats)) {
                return [
                    'lat'  => array_sum($lats) / count($lats),
                    'lng'  => array_sum($lngs) / count($lngs),
                    'zoom' => 16
                ];
            }
        }

        // Default fallback — Hugo Perez center
        return [
            'lat'  => 14.281205011111709,
            'lng'  => 120.88813802186077,
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
