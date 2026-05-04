<?php

namespace App\Http\Controllers;

use App\Models\staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SwapHealthWorkerController extends Controller
{
    public function getSwapData($healthWorkerId)
    {
        try {
            $healthWorker = Staff::where('user_id', $healthWorkerId)->firstOrFail();
            $brgyUnits = $this->getBrgyUnits();

            return response()->json([
                'success' => true,
                'data' => [
                    'health_worker' => [
                        'user_id'          => $healthWorker->user_id,
                        'full_name'        => $healthWorker->full_name,
                        'profile_image'    => $healthWorker->profile_image ?? 'default.png',
                        'assigned_area_id' => $healthWorker->assigned_area_id
                    ],
                    'current_area_id'   => $healthWorker->assigned_area_id,
                    'current_area_name' => $brgyUnits[$healthWorker->assigned_area_id] ?? 'Unknown',
                    'available_areas'   => $brgyUnits
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching health worker data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function swapArea(Request $request)
    {
        $request->validate([
            'health_worker_id' => 'required|exists:staff,user_id',
            'new_area_id'      => 'required|integer|min:1|max:14'
        ]);

        try {
            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $currentWorker = staff::where('user_id', $request->health_worker_id)->firstOrFail();
            $currentAreaId = $currentWorker->assigned_area_id;
            $newAreaId     = (int) $request->new_area_id;

            if ($currentAreaId == $newAreaId) {
                DB::rollBack();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                return response()->json([
                    'success' => false,
                    'message' => 'Health worker is already assigned to this area.'
                ], 422);
            }

            $targetWorker = staff::where('assigned_area_id', $newAreaId)
                ->where('user_id', '!=', $currentWorker->user_id)
                ->first();

            if ($targetWorker) {
                $this->performSwap($currentWorker, $targetWorker, $currentAreaId, $newAreaId);
                $message = "Successfully swapped areas between {$currentWorker->full_name} and {$targetWorker->full_name}";
            } else {
                $this->performReassignment($currentWorker, $newAreaId);
                $message = "Successfully reassigned {$currentWorker->full_name} to new area";
            }

            DB::commit();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Log::error('Area swap failed: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to swap areas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * THE KEY FIX: Use patient ADDRESS as source of truth, not health_worker_id.
     * Get all medical_record_case_ids for patients whose address matches the area name.
     */
    private function getCaseIdsByAddress(string $areaName): array
    {
        return DB::table('wra_masterlists')
            ->where('status', '!=', 'Archived')
            ->where(function ($q) use ($areaName) {
                $q->where('address', 'LIKE', '%' . $areaName . '%')
                    ->orWhere('brgy_name', $areaName);
            })
            ->whereNotNull('medical_record_case_id')
            ->pluck('medical_record_case_id')
            ->unique()
            ->toArray();
    }

    private function performSwap($worker1, $worker2, $area1Id, $area2Id)
    {
        $brgyUnits = $this->getBrgyUnits();
        $area1Name = $brgyUnits[$area1Id]; // e.g. "Purok 5"
        $area2Name = $brgyUnits[$area2Id]; // e.g. "Gawad Kalinga"

        // Source of truth: patient ADDRESS, not current health_worker_id
        // This is immune to previous corruptions
        $area1CaseIds = $this->getCaseIdsByAddress($area1Name); // Purok 5 case IDs
        $area2CaseIds = $this->getCaseIdsByAddress($area2Name); // Gawad Kalinga case IDs

        Log::info("Swap: {$area1Name} has " . count($area1CaseIds) . " cases, {$area2Name} has " . count($area2CaseIds) . " cases");

        $allTables = [
            'wra_masterlists',
            'vaccination_masterlists',
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

        foreach ($allTables as $table) {
            // Step 1: area1 patients → NULL (temp)
            if (!empty($area1CaseIds)) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $area1CaseIds)
                    ->whereNotNull('health_worker_id')
                    ->update(['health_worker_id' => null]);
            }

            // Step 2: area2 patients → worker1 (they move to worker1's area)
            if (!empty($area2CaseIds)) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $area2CaseIds)
                    ->update(['health_worker_id' => $worker1->user_id]);
            }

            // Step 3: NULL (area1 patients) → worker2
            if (!empty($area1CaseIds)) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $area1CaseIds)
                    ->whereNull('health_worker_id')
                    ->update(['health_worker_id' => $worker2->user_id]);
            }
        }

        // Also fix brgy_name in masterlists to match correctly
        if (!empty($area1CaseIds)) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $area1CaseIds)
                ->update(['brgy_name' => $area1Name]);

            DB::table('vaccination_masterlists')
                ->whereIn('medical_record_case_id', $area1CaseIds)
                ->update(['brgy_name' => $area1Name]);
        }

        if (!empty($area2CaseIds)) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $area2CaseIds)
                ->update(['brgy_name' => $area2Name]);

            DB::table('vaccination_masterlists')
                ->whereIn('medical_record_case_id', $area2CaseIds)
                ->update(['brgy_name' => $area2Name]);
        }

        // Swap staff assigned areas
        $worker1->assigned_area_id = $area2Id;
        $worker2->assigned_area_id = $area1Id;
        $worker1->save();
        $worker2->save();
    }

    private function performReassignment($worker, $newAreaId)
    {
        $brgyUnits   = $this->getBrgyUnits();
        $newAreaName = $brgyUnits[$newAreaId];

        // Unassign ALL current patients of this worker
        $currentCaseIds = DB::table('wra_masterlists')
            ->where('health_worker_id', $worker->user_id)
            ->where('status', '!=', 'Archived')
            ->pluck('medical_record_case_id')
            ->toArray();

        $allTables = [
            'wra_masterlists',
            'vaccination_masterlists',
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

        if (!empty($currentCaseIds)) {
            foreach ($allTables as $table) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $currentCaseIds)
                    ->update(['health_worker_id' => null]);
            }
        }

        // Assign new area's patients (by address) to this worker
        $newAreaCaseIds = $this->getCaseIdsByAddress($newAreaName);

        if (!empty($newAreaCaseIds)) {
            foreach ($allTables as $table) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $newAreaCaseIds)
                    ->update(['health_worker_id' => $worker->user_id]);
            }
        }

        $worker->assigned_area_id = $newAreaId;
        $worker->save();
    }

    private function getPatientsByArea($areaId)
    {
        $brgyUnits = $this->getBrgyUnits();
        $areaName  = $brgyUnits[$areaId] ?? null;

        if (!$areaName) return collect([]);

        return DB::table('patients')
            ->join('patient_addresses', 'patients.id', '=', 'patient_addresses.patient_id')
            ->where('patient_addresses.purok', $areaName)
            ->pluck('patients.id')
            ->unique();
    }

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

    public function previewSwap(Request $request)
    {
        $request->validate([
            'health_worker_id' => 'required|exists:staff,user_id',
            'new_area_id'      => 'required|integer|min:1|max:14'
        ]);

        try {
            $currentWorker = Staff::where('user_id', $request->health_worker_id)->firstOrFail();
            $currentAreaId = $currentWorker->assigned_area_id;
            $newAreaId     = $request->new_area_id;
            $brgyUnits     = $this->getBrgyUnits();

            $currentAreaPatients = $this->getPatientsByArea($currentAreaId);
            $newAreaPatients     = $this->getPatientsByArea($newAreaId);

            $targetWorker = Staff::where('assigned_area_id', $newAreaId)
                ->where('user_id', '!=', $request->health_worker_id)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'current_worker' => [
                        'name'          => $currentWorker->full_name,
                        'area'          => $brgyUnits[$currentAreaId] ?? 'Unknown',
                        'patient_count' => $currentAreaPatients->count()
                    ],
                    'target_worker' => $targetWorker ? [
                        'name'          => $targetWorker->full_name,
                        'area'          => $brgyUnits[$newAreaId] ?? 'Unknown',
                        'patient_count' => $newAreaPatients->count()
                    ] : null,
                    'new_area' => [
                        'name'          => $brgyUnits[$newAreaId] ?? 'Unknown',
                        'patient_count' => $newAreaPatients->count()
                    ],
                    'is_swap'  => $targetWorker !== null,
                    'message'  => $targetWorker
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
