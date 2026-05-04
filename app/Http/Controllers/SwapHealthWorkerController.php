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
        // Get patients by health_worker_id, NOT by address
        $worker1PatientIds = $this->getPatientsByWorker($worker1->user_id);
        $worker2PatientIds = $this->getPatientsByWorker($worker2->user_id);

        $brgyUnits = $this->getBrgyUnits();

        // Update worker1's patients to worker2 with worker2's new area name
        $this->updateAllPatientRecords($worker1PatientIds, $worker2->user_id, $brgyUnits[$area2Id]);

        // Update worker2's patients to worker1 with worker1's new area name  
        $this->updateAllPatientRecords($worker2PatientIds, $worker1->user_id, $brgyUnits[$area1Id]);

        // Swap the assigned_area_id
        $worker1->assigned_area_id = $area2Id;
        $worker2->assigned_area_id = $area1Id;

        $worker1->save();
        $worker2->save();
    }

    private function performReassignment($worker, $oldAreaId, $newAreaId)
    {
        $brgyUnits = $this->getBrgyUnits();

        // Get patients currently under this worker
        $workerPatients = $this->getPatientsByWorker($worker->user_id);

        // Remove assignment from old patients
        $this->updateAllPatientRecords($workerPatients, null, null);

        // Get patients in new area (under the new area's current worker if any)
        $newAreaWorker = staff::where('assigned_area_id', $newAreaId)
            ->where('user_id', '!=', $worker->user_id)
            ->first();

        if ($newAreaWorker) {
            $newAreaPatients = $this->getPatientsByWorker($newAreaWorker->user_id);
            $this->updateAllPatientRecords($newAreaPatients, $worker->user_id, $brgyUnits[$newAreaId]);
        }

        $worker->assigned_area_id = $newAreaId;
        $worker->save();
    }

    // NEW: Get patients by health_worker_id from wra_masterlists
    private function getPatientsByWorker($healthWorkerId)
    {
        return DB::table('medical_record_cases')
            ->whereIn('id', function ($q) use ($healthWorkerId) {
                $q->select('medical_record_case_id')
                    ->from('wra_masterlists')
                    ->where('health_worker_id', $healthWorkerId);
            })
            ->pluck('patient_id')
            ->unique();
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
    private function updateAllPatientRecords($patientIds, $healthWorkerId, $brgyName = null)
    {
        if ($patientIds->isEmpty()) return;

        $medicalRecordCaseIds = DB::table('medical_record_cases')
            ->whereIn('patient_id', $patientIds)
            ->pluck('id');

        if ($medicalRecordCaseIds->isEmpty()) return;

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
            'vaccination_medical_records',
        ];

        foreach ($tables as $table) {
            DB::table($table)
                ->whereIn('medical_record_case_id', $medicalRecordCaseIds)
                ->update(['health_worker_id' => $healthWorkerId]);
        }

        // Update masterlists with BOTH health_worker_id AND brgy_name
        $masterlistUpdate = ['health_worker_id' => $healthWorkerId];
        if ($brgyName) {
            $masterlistUpdate['brgy_name'] = $brgyName;
        }

        DB::table('wra_masterlists')
            ->whereIn('medical_record_case_id', $medicalRecordCaseIds)
            ->update($masterlistUpdate);

        DB::table('vaccination_masterlists')
            ->whereIn('medical_record_case_id', $medicalRecordCaseIds)
            ->update($masterlistUpdate);
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
