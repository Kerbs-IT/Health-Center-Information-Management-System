<?php

namespace App\Http\Controllers;

use App\Models\staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SwapHealthWorkerController extends Controller
{
    /**
     * Get health worker details and available areas for swap
     */
    public function getSwapData($healthWorkerId)
    {
        try {
            $healthWorker = Staff::where('user_id', $healthWorkerId)->firstOrFail();

            // Get all brgy units for dropdown
            $brgyUnits = [
                1 => 'Karlaville Park Homes',
                2 => 'Purok 1',
                3 => 'Purok 2',
                4 => 'Purok 3',
                5 => 'Purok 4',
                6 => 'Purok 5',
                7 => 'Purok 6',
                8 => 'Beverly Homes 1',
                9 => 'Beverly Homes 2',
                10 => 'Green Forbes City',
                11 => 'Gawad Kalinga',
                12 => 'Kaia Homes Phase 2',
                13 => 'Heneral DOS',
                14 => 'SUGAR LAND'
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'health_worker' => [
                        'user_id' => $healthWorker->user_id,
                        'full_name' => $healthWorker->full_name,
                        'profile_image' => $healthWorker->profile_image ?? 'default.png',
                        'assigned_area_id' => $healthWorker->assigned_area_id
                    ],
                    'current_area_id' => $healthWorker->assigned_area_id,
                    'current_area_name' => $brgyUnits[$healthWorker->assigned_area_id] ?? 'Unknown',
                    'available_areas' => $brgyUnits
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching health worker data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform the area swap operation
     */
    public function swapArea(Request $request)
    {
        $request->validate([
            'health_worker_id' => 'required|exists:staff,user_id',
            'new_area_id' => 'required|integer|min:1|max:14'
        ]);

        $healthWorkerId = $request->health_worker_id;
        $newAreaId = $request->new_area_id;

        try {
            DB::beginTransaction();

            // Get current health worker details
            $currentWorker = staff::where('user_id', $healthWorkerId)->firstOrFail();
            $currentAreaId = $currentWorker->assigned_area_id;

            // Check if there's another worker in the new area
            $targetWorker = staff::where('assigned_area_id', $newAreaId)
                ->where('user_id', '!=', $healthWorkerId)
                ->first();

            if ($targetWorker) {
                // SWAP SCENARIO: Two workers exchange areas
                $this->performSwap($currentWorker, $targetWorker, $currentAreaId, $newAreaId);
                $message = "Successfully swapped areas between {$currentWorker->full_name} and {$targetWorker->full_name}";
            } else {
                // REASSIGN SCENARIO: Just move the worker to new area
                $this->performReassignment($currentWorker, $currentAreaId, $newAreaId);
                $message = "Successfully reassigned {$currentWorker->full_name} to new area";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Area swap failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to swap areas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform swap between two health workers
     */
    private function performSwap($worker1, $worker2, $area1Id, $area2Id)
    {
        // Get patients for worker 1 (current area)
        $worker1Patients = $this->getPatientsByArea($area1Id);

        // Get patients for worker 2 (target area)
        $worker2Patients = $this->getPatientsByArea($area2Id);

        // Update all patient records for worker 1's patients to worker 2
        $this->updateAllPatientRecords($worker1Patients, $worker2->user_id);

        // Update all patient records for worker 2's patients to worker 1
        $this->updateAllPatientRecords($worker2Patients, $worker1->user_id);

        // Swap the assigned_area_id
        $worker1->assigned_area_id = $area2Id;
        $worker2->assigned_area_id = $area1Id;

        $worker1->save();
        $worker2->save();
    }

    /**
     * Perform reassignment of a single worker
     */
    private function performReassignment($worker, $oldAreaId, $newAreaId)
    {
        // Get patients from old area
        $oldAreaPatients = $this->getPatientsByArea($oldAreaId);

        // Get patients from new area
        $newAreaPatients = $this->getPatientsByArea($newAreaId);

        // Remove worker assignment from old area patients
        $this->updateAllPatientRecords($oldAreaPatients, null);

        // Assign worker to new area patients
        $this->updateAllPatientRecords($newAreaPatients, $worker->user_id);

        // Update worker's assigned area
        $worker->assigned_area_id = $newAreaId;
        $worker->save();
    }

    /**
     * Get all patient IDs for a specific area
     */
    private function getPatientsByArea($areaId)
    {
        $brgyUnits = $this->getBrgyUnits();
        $areaName = $brgyUnits[$areaId] ?? null;

        if (!$areaName) {
            return collect([]);
        }

        // Get patients based on their address purok
        $patientIds = DB::table('patients')
            ->join('patient_addresses', 'patients.id', '=', 'patient_addresses.patient_id')
            ->where('patient_addresses.purok', $areaName)
            ->pluck('patients.id')
            ->unique();

        return $patientIds;
    }

    /**
     * Get patient count for a specific area (for preview)
     */
    private function getPatientCountByArea($areaId)
    {
        $brgyUnits = $this->getBrgyUnits();
        $areaName = $brgyUnits[$areaId] ?? null;

        if (!$areaName) {
            return 0;
        }

        // Count patients based on their address purok
        $count = DB::table('patients')
            ->join('patient_addresses', 'patients.id', '=', 'patient_addresses.patient_id')
            ->where('patient_addresses.purok', $areaName)
            ->count();

        return $count;
    }

    /**
     * Get brgy units mapping
     */
    private function getBrgyUnits()
    {
        return [
            1 => 'Karlaville Park Homes',
            2 => 'Purok 1',
            3 => 'Purok 2',
            4 => 'Purok 3',
            5 => 'Purok 4',
            6 => 'Purok 5',
            7 => 'Purok 6',
            8 => 'Beverly Homes 1',
            9 => 'Beverly Homes 2',
            10 => 'Green Forbes City',
            11 => 'Gawad Kalinga',
            12 => 'Kaia Homes Phase 2',
            13 => 'Heneral DOS',
            14 => 'SUGAR LAND'
        ];
    }

    /**
     * Update health_worker_id in all patient record tables
     * Uses medical_record_cases as the bridge to find patient records
     */
    private function updateAllPatientRecords($patientIds, $healthWorkerId)
    {
        if ($patientIds->isEmpty()) {
            return;
        }

        // Get all medical_record_case_ids for these patients
        $medicalRecordCaseIds = DB::table('medical_record_cases')
            ->whereIn('patient_id', $patientIds)
            ->pluck('id');

        if ($medicalRecordCaseIds->isEmpty()) {
            return;
        }

        // Tables that use medical_record_case_id
        $tables = [
            'family_planning_case_records',
            'family_planning_medical_records',
            'family_planning_side_b_records',
            'pregnancy_checkups',
            'prenatal_case_records',
            'prenatal_medical_records',
            'senior_citizen_case_records',
            'senior_citizen_medical_records',
            'tb_dots_case_records',
            'tb_dots_medical_records',
            'tb_dots_check_ups',
            'vaccination_case_records',
            'vaccination_masterlists',
            'vaccination_medical_records',
            'wra_masterlists'
        ];

        foreach ($tables as $table) {
            DB::table($table)
                ->whereIn('medical_record_case_id', $medicalRecordCaseIds)
                ->update(['health_worker_id' => $healthWorkerId]);
        }
    }

    /**
     * Preview the impact of a swap
     */
    public function previewSwap(Request $request)
    {
        $request->validate([
            'health_worker_id' => 'required|exists:staff,user_id',
            'new_area_id' => 'required|integer|min:1|max:14'
        ]);

        try {
            $currentWorker = Staff::where('user_id', $request->health_worker_id)->firstOrFail();
            $currentAreaId = $currentWorker->assigned_area_id;
            $newAreaId = $request->new_area_id;

            $brgyUnits = $this->getBrgyUnits();

            // Get patient counts
            $currentAreaPatients = $this->getPatientsByArea($currentAreaId);
            $newAreaPatients = $this->getPatientsByArea($newAreaId);

            // Check for target worker
            $targetWorker = Staff::where('assigned_area_id', $newAreaId)
                ->where('user_id', '!=', $request->health_worker_id)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'current_worker' => [
                        'name' => $currentWorker->full_name,
                        'area' => $brgyUnits[$currentAreaId] ?? 'Unknown',
                        'patient_count' => $currentAreaPatients->count()
                    ],
                    'target_worker' => $targetWorker ? [
                        'name' => $targetWorker->full_name,
                        'area' => $brgyUnits[$newAreaId] ?? 'Unknown',
                        'patient_count' => $newAreaPatients->count()
                    ] : null,
                    'new_area' => [
                        'name' => $brgyUnits[$newAreaId] ?? 'Unknown',
                        'patient_count' => $newAreaPatients->count()
                    ],
                    'is_swap' => $targetWorker !== null,
                    'message' => $targetWorker
                        ? "This will swap areas between {$currentWorker->full_name} and {$targetWorker->full_name}"
                        : "This will reassign {$currentWorker->full_name} to {$brgyUnits[$newAreaId]}"
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error previewing swap: ' . $e->getMessage()
            ], 500);
        }
    }
}
