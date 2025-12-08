<?php

namespace App\Console\Commands;

use App\Models\patient_addresses;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class GeocodePatientAddresses extends Command
{
    protected $signature = 'patients:geocode 
                          {--force : Force geocode even if coordinates exist}
                          {--limit= : Limit number of addresses to geocode}
                          {--delay=1 : Delay between requests in seconds}';

    protected $description = 'Geocode patient addresses that are missing latitude/longitude';

    private $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        parent::__construct();
        $this->geocodingService = $geocodingService;
    }

    public function handle()
    {
        $force = $this->option('force');
        $limit = $this->option('limit');
        $delay = $this->option('delay');

        // Get addresses without coordinates
        $query = patient_addresses::query();

        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('latitude')
                    ->orWhereNull('longitude');
            });
        }

        if ($limit) {
            $query->limit($limit);
        }

        $addresses = $query->get();
        $total = $addresses->count();

        if ($total === 0) {
            $this->info('No addresses found to geocode.');
            return 0;
        }

        $this->info("Found {$total} addresses to geocode.");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $successCount = 0;
        $failureCount = 0;
        $skippedCount = 0;

        foreach ($addresses as $address) {
            // Skip if address is incomplete
            if (!$this->isAddressComplete($address)) {
                $this->newLine();
                $this->warn("Skipping incomplete address for patient ID: {$address->patient_id}");
                $skippedCount++;
                $bar->advance();
                continue;
            }

            $addressComponents = [
                'house_number' => $address->house_number,
                'street' => $address->street,
                'purok' => $address->purok,
                'barangay' => $address->barangay,
                'city' => $address->city,
                'province' => $address->province,
                'postal_code' => $address->postal_code,
            ];

            $result = $this->geocodingService->geocodeAddress($addressComponents);

            if ($result['success']) {
                // Validate coordinates are within expected area
                if ($this->geocodingService->validateCoordinates(
                    $result['latitude'],
                    $result['longitude']
                )) {
                    $address->update([
                        'latitude' => $result['latitude'],
                        'longitude' => $result['longitude']
                    ]);
                    $successCount++;
                } else {
                    $this->newLine();
                    $this->warn("Coordinates out of bounds for patient ID: {$address->patient_id}");
                    $failureCount++;
                }
            } else {
                $this->newLine();
                $this->error("Failed to geocode patient ID {$address->patient_id}: {$result['error']}");
                $failureCount++;
            }

            $bar->advance();

            // Delay to respect API rate limits
            if ($delay > 0) {
                sleep($delay);
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Geocoding complete!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Success', $successCount],
                ['Failed', $failureCount],
                ['Skipped', $skippedCount],
                ['Total', $total]
            ]
        );

        return 0;
    }

    private function isAddressComplete($address)
    {
        // Minimum required fields
        return !empty($address->barangay) &&
            !empty($address->city) &&
            !empty($address->province);
    }
}
