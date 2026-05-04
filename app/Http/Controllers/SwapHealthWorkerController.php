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

            $brgyUnits = $this->getBrgyUnits();

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

            $currentWorker = staff::where('user_id', $healthWorkerId)->firstOrFail();
            $currentAreaId = $currentWorker->assigned_area_id;

            if ($currentAreaId == $newAreaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Health worker is already assigned to this area.'
                ], 422);
            }

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
     * Perform swap between two health workers.
     * Only health_worker_id is updated — brgy_name is NEVER changed
     * because it reflects the patient's actual address, not the worker's area.
     */
    private function performSwap($worker1, $worker2, $area1Id, $area2Id)
    {
        // Get case IDs directly from wra_masterlists by health_worker_id
        $worker1CaseIds = DB::table('wra_masterlists')
            ->where('health_worker_id', $worker1->user_id)
            ->pluck('medical_record_case_id');

        $worker2CaseIds = DB::table('wra_masterlists')
            ->where('health_worker_id', $worker2->user_id)
            ->pluck('medical_record_case_id');

        // Swap health_worker_id on wra_masterlists — do NOT touch brgy_name
        if ($worker1CaseIds->isNotEmpty()) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $worker1CaseIds)
                ->update(['health_worker_id' => $worker2->user_id]);
        }

        if ($worker2CaseIds->isNotEmpty()) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $worker2CaseIds)
                ->update(['health_worker_id' => $worker1->user_id]);
        }

        // Swap health_worker_id on all other record tables
        $this->swapWorkerOnTables($worker1CaseIds, $worker2CaseIds, $worker1->user_id, $worker2->user_id);

        // Swap assigned_area_id on staff records
        $worker1->assigned_area_id = $area2Id;
        $worker2->assigned_area_id = $area1Id;
        $worker1->save();
        $worker2->save();
    }

    /**
     * Swap health_worker_id on all related record tables (excluding masterlists
     * which are handled separately to avoid touching brgy_name).
     */
    private function swapWorkerOnTables($worker1CaseIds, $worker2CaseIds, $worker1Id, $worker2Id)
    {
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
            'vaccination_masterlists',
        ];

        foreach ($tables as $table) {
            if ($worker1CaseIds->isNotEmpty()) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $worker1CaseIds)
                    ->update(['health_worker_id' => $worker2Id]);
            }

            if ($worker2CaseIds->isNotEmpty()) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $worker2CaseIds)
                    ->update(['health_worker_id' => $worker1Id]);
            }
        }
    }

    /**
     * Reassign a worker to a new area with no existing worker.
     * Unassigns current patients and takes over the new area's patients.
     */
    private function performReassignment($worker, $oldAreaId, $newAreaId)
    {
        // Get this worker's current case IDs from wra_masterlists
        $workerCaseIds = DB::table('wra_masterlists')
            ->where('health_worker_id', $worker->user_id)
            ->pluck('medical_record_case_id');

        // Unassign current patients across all tables
        if ($workerCaseIds->isNotEmpty()) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $workerCaseIds)
                ->update(['health_worker_id' => null]);

            DB::table('vaccination_masterlists')
                ->whereIn('medical_record_case_id', $workerCaseIds)
                ->update(['health_worker_id' => null]);

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
                    ->whereIn('medical_record_case_id', $workerCaseIds)
                    ->update(['health_worker_id' => null]);
            }
        }

        // If the new area has an existing worker, take over their patients
        $newAreaWorker = staff::where('assigned_area_id', $newAreaId)
            ->where('user_id', '!=', $worker->user_id)
            ->first();

        if ($newAreaWorker) {
            $newAreaCaseIds = DB::table('wra_masterlists')
                ->where('health_worker_id', $newAreaWorker->user_id)
                ->pluck('medical_record_case_id');

            if ($newAreaCaseIds->isNotEmpty()) {
                DB::table('wra_masterlists')
                    ->whereIn('medical_record_case_id', $newAreaCaseIds)
                    ->update(['health_worker_id' => $worker->user_id]);

                DB::table('vaccination_masterlists')
                    ->whereIn('medical_record_case_id', $newAreaCaseIds)
                    ->update(['health_worker_id' => $worker->user_id]);

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
                        ->whereIn('medical_record_case_id', $newAreaCaseIds)
                        ->update(['health_worker_id' => $worker->user_id]);
                }
            }
        }

        $worker->assigned_area_id = $newAreaId;
        $worker->save();
    }

    /**
     * Get all patient IDs for a specific area (used for preview count only).
     */
    private function getPatientsByArea($areaId)
    {
        $brgyUnits = $this->getBrgyUnits();
        $areaName = $brgyUnits[$areaId] ?? null;

        if (!$areaName) {
            return collect([]);
        }

        return DB::table('patients')
            ->join('patient_addresses', 'patients.id', '=', 'patient_addresses.patient_id')
            ->where('patient_addresses.purok', $areaName)
            ->pluck('patients.id')
            ->unique();
    }

    /**
     * Get brgy units mapping.
     */
    private function getBrgyUnits()
    {
        return [
            1  => 'Karlaville Park Homes',
            2  => 'Purok 1',
            3  => 'Purok 2',
            4  => 'Purok 3',
            5  => 'Purok 4',
            6  => 'Purok 5',
            7  => 'Purok 6',
            8  => 'Beverly Homes 1',
            9  => 'Beverly Homes 2',
            10 => 'Green Forbes City',
            11 => 'Gawad Kalinga',
            12 => 'Kaia Homes Phase 2',
            13 => 'Heneral DOS',
            14 => 'SUGAR LAND'
        ];
    }

    /**
     * Preview the impact of a swap.
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

            // Count patients by actual address for preview accuracy
            $currentAreaPatients = $this->getPatientsByArea($currentAreaId);
            $newAreaPatients = $this->getPatientsByArea($newAreaId);

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
