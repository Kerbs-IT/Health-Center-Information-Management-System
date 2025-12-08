<?php

namespace App\Observers;

use App\Models\patient_addresses;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\Log;

class PatientAddressObserver
{
    private $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    /**
     * Handle the patient_addresses "creating" event.
     * Geocode address automatically when creating
     */
    public function creating(patient_addresses $patientAddress)
    {
        // Only geocode if coordinates are not provided
        if (empty($patientAddress->latitude) || empty($patientAddress->longitude)) {
            $this->geocodeAddress($patientAddress);
        }
    }

   
    public function updating(patient_addresses $patientAddress)
    {
        // Check if any address component has changed
        $addressFields = [
            'house_number',
            'street',
            'purok',
            'barangay',
            'city',
            'province',
            'postal_code'
        ];

        $hasAddressChanged = false;
        foreach ($addressFields as $field) {
            if ($patientAddress->isDirty($field)) {
                $hasAddressChanged = true;
                break;
            }
        }

        // Re-geocode if address changed and coordinates aren't manually set
        if ($hasAddressChanged && !$patientAddress->isDirty('latitude') && !$patientAddress->isDirty('longitude')) {
            $this->geocodeAddress($patientAddress);
        }
    }

   
    private function geocodeAddress(patient_addresses $patientAddress)
    {
        try {
            $addressComponents = [
                'house_number' => $patientAddress->house_number,
                'street' => $patientAddress->street,
                'purok' => $patientAddress->purok,
                'barangay' => $patientAddress->barangay,
                'city' => $patientAddress->city,
                'province' => $patientAddress->province,
                'postal_code' => $patientAddress->postal_code,
            ];

            $result = $this->geocodingService->geocodeAddress($addressComponents);

            if ($result['success']) {
                // Validate coordinates
                if ($this->geocodingService->validateCoordinates(
                    $result['latitude'],
                    $result['longitude']
                )) {
                    $patientAddress->latitude = $result['latitude'];
                    $patientAddress->longitude = $result['longitude'];
                } else {
                    Log::warning('Geocoded coordinates out of bounds', [
                        'patient_id' => $patientAddress->patient_id,
                        'coordinates' => [$result['latitude'], $result['longitude']]
                    ]);
                }
            } else {
                Log::error('Failed to geocode patient address', [
                    'patient_id' => $patientAddress->patient_id,
                    'error' => $result['error']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in PatientAddressObserver: ' . $e->getMessage());
        }
    }
}
