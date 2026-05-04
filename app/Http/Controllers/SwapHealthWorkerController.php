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

        $healthWorkerId = $request->health_worker_id;
        $newAreaId      = $request->new_area_id;

        try {
            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $currentWorker = staff::where('user_id', $healthWorkerId)->firstOrFail();
            $currentAreaId = $currentWorker->assigned_area_id;

            if ($currentAreaId == $newAreaId) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                return response()->json([
                    'success' => false,
                    'message' => 'Health worker is already assigned to this area.'
                ], 422);
            }

            $targetWorker = staff::where('assigned_area_id', $newAreaId)
                ->where('user_id', '!=', $healthWorkerId)
                ->first();

            if ($targetWorker) {
                $this->performSwap($currentWorker, $targetWorker, $currentAreaId, $newAreaId);
                $message = "Successfully swapped areas between {$currentWorker->full_name} and {$targetWorker->full_name}";
            } else {
                $this->performReassignment($currentWorker, $currentAreaId, $newAreaId);
                $message = "Successfully reassigned {$currentWorker->full_name} to new area";
            }

            DB::commit();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Log::error('Area swap failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to swap areas: ' . $e->getMessage()
            ], 500);
        }
    }

    private function performSwap($worker1, $worker2, $area1Id, $area2Id)
    {
        // Snapshot BOTH sets of case IDs before ANY updates
        $worker1CaseIds = DB::table('wra_masterlists')
            ->where('health_worker_id', $worker1->user_id)
            ->pluck('medical_record_case_id')
            ->toArray();

        $worker2CaseIds = DB::table('wra_masterlists')
            ->where('health_worker_id', $worker2->user_id)
            ->pluck('medical_record_case_id')
            ->toArray();

        $w1 = collect($worker1CaseIds);
        $w2 = collect($worker2CaseIds);

        // wra_masterlists: 3-step swap using NULL as temp
        if ($w1->isNotEmpty()) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $worker1CaseIds)
                ->update(['health_worker_id' => null]);          // Step 1: w1 → NULL
        }
        if ($w2->isNotEmpty()) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $worker2CaseIds)
                ->update(['health_worker_id' => $worker1->user_id]); // Step 2: w2 → w1
        }
        if ($w1->isNotEmpty()) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $worker1CaseIds)
                ->whereNull('health_worker_id')
                ->update(['health_worker_id' => $worker2->user_id]); // Step 3: NULL → w2
        }

        // All other tables: same 3-step pattern
        $this->swapWorkerOnTablesWithTemp($w1, $w2, $worker1->user_id, $worker2->user_id);

        // Swap assigned areas on staff
        $worker1->assigned_area_id = $area2Id;
        $worker2->assigned_area_id = $area1Id;
        $worker1->save();
        $worker2->save();
    }

    private function swapWorkerOnTablesWithTemp($w1CaseIds, $w2CaseIds, $worker1Id, $worker2Id)
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
            // Step 1: worker1's records → NULL
            if ($w1CaseIds->isNotEmpty()) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $w1CaseIds->toArray())
                    ->update(['health_worker_id' => null]);
            }

            // Step 2: worker2's records → worker1
            if ($w2CaseIds->isNotEmpty()) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $w2CaseIds->toArray())
                    ->update(['health_worker_id' => $worker1Id]);
            }

            // Step 3: NULL records (worker1's old) → worker2
            if ($w1CaseIds->isNotEmpty()) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $w1CaseIds->toArray())
                    ->whereNull('health_worker_id')
                    ->update(['health_worker_id' => $worker2Id]);
            }
        }
    }

    private function performReassignment($worker, $oldAreaId, $newAreaId)
    {
        $workerCaseIds = DB::table('wra_masterlists')
            ->where('health_worker_id', $worker->user_id)
            ->pluck('medical_record_case_id')
            ->toArray();

        $allTables = [
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

        // Unassign current patients
        if (!empty($workerCaseIds)) {
            DB::table('wra_masterlists')
                ->whereIn('medical_record_case_id', $workerCaseIds)
                ->update(['health_worker_id' => null]);

            DB::table('vaccination_masterlists')
                ->whereIn('medical_record_case_id', $workerCaseIds)
                ->update(['health_worker_id' => null]);

            foreach ($allTables as $table) {
                DB::table($table)
                    ->whereIn('medical_record_case_id', $workerCaseIds)
                    ->update(['health_worker_id' => null]);
            }
        }

        // Take over new area's patients if any
        $newAreaWorker = staff::where('assigned_area_id', $newAreaId)
            ->where('user_id', '!=', $worker->user_id)
            ->first();

        if ($newAreaWorker) {
            $newAreaCaseIds = DB::table('wra_masterlists')
                ->where('health_worker_id', $newAreaWorker->user_id)
                ->pluck('medical_record_case_id')
                ->toArray();

            if (!empty($newAreaCaseIds)) {
                DB::table('wra_masterlists')
                    ->whereIn('medical_record_case_id', $newAreaCaseIds)
                    ->update(['health_worker_id' => $worker->user_id]);

                DB::table('vaccination_masterlists')
                    ->whereIn('medical_record_case_id', $newAreaCaseIds)
                    ->update(['health_worker_id' => $worker->user_id]);

                foreach ($allTables as $table) {
                    DB::table($table)
                        ->whereIn('medical_record_case_id', $newAreaCaseIds)
                        ->update(['health_worker_id' => $worker->user_id]);
                }
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
