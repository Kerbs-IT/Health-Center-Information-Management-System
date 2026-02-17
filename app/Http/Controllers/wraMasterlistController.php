<?php

namespace App\Http\Controllers;

use App\Models\family_planning_case_records;
use App\Models\patient_addresses;
use App\Models\wra_masterlists;
use App\Services\PatientUpdateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class wraMasterlistController extends Controller
{
    //
    public function update(Request $request, $id)
    {
        try {
            $wra_masterlistRecord = wra_masterlists::with('patient')->findOrFail($id);

            $data = $request->validate([
                'house_hold_number' => 'sometimes|nullable|string',
                'wra_masterlist_fname' => 'required',
                'wra_masterlist_lname' => 'required',
                'wra_masterlist_MI' => 'sometimes|nullable|string',
                'wra_masterlist_suffix' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy' => 'required',
                'sex' => 'sometimes|nullable|string',
                'age' => 'required|numeric|max:50',
                'date_of_birth' => 'required|date|before_or_equal:today',
                'SE_status' => 'sometimes|nullable|string',
                'plan_to_have_more_children' => 'sometimes|nullable|string',
                'plan_to_have_more_children_yes' => 'sometimes|nullable|string',
                'currently_using_any_FP_method' => 'sometimes|nullable|string',
                'currently_using_methods' => 'sometimes|nullable|array',
                'wra_with_MFP_unmet_need' => 'sometimes|nullable|string',
                'shift_to_modern_method' => 'sometimes|nullable|string',
                'wra_accept_any_modern_FP_method' => 'sometimes|nullable|string',
                'date_when_FP_method_accepted' => 'sometimes|required_if:wra_accept_any_modern_FP_method,yes|nullable|string',
                'selected_modern_FP_method' => 'sometimes|required_if:wra_accept_any_modern_FP_method,yes|nullable|array',
            ], [
                'house_hold_number.string' => 'The household number must be a string.',
                'wra_masterlist_fname.required' => 'The first name field is required.',
                'wra_masterlist_lname.required' => 'The last name field is required.',
                'wra_masterlist_MI.string' => 'The middle initial must be a string.',
                'age.required' => 'The age field is required.',
                'age.numeric' => 'The age must be a number.',
                'age.max' => 'The age may not be greater than :max.',
                'date_of_birth.required' => 'The date of birth field is required.',
                'date_of_birth.date' => 'The date of birth must be a valid date.',
                'date_of_birth.before_or_equal' => 'The date of birth must be today or earlier.',
                'SE_status.string' => 'The SE status must be a string.',
                'plan_to_have_more_children.string' => 'The plan to have more children field must be a string.',
                'currently_using_any_FP_method.string' => 'The currently using any FP method field must be a string.',
                'wra_with_MFP_unmet_need.string' => 'The WRA with MFP unmet need field must be a string.',
                'shift_to_modern_method.string' => 'The shift to modern method field must be a string.',
                'wra_accept_any_modern_FP_method.string' => 'The accept any modern FP method field must be a string.',
                'date_when_FP_method_accepted.required_if' => 'The date when FP method accepted field is required when accept any modern FP method is yes.',
                'date_when_FP_method_accepted.string' => 'The date when FP method accepted must be a string.',
                'selected_modern_FP_method.required_if' => 'The selected modern FP method field is required when accept any modern FP method is yes.',
                'selected_modern_FP_method.array' => 'The selected modern FP method must be an array.',
            ]);

            // update the user side
            $serviceData = [
                'first_name'     => $data['wra_masterlist_fname'],
                'last_name'      => $data['wra_masterlist_lname'],
                'middle_initial' => $data['wra_masterlist_MI']     ?? null,
                'suffix'         => $data['wra_masterlist_suffix'] ?? null,
                'street'         => $data['street'],
                'brgy'           => $data['brgy'],
                'sex'            => $data['sex']                   ?? 'Female',
                'date_of_birth'  => $data['date_of_birth'],
                'contact_number' => null,
                'civil_status'   => null,
                'place_of_birth' => null,
                'nationality'    => null,
            ];

            $patientUpdateService = new PatientUpdateService();
            $updatePatientRelatedTables = $patientUpdateService->updatePatientDetails($serviceData, $wra_masterlistRecord->patient_id);

            $middle = substr($data['wra_masterlist_MI'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $parts = [
                strtolower($data['wra_masterlist_fname']),
                $middle,
                strtolower($data['wra_masterlist_lname']),
                $data['wra_masterlist_suffix'] ?? null,
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));

            // FIX 1: Guard against null patient relation
            if ($wra_masterlistRecord->patient) {
                $wra_masterlistRecord->patient->update([
                    'first_name' => ucwords(strtolower($data['wra_masterlist_fname'] ?? $wra_masterlistRecord->patient->first_name)),
                    'middle_initial' => !empty($data['wra_masterlist_MI']) ? ucwords(strtolower($data['wra_masterlist_MI'])) : null,
                    'last_name' => ucwords(strtolower($data['wra_masterlist_lname'] ?? $wra_masterlistRecord->patient->last_name)),
                    'full_name' => $fullName ?? $wra_masterlistRecord->patient->full_name,
                    'sex' => 'Female',
                    'age' => $data['age'] ?? $wra_masterlistRecord->patient->age,
                    'date_of_birth' => $data['date_of_birth'] ?? $wra_masterlistRecord->patient->date_of_birth,
                    'suffix' => $data['wra_masterlist_suffix'] ?? ''
                ]);
            }

            // Update the address
            $address = patient_addresses::where('patient_id', $wra_masterlistRecord->patient_id)->firstOrFail();
            $blk_n_street = explode(',', $data['street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street' => $blk_n_street[1] ?? $address->street,
                'purok' => $data['brgy'] ?? $address->purok
            ]);

            // Handle the traditional and modern current FP method
            $method_of_FP = [
                'modern' => ['Implant', 'IUD', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
            ];

            $modern_methods = [];
            $traditional_methods = [];
            $converted_current_FP_method = '';
            $selected_modern_FP_methods = '';
            $converted_modern_methods = '';
            $converted_traditional_methods = '';

            if (!empty($data['currently_using_methods'])) {
                foreach ($data['currently_using_methods'] as $method) {
                    if (in_array($method, $method_of_FP['modern'])) {
                        $modern_methods[] = $method;
                    } elseif (in_array($method, $method_of_FP['traditional'])) {
                        $traditional_methods[] = $method;
                    }
                }
                $converted_modern_methods = implode(",", $modern_methods);
                $converted_traditional_methods = implode(",", $traditional_methods);
                $converted_current_FP_method = implode(",", $data['currently_using_methods']);
            }

            if (!empty($data['selected_modern_FP_method'])) {
                $selected_modern_FP_methods = implode(",", $data['selected_modern_FP_method']);
            }

            // Refresh the new address
            $address->refresh();

            // FIX 2: Null-safe address string concatenation
            $newAddress = implode(",", array_filter([
                $address->house_number ?? null,
                $address->street ?? null,
                $address->purok ?? null,
                $address->barangay ?? null,
                $address->city ?? null,
                $address->province ?? null,
            ]));

            $wra_masterlistRecord->update([
                'name_of_wra' => $fullName,
                'house_hold_number' => $data['house_hold_number'] ?? $wra_masterlistRecord->house_hold_number,
                'Address' => $newAddress,
                'sex' => 'Female',
                'age' => $data['age'] ?? $wra_masterlistRecord->age,
                'SE_status' => $data['SE_status'] ?? $wra_masterlistRecord->SE_status,
                'plan_to_have_more_children_yes' => ($data['plan_to_have_more_children'] ?? null) === 'Yes'
                    ? ($data['plan_to_have_more_children_yes'] ?? $wra_masterlistRecord->plan_to_have_more_children_yes)
                    : null,
                'plan_to_have_more_children_no' => ($data['plan_to_have_more_children'] ?? null) === 'No'
                    ? 'limiting' ?? $wra_masterlistRecord->plan_to_have_more_children_no
                    : null,
                'current_FP_methods' => ($data['currently_using_any_FP_method'] ?? null) === 'yes'
                    ? $converted_current_FP_method ?? $wra_masterlistRecord->current_FP_methods
                    : null,
                'currently_using_any_FP_method_no' => ($data['currently_using_any_FP_method'] ?? null) === 'no'
                    ? 'yes' ?? $wra_masterlistRecord->currently_using_any_FP_method_no
                    : null,
                'shift_to_modern_method' => $data['shift_to_modern_method'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? $wra_masterlistRecord->date_of_birth,
                'wra_with_MFP_unmet_need' => $data['wra_with_MFP_unmet_need'] ?? null,
                'wra_accept_any_modern_FP_method' => $data['wra_accept_any_modern_FP_method'] ?? null,
                'selected_modern_FP_method' => ($data['wra_accept_any_modern_FP_method'] ?? null) === 'yes'
                    ? $selected_modern_FP_methods ?? $wra_masterlistRecord->selected_modern_FP_method
                    : null,
                'date_when_FP_method_accepted' => ($data['wra_accept_any_modern_FP_method'] ?? null) === 'yes'
                    ? $data['date_when_FP_method_accepted'] ?? $wra_masterlistRecord->date_when_FP_method_accepted
                    : null,
                'modern_FP' => $converted_modern_methods,
                'traditional_FP' => $converted_traditional_methods
            ]);

            // Update the family planning case
            $familyPlanningCase = family_planning_case_records::where('medical_record_case_id', $wra_masterlistRecord->medical_record_case_id)->first();

            if ($familyPlanningCase) {
                $old_choosen_method = $familyPlanningCase->choosen_method
                    ? array_map('trim', explode(",", $familyPlanningCase->choosen_method))
                    : [];

                $new_selected_modern_FP = $data['selected_modern_FP_method'] ?? [];

                $old_traditional = array_diff($old_choosen_method, $method_of_FP['modern']);
                $final = array_merge($new_selected_modern_FP, $old_traditional);
                $converted_choosen_method = implode(",", $final);

                // FIX 3: Null-safe type_of_patient before building dynamic column name
                $reasonColumn = null;
                if (!empty($familyPlanningCase->type_of_patient)) {
                    $exploded_typeOfPatient = explode(" ", $familyPlanningCase->type_of_patient);
                    $merge_typeOfPatient = implode("_", $exploded_typeOfPatient);
                    $reasonColumn = $merge_typeOfPatient . "_reason_for_FP";
                }

                $fpUpdateData = [
                    'NHTS' => $data['SE_status'] ?? $familyPlanningCase->NHTS,
                    'client_name' => $fullName ?? $familyPlanningCase->client_name,
                    'client_date_of_birth' => $data['date_of_birth'] ?? $familyPlanningCase->client_date_of_birth,
                    'client_age' => $data['age'] ?? $familyPlanningCase->client_age,
                    'plan_to_have_more_children' => $data['plan_to_have_more_children'] ?? $familyPlanningCase->plan_to_have_more_children,
                    'previously_used_method' => $converted_current_FP_method ?? $familyPlanningCase->previously_used_method,
                    'choosen_method' => $converted_choosen_method ?? $familyPlanningCase->choosen_method,
                    'date_of_acknowledgement' => ($data['wra_accept_any_modern_FP_method'] ?? null) === 'yes'
                        ? $data['date_when_FP_method_accepted']
                        : $familyPlanningCase->date_of_acknowledgement,
                    // FIX 4: Was incorrectly using ->date_of_acknowledgement instead of ->date_of_acknowledgement_consent
                    'date_of_acknowledgement_consent' => ($data['wra_accept_any_modern_FP_method'] ?? null) === 'yes'
                        ? $data['date_when_FP_method_accepted']
                        : $familyPlanningCase->date_of_acknowledgement_consent,
                ];

                // Only include the reason column if type_of_patient was valid
                if ($reasonColumn !== null) {
                    $fpUpdateData[$reasonColumn] = $data['plan_to_have_more_children_yes'] ?? $familyPlanningCase->$reasonColumn;
                }

                $familyPlanningCase->update($fpUpdateData);
            }

            return response()->json(['message' => 'WRA Masterlist record updated successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['errors' => 'Record does not exist.'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
}
