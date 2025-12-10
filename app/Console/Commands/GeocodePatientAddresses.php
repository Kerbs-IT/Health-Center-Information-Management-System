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
                          {--delay=1 : Delay between requests in seconds}
                          {--debug : Show detailed debug info}';

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
        $debug = $this->option('debug');

        // 🔧 DEBUG: Show available puroks from config
        if ($debug) {
            $this->info('=== AVAILABLE PUROKS IN CONFIG ===');
            $coordinates = config('coordinates.hugo_perez');
            if (isset($coordinates['puroks'])) {
                foreach (array_keys($coordinates['puroks']) as $purok) {
                    $this->line("  - '{$purok}'");
                }
            }
            $this->newLine();
        }

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

        // 🔧 DEBUG: Show unique puroks from database
        if ($debug) {
            $this->info('=== UNIQUE PUROKS IN DATABASE ===');
            $uniquePuroks = $addresses->pluck('purok')->unique()->filter()->values();
            foreach ($uniquePuroks as $purok) {
                $this->line("  - '{$purok}'");
            }
            $this->newLine();
        }

        $this->info("Found {$total} addresses to geocode.");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $successCount = 0;
        $failureCount = 0;
        $skippedCount = 0;
        $errors = []; // Track unique errors

        foreach ($addresses as $address) {
            if (!$this->isAddressComplete($address)) {
                if ($debug) {
                    $this->newLine();
                    $this->warn("Skipping incomplete address for patient ID: {$address->patient_id}");
                }
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

            // 🔧 DEBUG: Show what we're trying to geocode
            if ($debug && $failureCount < 3) { // Only show first 3 to avoid spam
                $this->newLine();
                $this->info("Attempting patient ID: {$address->patient_id}");
                $this->line("  Purok: '{$address->purok}'");
                $this->line("  Barangay: '{$address->barangay}'");
            }

            $result = $this->geocodingService->geocodeAddress($addressComponents);

            if ($result['success']) {
                if ($this->geocodingService->validateCoordinates(
                    $result['latitude'],
                    $result['longitude']
                )) {
                    $address->update([
                        'latitude' => $result['latitude'],
                        'longitude' => $result['longitude']
                    ]);
                    $successCount++;

                    if ($debug) {
                        $this->info("  ✓ Success via: {$result['source']}");
                    }
                } else {
                    if ($debug) {
                        $this->newLine();
                        $this->warn("Coordinates out of bounds for patient ID: {$address->patient_id}");
                    }
                    $failureCount++;
                    $errors['out_of_bounds'] = ($errors['out_of_bounds'] ?? 0) + 1;
                }
            } else {
                if ($debug) {
                    $this->newLine();
                    $this->error("Failed to geocode patient ID {$address->patient_id}: {$result['error']}");
                }
                $failureCount++;

                // Track error types
                $errorKey = $result['error'] ?? 'unknown';
                $errors[$errorKey] = ($errors[$errorKey] ?? 0) + 1;
            }

            $bar->advance();

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

        // 🔧 Show error breakdown
        if (!empty($errors)) {
            $this->newLine();
            $this->warn('Error breakdown:');
            foreach ($errors as $error => $count) {
                $this->line("  - {$error}: {$count}");
            }
        }

        return 0;
    }

    private function isAddressComplete($address)
    {
        return !empty($address->barangay) &&
            !empty($address->city) &&
            !empty($address->province);
    }
}
