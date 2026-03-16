<?php

namespace App\Services;

use App\Models\wra_masterlists;

class WraMasterlistService
{
    /**
     * Minimum age to be included in the WRA masterlist.
     */
    private const MINIMUM_AGE = 10;
    private const MAXIMUM_AGE = 49;

    /**
     * Create a WRA masterlist record only if:
     *   - The patient is female
     *   - The patient is at least 10 years old
     *   - The patient does not already have a WRA masterlist record
     *
     * @param  array  $data  Required keys:
     *   - patient              : the patients model instance
     *   - patient_address      : the patient_addresses model instance
     *   - full_address         : string — formatted full address
     *   - health_worker_id     : int
     *   - medical_record_case_id : int|null
     *   - wra_with_MFP_unmet_need : 'yes'|'no'
     *       'no'  → patient already has FP context (family-planning, prenatal+FP)
     *       'yes' → no FP context (prenatal without FP, vaccination, senior-citizen, tb-dots)
     *
     *  Optional FP-specific keys (only relevant for family-planning callers):
     *   - SE_status
     *   - plan_to_have_more_children_yes
     *   - plan_to_have_more_children_no
     *   - current_FP_methods
     *   - modern_FP
     *   - traditional_FP
     *   - currently_using_any_FP_method_no
     *   - shift_to_modern_method
     *   - wra_accept_any_modern_FP_method
     *   - selected_modern_FP_method
     *   - date_when_FP_method_accepted
     *
     * @return wra_masterlists|null  The created record, or null if skipped.
     */
    public function createIfNotExists(array $data): ?wra_masterlists
    {
        $patient = $data['patient'] ?? null;

        if (!$patient) {
            throw new \InvalidArgumentException('patient model instance is required.');
        }

        // WRA = Women of Reproductive Age — only female patients qualify
        if (empty($patient->sex) || strtolower($patient->sex) !== 'female') {
            return null;
        }

        // Must meet minimum age threshold
        if (($patient->age ?? 0) < self::MINIMUM_AGE || ($patient->age ?? 0) > self::MAXIMUM_AGE) {
            return null;
        }

        // Skip if a masterlist record already exists for this patient
        $exists = wra_masterlists::where('patient_id', $patient->id)->exists();
        if ($exists) {
            return null;
        }

        $patientAddress = $data['patient_address'];

        return wra_masterlists::create([
            'medical_record_case_id'           => $data['medical_record_case_id']           ?? null,
            'health_worker_id'                 => $data['health_worker_id'],
            'address_id'                       => $patientAddress->id,
            'patient_id'                       => $patient->id,
            'brgy_name'                        => $patientAddress->purok,
            'house_hold_number'                => null,
            'name_of_wra'                      => $patient->full_name,
            'address'                          => $data['full_address'],
            'age'                              => $patient->age                              ?? null,
            'date_of_birth'                    => $patient->date_of_birth                   ?? null,

            // FP-specific fields — null by default, only populated by family-planning callers
            'SE_status'                        => $data['SE_status']                        ?? null,
            'plan_to_have_more_children_yes'   => $data['plan_to_have_more_children_yes']   ?? null,
            'plan_to_have_more_children_no'    => $data['plan_to_have_more_children_no']    ?? null,
            'current_FP_methods'               => $data['current_FP_methods']               ?? null,
            'modern_FP'                        => $data['modern_FP']                        ?? null,
            'traditional_FP'                   => $data['traditional_FP']                   ?? null,
            'currently_using_any_FP_method_no' => $data['currently_using_any_FP_method_no'] ?? null,
            'shift_to_modern_method'           => $data['shift_to_modern_method']           ?? null,
            'wra_accept_any_modern_FP_method'  => $data['wra_accept_any_modern_FP_method']  ?? null,
            'selected_modern_FP_method'        => $data['selected_modern_FP_method']        ?? null,
            'date_when_FP_method_accepted'     => $data['date_when_FP_method_accepted']     ?? null,

            'wra_with_MFP_unmet_need'          => $data['wra_with_MFP_unmet_need']          ?? 'yes',
            'status'                           => 'Active',
        ]);
    }

    /**
     * Update only the allowed fields of an existing WRA masterlist record by patient ID.
     *
     * Locked fields (medical_record_case_id, health_worker_id, address_id, patient_id,
     * all FP fields, etc.) will never be touched by this method.
     *
     * @param  int    $patientId
     * @param  array  $data
     * @return wra_masterlists|null  Returns the updated record, or null if not found.
     */
    public function updateByPatientId(int $patientId, array $data): ?wra_masterlists
    {
        $record = wra_masterlists::where('patient_id', $patientId)->first();

        if (!$record) {
            return null;
        }

        $allowedFields = [
            'brgy_name',
            'house_hold_number',
            'name_of_wra',
            'address',
            'age',
            'date_of_birth',
            'status',
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($updateData)) {
            return $record;
        }

        $record->update($updateData);

        return $record->fresh();
    }
}
