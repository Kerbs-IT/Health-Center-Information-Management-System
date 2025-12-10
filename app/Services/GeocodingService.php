<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * LocationIQ Geocoding Service
 * FREE TIER: 5,000 requests per day
 */
class GeocodingService
{
    private $apiKey;
    private $baseUrl = 'https://us1.locationiq.com/v1/search';

    public function __construct()
    {
        $this->apiKey = config('services.locationiq.api_key');
    }

    /**
     * Geocode an address and return latitude and longitude
     * Tries API first, then falls back to manual coordinates
     */
    public function geocodeAddress($addressComponents)
    {
        // Try manual coordinates first (most reliable for Philippine addresses)
        $manualResult = $this->getManualCoordinates($addressComponents);
        if ($manualResult['success']) {
            return $manualResult;
        }

        // Fallback to API geocoding
        $addressStrategies = $this->buildAddressStrategies($addressComponents);

        foreach ($addressStrategies as $addressString) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl, [
                    'key' => $this->apiKey,
                    'q' => $addressString,
                    'format' => 'json',
                    'limit' => 3,
                    'countrycodes' => 'ph',
                    'addressdetails' => 1
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data)) {
                        $targetCity = $addressComponents['city'] ?? '';

                        foreach ($data as $result) {
                            $displayName = strtolower($result['display_name'] ?? '');
                            $address = $result['address'] ?? [];

                            $cityMatch = !empty($targetCity) && (
                                stripos($displayName, strtolower($targetCity)) !== false ||
                                stripos($address['city'] ?? '', strtolower($targetCity)) !== false ||
                                stripos($address['municipality'] ?? '', strtolower($targetCity)) !== false
                            );

                            $provinceMatch = stripos($displayName, 'cavite') !== false;

                            if ($cityMatch || $provinceMatch) {
                                return [
                                    'success' => true,
                                    'latitude' => (float) $result['lat'],
                                    'longitude' => (float) $result['lon'],
                                    'formatted_address' => $result['display_name'] ?? null,
                                    'place_id' => $result['place_id'] ?? null,
                                    'source' => 'api'
                                ];
                            }
                        }
                    }
                }

                if ($response->status() === 429) {
                    // If rate limited, use manual coordinates
                    return $this->getManualCoordinates($addressComponents, true);
                }
            } catch (\Exception $e) {
                Log::error('LocationIQ geocoding error: ' . $e->getMessage(), [
                    'address' => $addressString
                ]);
                continue;
            }
        }

        // Final fallback to manual coordinates
        return $this->getManualCoordinates($addressComponents, true);
    }

    /**
     * Get manually defined coordinates for Philippine addresses
     * Adds slight randomization to create distribution within purok (configurable)
     * Supports polygon boundaries to keep points within residential areas
     */
    private function getManualCoordinates($addressComponents, $force = false)
    {
        $purok = $addressComponents['purok'] ?? '';
        $barangay = strtolower($addressComponents['barangay'] ?? '');

        // Load manual coordinates configuration
        $coordinates = config('coordinates.hugo_perez');
        $jitterConfig = config('coordinates.jittering', []);

        if (empty($coordinates)) {
            return ['success' => false, 'error' => 'No manual coordinates defined'];
        }

        // Check if jittering is enabled
        $jitterEnabled = $jitterConfig['enabled'] ?? true;

        // Try to find purok-specific coordinates
        if (!empty($purok) && isset($coordinates['puroks'][$purok])) {
            $coord = $coordinates['puroks'][$purok];
            $boundary = $coord['boundary'] ?? [];

            // Apply jittering if enabled and configured for purok level
            if ($jitterEnabled && ($jitterConfig['apply_to_purok'] ?? true)) {
                // Use boundary-based jittering if boundary is defined
                if (!empty($boundary) && count($boundary) >= 3) {
                    $jitteredCoords = $this->addBoundedJittering(
                        $coord['latitude'],
                        $coord['longitude'],
                        $boundary,
                        $jitterConfig['max_attempts'] ?? 50
                    );
                } else {
                    // Fallback to regular jittering
                    $jitteredCoords = $this->addJittering(
                        $coord['latitude'],
                        $coord['longitude'],
                        $jitterConfig['purok_min'] ?? 0.0003,
                        $jitterConfig['purok_max'] ?? 0.0008
                    );
                }

                return [
                    'success' => true,
                    'latitude' => $jitteredCoords['latitude'],
                    'longitude' => $jitteredCoords['longitude'],
                    'formatted_address' => "{$coord['name']}, Hugo Perez, Trece Martires City, Cavite, Philippines",
                    'place_id' => null,
                    'source' => !empty($boundary) ? 'manual_bounded' : 'manual_jittered',
                    'original_lat' => $coord['latitude'],
                    'original_lng' => $coord['longitude']
                ];
            } else {
                // Return exact coordinates without jittering
                return [
                    'success' => true,
                    'latitude' => $coord['latitude'],
                    'longitude' => $coord['longitude'],
                    'formatted_address' => "{$coord['name']}, Hugo Perez, Trece Martires City, Cavite, Philippines",
                    'place_id' => null,
                    'source' => 'manual_exact'
                ];
            }
        }

        // Fallback to barangay center
        if ($force && isset($coordinates['default'])) {
            $coord = $coordinates['default'];

            // Apply jittering if enabled and configured for default level
            if ($jitterEnabled && ($jitterConfig['apply_to_default'] ?? true)) {
                $jitteredCoords = $this->addJittering(
                    $coord['latitude'],
                    $coord['longitude'],
                    $jitterConfig['default_min'] ?? 0.001,
                    $jitterConfig['default_max'] ?? 0.002
                );

                return [
                    'success' => true,
                    'latitude' => $jitteredCoords['latitude'],
                    'longitude' => $jitteredCoords['longitude'],
                    'formatted_address' => "{$coord['name']}, Trece Martires City, Cavite, Philippines",
                    'place_id' => null,
                    'source' => 'manual_default_jittered'
                ];
            } else {
                // Return exact barangay center without jittering
                return [
                    'success' => true,
                    'latitude' => $coord['latitude'],
                    'longitude' => $coord['longitude'],
                    'formatted_address' => "{$coord['name']}, Trece Martires City, Cavite, Philippines",
                    'place_id' => null,
                    'source' => 'manual_default_exact'
                ];
            }
        }

        return ['success' => false, 'error' => 'Purok not found in manual coordinates'];
    }

    /**
     * Add random jittering within polygon boundary
     * Keeps points inside the actual residential area
     */
    private function addBoundedJittering($centerLat, $centerLng, $boundary, $maxAttempts = 100)
    {
        $minLat = min(array_column($boundary, 0));
        $maxLat = max(array_column($boundary, 0));
        $minLng = min(array_column($boundary, 1));
        $maxLng = max(array_column($boundary, 1));

        // 🔧 FIX: Try random points in bounding box first
        for ($i = 0; $i < $maxAttempts; $i++) {
            $randomLat = $minLat + mt_rand() / mt_getrandmax() * ($maxLat - $minLat);
            $randomLng = $minLng + mt_rand() / mt_getrandmax() * ($maxLng - $minLng);

            if ($this->isPointInPolygon($randomLat, $randomLng, $boundary)) {
                return [
                    'latitude' => round($randomLat, 16),
                    'longitude' => round($randomLng, 16)
                ];
            }
        }

        // 🔧 NEW: Smarter fallback - interpolate along polygon edges
        // This works better for narrow/elongated polygons
        $numPoints = count($boundary);

        // Try generating points along random edges of the polygon
        for ($attempt = 0; $attempt < 50; $attempt++) {
            // Pick a random edge
            $edgeIndex = rand(0, $numPoints - 1);
            $point1 = $boundary[$edgeIndex];
            $point2 = $boundary[($edgeIndex + 1) % $numPoints];

            // Generate a point somewhere along this edge (with small inward offset)
            $t = mt_rand() / mt_getrandmax(); // Random position along edge (0 to 1)
            $edgeLat = $point1[0] + $t * ($point2[0] - $point1[0]);
            $edgeLng = $point1[1] + $t * ($point2[1] - $point1[1]);

            // Add small random offset perpendicular to edge (to move slightly inside)
            $inwardOffset = 0.00005; // ~5 meters inward
            $offsetLat = ($this->randomFloat(-1, 1)) * $inwardOffset;
            $offsetLng = ($this->randomFloat(-1, 1)) * $inwardOffset;

            $finalLat = $edgeLat + $offsetLat;
            $finalLng = $edgeLng + $offsetLng;

            // Verify it's inside the polygon
            if ($this->isPointInPolygon($finalLat, $finalLng, $boundary)) {
                return [
                    'latitude' => round($finalLat, 16),
                    'longitude' => round($finalLng, 16)
                ];
            }
        }

        // 🔧 LAST RESORT: Use center with very small jitter and log warning
        Log::warning('Could not generate point inside boundary after all attempts', [
            'center' => [$centerLat, $centerLng],
            'boundary_points' => count($boundary)
        ]);

        $latOffset = $this->randomFloat(-0.0001, 0.0001);
        $lngOffset = $this->randomFloat(-0.0001, 0.0001);

        return [
            'latitude' => round($centerLat + $latOffset, 16),
            'longitude' => round($centerLng + $lngOffset, 16)
        ];
    }

    /**
     * Check if a point is inside a polygon using ray casting algorithm
     * @param float $lat Point latitude
     * @param float $lng Point longitude
     * @param array $polygon Array of [lat, lng] coordinates defining the polygon
     * @return bool True if point is inside polygon
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
     * Add random jittering to coordinates to simulate distribution
     * This creates a realistic spread for heatmap visualization
     */
    private function addJittering($latitude, $longitude, $minOffset, $maxOffset)
    {
        // Generate random offset within specified range
        $latOffset = $this->randomFloat($minOffset, $maxOffset);
        $lngOffset = $this->randomFloat($minOffset, $maxOffset);

        // Randomly make offset positive or negative
        $latOffset *= (rand(0, 1) ? 1 : -1);
        $lngOffset *= (rand(0, 1) ? 1 : -1);

        return [
            'latitude' => round($latitude + $latOffset, 16),
            'longitude' => round($longitude + $lngOffset, 16)
        ];
    }

    /**
     * Generate random float between min and max
     */
    private function randomFloat($min, $max)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * Build multiple address string strategies
     * Try different formats since Philippine address data can be inconsistent
     */
    private function buildAddressStrategies($components)
    {
        $strategies = [];

        $barangay = $components['barangay'] ?? '';
        $city = $components['city'] ?? '';
        $province = $components['province'] ?? '';
        $purok = $components['purok'] ?? '';

        // Strategy 1: Full format with purok
        if (!empty($purok) && !empty($barangay)) {
            $strategies[] = "{$purok}, Barangay {$barangay}, {$city}, {$province}, Philippines";
        }

        // Strategy 2: Without "Barangay" prefix
        if (!empty($barangay)) {
            $strategies[] = "{$barangay}, {$city}, {$province}, Philippines";
        }

        // Strategy 3: With "Barangay" prefix
        if (!empty($barangay)) {
            $strategies[] = "Barangay {$barangay}, {$city}, {$province}, Philippines";
        }

        // Strategy 4: Try city variations
        if (!empty($city)) {
            $cityAlt = str_replace(' City', '', $city); // Remove "City" suffix
            if (!empty($barangay)) {
                $strategies[] = "{$barangay}, {$cityAlt}, {$province}, Philippines";
            }
        }

        // Strategy 5: Just city and province (most general)
        $strategies[] = "{$city}, {$province}, Philippines";

        // Remove empty strategies and duplicates
        $strategies = array_filter($strategies);
        $strategies = array_unique($strategies);

        return $strategies;
    }

    /**
     * Validate if coordinates are within expected bounds
     * Adjust these bounds based on your specific barangay location
     */
    public function validateCoordinates($latitude, $longitude, $expectedBounds = null)
    {
        // Default bounds for Imus, Cavite area (adjust as needed)
        $bounds = $expectedBounds ?? [
            'min_lat' => 14.25,
            'max_lat' => 14.32,
            'min_lng' => 120.87,
            'max_lng' => 120.90
        ];

        return (
            $latitude >= $bounds['min_lat'] &&
            $latitude <= $bounds['max_lat'] &&
            $longitude >= $bounds['min_lng'] &&
            $longitude <= $bounds['max_lng']
        );
    }

    /**
     * Reverse geocode - get address from coordinates
     * Useful for validation or future features
     */
    public function reverseGeocode($latitude, $longitude)
    {
        try {
            $response = Http::timeout(10)->get('https://us1.locationiq.com/v1/reverse', [
                'key' => $this->apiKey,
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'address' => $data['display_name'] ?? null,
                    'details' => $data['address'] ?? []
                ];
            }

            return [
                'success' => false,
                'error' => 'Reverse geocoding failed'
            ];
        } catch (\Exception $e) {
            Log::error('Reverse geocoding error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
