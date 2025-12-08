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
     */
    public function geocodeAddress($addressComponents)
    {
        // Build the full address string
        $addressString = $this->buildAddressString($addressComponents);

        try {
            $response = Http::timeout(10)->get($this->baseUrl, [
                'key' => $this->apiKey,
                'q' => $addressString,
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'ph', // Restrict to Philippines
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data) && isset($data[0])) {
                    $result = $data[0];

                    return [
                        'success' => true,
                        'latitude' => (float) $result['lat'],
                        'longitude' => (float) $result['lon'],
                        'formatted_address' => $result['display_name'] ?? null,
                        'place_id' => $result['place_id'] ?? null
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'Address not found'
                ];
            }

            // Handle rate limiting
            if ($response->status() === 429) {
                return [
                    'success' => false,
                    'error' => 'Rate limit exceeded. Please wait and try again.'
                ];
            }

            return [
                'success' => false,
                'error' => 'Geocoding request failed: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('LocationIQ geocoding error: ' . $e->getMessage(), [
                'address' => $addressString
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build address string from components
     */
    private function buildAddressString($components)
    {
        $parts = [];

        if (!empty($components['house_number'])) {
            $parts[] = $components['house_number'];
        }
        if (!empty($components['street'])) {
            $parts[] = $components['street'];
        }
        if (!empty($components['purok'])) {
            $parts[] = $components['purok'];
        }
        if (!empty($components['barangay'])) {
            $parts[] = $components['barangay'];
        }
        if (!empty($components['city'])) {
            $parts[] = $components['city'];
        }
        if (!empty($components['province'])) {
            $parts[] = $components['province'];
        }
        if (!empty($components['postal_code'])) {
            $parts[] = $components['postal_code'];
        }

        $parts[] = 'Philippines';

        return implode(', ', $parts);
    }

    /**
     * Validate if coordinates are within expected bounds
     * Adjust these bounds based on your specific barangay location
     */
    public function validateCoordinates($latitude, $longitude, $expectedBounds = null)
    {
        // Default bounds for hugo perez, Cavite area (adjust as needed)
        $bounds = $expectedBounds ?? [
            'min_lat' => 14.2500,  // Adjusted for Trece Martires
            'max_lat' => 14.3500,
            'min_lng' => 120.8500,
            'max_lng' => 121.0000
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
