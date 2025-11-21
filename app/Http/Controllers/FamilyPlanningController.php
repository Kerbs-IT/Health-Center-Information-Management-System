<?php

namespace App\Http\Controllers;

use App\Models\family_planning_case_records;
use App\Models\family_planning_medical_histories;
use App\Models\family_planning_medical_records;
use App\Models\family_planning_obsterical_histories;
use App\Models\family_planning_physical_examinations;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\risk_for_sexually_transmitted_infections;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FamilyPlanningController extends Controller
{
    //
    public function addPatient(Request $request)
    {
        try {
            // validations
            $patientData = $request->validate([
                'type_of_patient' => 'required',
                'first_name' => 'sometimes|nullable|string',
                'last_name' => 'sometimes|nullable|string',
                'middle_initial' => 'sometimes|nullable|string|max:2',
                'date_of_birth' => 'sometimes|nullable|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric|max:100',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'sometimes|nullable|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'street' => 'required',
                'brgy' => 'required',
                'civil_status' => 'sometimes|nullable|string',
            ]);

            $medicalData = $request->validate([
                'religion' => 'sometimes|nullable|string',
                'family_plan_occupation' => 'sometimes|nullable|string',
                'philhealth_no' => [
                    'sometimes',
                    'nullable',
                    'regex:/^\d{2}-\d{9}-\d{1}$/'
                ],
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
            ]);
            $caseData = $request->validate([
                'client_id' => 'sometimes|nullable|string',
                'philhealth_no' => [
                    'sometimes',
                    'nullable',
                    'regex:/^\d{2}-\d{9}-\d{1}$/'
                ],
                'NHTS' => 'sometimes|nullable|string',
                'spouse_lname' => 'sometimes|nullable|string',
                'spouse_fname' => 'sometimes|nullable|string',
                'spouse_MI' => 'sometimes|nullable|string|max:2',
                'spouse_date_of_birth' => 'sometimes|nullable|date',
                'spouse_age' => 'sometimes|nullable|numeric|max:100',
                'spouse_occupation' => 'sometimes|nullable|string',

                'number_of_living_children' => 'sometimes|nullable|numeric|max:50',
                'plan_to_have_more_children' => 'sometimes|nullable|string',
                'average_montly_income' => 'sometimes|nullable|numeric',
                'family_planning_type_of_patient' => 'sometimes|nullable|string',
                'new_acceptor_reason_for_FP' => 'sometimes|nullable|string',
                'current_user_reason_for_FP' => 'sometimes|nullable|string',
                'current_method_reason' => 'sometimes|nullable|string',
                'previously_used_method' => 'sometimes|nullable|array',

                // acknowledgement
                'choosen_method' => 'sometimes|nullable|string',
                'family_planning_signature_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
                'family_planning_date_of_acknowledgement' => 'sometimes|nullable|date',
                'family_planning_acknowlegement_consent_signature_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
                'family_planning_date_of_acknowledgement_consent' => 'sometimes|nullable|date',
                'current_user_type' => 'sometimes|nullable|string'
            ]);

            // medical history
            $medicalHistoryData = $request->validate([
                'medical_history_severe_headaches_migraine' => 'sometimes|nullable|string',
                'medical_history_history_of_stroke' => 'sometimes|nullable|string',
                'medical_history_non_traumatic_hemtoma' => 'sometimes|nullable|string',
                'medical_history_history_of_breast_cancer' => 'sometimes|nullable|string',
                'medical_history_severe_chest_pain' => 'sometimes|nullable|string',
                'medical_history_cough' => 'sometimes|nullable|string',
                'medical_history_jaundice' => 'sometimes|nullable|string',
                'medical_history_unexplained_vaginal_bleeding' => 'sometimes|nullable|string',
                'medical_history_abnormal_vaginal_discharge' => 'sometimes|nullable|string',
                'medical_history_abnormal_phenobarbital' => 'sometimes|nullable|string',
                'medical_history_smoker' => 'sometimes|nullable|string',
                'medical_history_with_dissability' => 'sometimes|nullable|string',
                'if_with_dissability_specification' => 'sometimes|nullable|string',
            ]);

            // Obsterical history
            $obstericalHistoryData = $request->validate([
                'family_planning_G' => 'sometimes|nullable|numeric|max:20',
                'family_planning_P' => 'sometimes|nullable|numeric|max:20',
                'family_planning_full_term' => 'sometimes|nullable|numeric|max:20',
                'family_planning_abortion' => 'sometimes|nullable|numeric|max:20',
                'family_planning_premature' => 'sometimes|nullable|numeric|max:20',
                'family_planning_living_children' => 'sometimes|nullable|numeric|max:20',
                'family_planning_date_of_last_delivery' => 'sometimes|nullable|date',
                'family_planning_type_of_last_delivery' => 'sometimes|nullable|string',
                'family_planning_date_of_last_delivery_menstrual_period' => 'sometimes|nullable|date',
                'family_planning_date_of_previous_delivery_menstrual_period' => 'sometimes|nullable|date',
                'family_planning_type_of_menstrual' => 'sometimes|nullable|string',
                'family_planning_Dysmenorrhea' => 'sometimes|nullable|string',
                'family_planning_hydatidiform_mole' => 'sometimes|nullable|string',
                'family_planning_ectopic_pregnancy' => 'sometimes|nullable|string',
            ]);

            //  RISK FOR SEXUALLY TRANSMITTED INFECTIONS & RISKS FOR VIOLENCE AGAINTS WOMEN (VAW)
            $riskData = $request->validate([
                'infection_abnormal_discharge_from_genital_area' => 'sometimes|nullable|string',
                'origin_of_abnormal_discharge' => 'sometimes|nullable|string',
                'scores_or_ulcer' => 'sometimes|nullable|string',
                'pain_or_burning_sensation' => 'sometimes|nullable|string',
                'history_of_sexually_transmitted_infection' => 'sometimes|nullable|string',
                'sexually_transmitted_disease' => 'sometimes|nullable|string',

                'history_of_domestic_violence_of_VAW' => 'sometimes|nullable|string',
                'unpleasant_relationship_with_partner' => 'sometimes|nullable|string',
                'partner_does_not_approve' => 'sometimes|nullable|string',
                'referred_to' => 'sometimes|nullable|string',
                'reffered_to_others' => 'sometimes|nullable|string',
            ]);

            // physical examination
            $physicalExaminationData = $request->validate([
                'physical_examination_skin_type' => 'sometimes|nullable|string',
                'physical_examination_conjuctiva_type' => 'sometimes|nullable|string',
                'physical_examination_breast_type' => 'sometimes|nullable|string',
                'physical_examination_abdomen_type' => 'sometimes|nullable|string',
                'physical_examination_extremites_type' => 'sometimes|nullable|string',
                'physical_examination_extremites_UID_type' => 'sometimes|nullable|string',
                'cervical_abnormalities_type' => 'sometimes|nullable|string',
                'cervical_consistency_type' => 'sometimes|nullable|string',
                'uterine_position_type' => 'sometimes|nullable|string',
                'uterine_depth_text' => 'sometimes|nullable|numeric',
                'physical_examination_neck_type' => 'sometimes|nullable|string',
            ]);

            // -------------------------------------------------------------------------------------------------------
            // INSERT THE DATA 

            // create the patient record
            $familPlanningPatient = patients::create([
                'user_id' => null,
                'first_name' => $patientData['first_name'],
                'middle_initial' => $patientData['middle_initial'],
                'last_name' => $patientData['last_name'],
                'full_name' => ($patientData['first_name'] . ' ' . $patientData['middle_initial'] . ' ' . $patientData['last_name']),
                'age' => $patientData['age'] ?? null,
                'sex' => $patientData['sex'] ?? null,
                'civil_status' => $patientData['civil_status'] ?? null,
                'contact_number' => $patientData['contact_number'] ?? null,
                'date_of_birth' => $patientData['date_of_birth'] ?? null,
                'profile_image' => 'images/default_profile.png',
                'nationality' => $patientData['nationality'] ?? null,
                'date_of_registration' => $patientData['date_of_registration'] ?? null,
                'place_of_birth' => $patientData['place_of_birth'] ?? null,
            ]);

            $familyPlanningPatientRecordId = $familPlanningPatient->id;

            // add the patient address
            // dd($patient->id);
            $blk_n_street = explode(',', $patientData['street']);
            // dd($blk_n_street);
            $patientAddress = patient_addresses::create([
                'patient_id' => $familyPlanningPatientRecordId,
                'house_number' => $blk_n_street[0] ?? $patientData['blk_n_street'],
                'street' => $blk_n_street[1] ?? null,
                'purok' => $patientData['brgy'],
                'postal_code' => '4109',
                'latitude' => null,
                'longitude' => null,
            ]);

            $patientAddress->refresh(); // <-- this pulls in DB defaults

            $fullAddress = collect([
                $patientAddress->house_number,
                $patientAddress->street,
                $patientAddress->purok,
                $patientAddress->barangay ?? null,
                $patientAddress->city ?? null,
                $patientAddress->province ?? null,
            ])->filter()->join(', ');


            // add record for medical_case table
            $medicalCase = medical_record_cases::create([
                'patient_id' =>  $familyPlanningPatientRecordId,
                'type_of_case' => $patientData['type_of_patient'],
            ]);

            $medicalCaseId = $medicalCase->id; //medical record id


            // CREATE THE MEDICAL RECORD
            family_planning_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id' => $patientData['handled_by'],
                'patient_name' => $familPlanningPatient->full_name,
                'occupation' => $medicalData['family_planning_occupation'] ?? null,
                'religion' => $medicalData['religion'] ?? null,
                'philhealth_no' => $medicalData['philhealth_no'] ?? null,
                'blood_pressure' => $medicalData['blood_pressure'] ?? null,
                'temperature' => $medicalData['temperature'] ?? null,
                'pulse_rate' => $medicalData['pulse_rate'] ?? null,
                'respiratory_rate' => $medicalData['respiratory_rate'] ?? null,
                'height' => $medicalData['height'] ?? null,
                'weight' => $medicalData['weight'] ?? null,
            ]);

            $previoulyMethod = implode(",", $caseData['previously_used_method']);

            // CREATE THE CASE RECORD
            $caseRecord = family_planning_case_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id' => $patientData['handled_by'],
                'client_id' => $caseData['client_id'] ?? null,
                'philhealth_no' => $caseData['philhealth_no'] ?? null,
                'NHTS' => $caseData['NHTS'] ?? null,
                'client_name' => $familPlanningPatient->full_name,
                'client_date_of_birth' => $patientData['date_of_birth'] ?? null,
                'client_age' => $patientData['age'] ?? null,
                'occupation' => $medicalData['family_plan_occupation'] ?? null,
                'client_address' => $fullAddress,
                'client_contact_number' => $patientData['contact_number'] ?? null,
                'client_civil_status' => $patientData['civil_status'] ?? null,
                'client_religion' => $medicalData['religion'] ?? null,
                'spouse_lname' => $caseData['spouse_lname'] ?? null,
                'spouse_fname' => $caseData['spouse_fname'] ?? null,
                'spouse_MI' => $caseData['spouse_MI'] ?? null,
                'spouse_date_of_birth' => $caseData['spouse_date_of_birth'] ?? null,
                'spouse_age' => $caseData['spouse_age'] ?? null,
                'spouse_occupation' => $caseData['spouse_occupation'] ?? null,
                'number_of_living_children' => $caseData['number_of_living_children'] ?? null,
                'plan_to_have_more_children' => $caseData['plan_to_have_more_children'] ?? null,

                'average_montly_income' => $caseData['average_montly_income'] ?? null,
                'type_of_patient' => $caseData['family_planning_type_of_patient'] ?? null,
                'new_acceptor_reason_for_FP' => $caseData['new_acceptor_reason_for_FP'] ?? null,
                'current_user_reason_for_FP' => $caseData['current_user_reason_for_FP'] ?? null,
                'current_method_reason' => $caseData['current_method_reason'] ?? null,
                'previously_used_method' => $previoulyMethod ?? null,
                'choosen_method' => $caseData['choosen_method'] ?? null,
                'signature_image' => $caseData['family_planning_signature_image'] ?? null,
                'date_of_acknowledgement' => $caseData['family_planning_date_of_acknowledgement'] ?? null,
                'acknowledgement_consent_signature_image' => $caseData['family_planning_acknowlegement_consent_signature_image'] ?? null,
                'date_of_acknowledgement_consent' => $caseData['family_planning_date_of_acknowledgement_consent'] ?? null,
                'current_user_type' => $caseData['current_user_type'] ?? null,
            ]);

            $caseId = $caseRecord->id;

            // medical history
            $medicalHistories = family_planning_medical_histories::create([
                'case_id' => $caseId,
                'severe_headaches_migraine' => $medicalHistoryData['medical_history_severe_headaches_migraine'] ?? null,
                'history_of_stroke' => $medicalHistoryData['medical_history_history_of_stroke'] ?? null,
                'non_traumatic_hemtoma' => $medicalHistoryData['medical_history_non_traumatic_hemtoma'] ?? null,
                'history_of_breast_cancer' => $medicalHistoryData['medical_history_history_of_breast_cancer'] ?? null,
                'severe_chest_pain' => $medicalHistoryData['medical_history_severe_chest_pain'] ?? null,
                'cough' => $medicalHistoryData['medical_history_cough'] ?? null,
                'jaundice' => $medicalHistoryData['medical_history_jaundice'] ?? null,
                'unexplained_vaginal_bleeding' => $medicalHistoryData['medical_history_unexplained_vaginal_bleeding'] ?? null,
                'abnormal_vaginal_discharge' => $medicalHistoryData['medical_history_abnormal_vaginal_discharge'] ?? null,
                'abnormal_phenobarbital' => $medicalHistoryData['medical_history_abnormal_phenobarbital'] ?? null,
                'smoker' => $medicalHistoryData['medical_history_smoker'] ?? null,
                'with_dissability' => $medicalHistoryData['medical_history_with_dissability'] ?? null,
                'if_with_dissability_specification' => $medicalHistoryData['if_with_dissability_specification'] ?? null,
            ]);
            // obsterical history
            $obstericalHistories = family_planning_obsterical_histories::create([
                'case_id' => $caseId,
                'G' => $obstericalHistoryData['family_planning_G'] ?? null,
                'P' => $obstericalHistoryData['family_planning_P'] ?? null,
                'full_term' => $obstericalHistoryData['family_planning_full_term'] ?? null,
                'abortion' => $obstericalHistoryData['family_planning_abortion'] ?? null,
                'premature' => $obstericalHistoryData['family_planning_premature'] ?? null,
                'living_children' => $obstericalHistoryData['family_planning_living_chldren'] ?? null,
                'date_of_last_delivery' => $obstericalHistoryData['family_planning_date_of_last_delivery'] ?? null,
                'type_of_last_delivery' => $obstericalHistoryData['family_planning_type_of_last_delivery'] ?? null,
                'date_of_last_delivery_menstrual_period' => $obstericalHistoryData['family_planning_date_of_last_delivery_menstrual_period'] ?? null,
                'date_of_previous_delivery_menstrual_period' => $obstericalHistoryData['family_planning_date_of_previous_delivery_menstrual_period'] ?? null,
                'type_of_menstrual' => $obstericalHistoryData['family_planning_type_of_menstrual'] ?? null,
                'Dysmenorrhea' => $obstericalHistoryData['family_planning_Dysmenorrhea'] ?? null,
                'hydatidiform_mole' => $obstericalHistoryData['family_planning_hydatidiform_mole'] ?? null,
                'ectopic_pregnancy' => $obstericalHistoryData['family_planning_ectopic_pregnancy'] ?? null,
            ]);

            // III. RISK FOR SEXUALLY TRANSMITTED INFECTIONS

            $riskOfSexuallyTransmitted = risk_for_sexually_transmitted_infections::create([
                'case_id' => $caseId,
                'infection_abnormal_discharge_from_genital_area' => $riskData['infection_abnormal_discharge_from_genital_area'] ?? null,
                'origin_of_abnormal_discharge' => $riskData['origin_of_abnormal_discharge'] ?? null,
                'scores_or_ulcer' => $riskData['scores_or_ulcer'] ?? null,
                'pain_or_burning_sensation' => $riskData['pain_or_burning_sensation'] ?? null,
                'history_of_sexually_transmitted_infection' => $riskData['history_of_sexually_transmitted_infection'] ?? null,
                'sexually_transmitted_disease' => $riskData['sexually_transmitted_disease'] ?? null,
                'history_of_domestic_violence_of_VAW' => $riskData['history_of_domestic_violence_of_VAW'] ?? null,
                'unpleasant_relationship_with_partner' => $riskData['unpleasant_relationship_with_partner'] ?? null,
                'partner_does_not_approve' => $riskData['partner_does_not_approve'] ?? null,
                'referred_to' => $riskData['referred_to'] ?? null,
                'reffered_to_others' => $riskData['reffered_to_others'] ?? null,
            ]);

            // PHYSICAL EXAMINATION
            $physicalExamination = family_planning_physical_examinations::create([
                'case_id' => $caseId,
                'blood_pressure' => $medicalData['blood_pressure'] ?? null,
                'pulse_rate' => $medicalData['pulse_rate'] ?? null,
                'height' => $medicalData['height'] ?? null,
                'weight' => $medicalData['height'] ?? null,

                'skin_type' => $physicalExaminationData['physical_examination_skin_type'] ?? null,
                'conjuctiva_type' => $physicalExaminationData['physical_examination_conjuctiva_type'] ?? null,
                'breast_type' => $physicalExaminationData['physical_examination_breast_type'] ?? null,
                'abdomen_type' => $physicalExaminationData['physical_examination_abdomen_type'] ?? null,
                'extremites_type' => $physicalExaminationData['physical_examination_extremites_type'] ?? null,
                'extremites_UID_type' => $physicalExaminationData['physical_examination_extremites_UID_type'] ?? null,
                'cervical_abnormalities_type' => $physicalExaminationData['cervical_abnormalities_type'] ?? null,
                'cervical_consistency_type' => $physicalExaminationData['cervical_consistency_type'] ?? null,
                'uterine_position_type' => $physicalExaminationData['uterine_position_type'] ?? null,
                'uterine_depth_text' => $physicalExaminationData['uterine_depth_text'] ?? null,
                'neck_type' => $physicalExaminationData['physical_examination_neck_type'] ?? null
            ]);


            return response()->json(['message' => 'Family Planning Patient information is added Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function editPatientDetails(Request $request, $id)
    {
        try {

            $familyPlanningRecord = medical_record_cases::with(['patient', 'family_planning_case_record', 'family_planning_medical_record'])->findOrFail($id);
            $address = patient_addresses::where('patient_id', $familyPlanningRecord->patient->id)->firstOrFail();


            $data = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'middle_initial' => 'sometimes|nullable|string|max:2',
                'date_of_birth' => 'sometimes|nullable|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric|max:100',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'sometimes|nullable|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'civil_status' => 'sometimes|nullable|string',
                'occupation' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy' => 'required',
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
                'client_id' =>  'sometimes|nullable|string',
                'philhealth_no' => [
                    'sometimes',
                    'nullable',
                    'regex:/^\d{2}-\d{9}-\d{1}$/'
                ],
                'NHTS' => 'sometimes|nullable|string',

            ]);

            // update the patient data first
            $familyPlanningRecord->patient->update([
                'first_name' => $data['first_name'] ?? $familyPlanningRecord->patient->first_name,
                'middle_initial' => $data['middle_initial'] ?? $familyPlanningRecord->patient->middle_initial,
                'last_name' => $data['last_name'] ?? $familyPlanningRecord->patient->last_name,
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']) ?? $familyPlanningRecord->patient->full_name,
                'age' => $data['age'] ?? $familyPlanningRecord->patient->age,
                'sex' => $data['sex'] ?? $familyPlanningRecord->patient->sex,
                'civil_status' => $data['civil_status'] ?? $familyPlanningRecord->patient->civil_status,
                'contact_number' => $data['contact_number'] ?? $familyPlanningRecord->patient->contact_number,
                'date_of_birth' => $data['date_of_birth'] ?? $familyPlanningRecord->patient->date_of_birth,
                'nationality' => $data['nationality'] ?? $familyPlanningRecord->patient->nationality,
                'date_of_registration' => $data['date_of_registration'] ?? $familyPlanningRecord->patient->date_of_registration,
                'place_of_birth' => $data['place_of_birth'] ?? $familyPlanningRecord->patient->place_of_birth,
            ]);
            // update the address
            $blk_n_street = explode(',', $data['street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street' => $blk_n_street[1] ?? $address->street,
                'purok' => $data['brgy'] ?? $address->purok
            ]);

            $address->refresh(); // <-- this pulls in DB defaults

            $fullAddress = collect([
                $address->house_number,
                $address->street,
                $address->purok,
                $address->barangay ?? null,
                $address->city ?? null,
                $address->province ?? null,
            ])->filter()->join(', ');

            $familyPlanningRecord->patient->refresh();

            // update medical record
            $familyPlanningRecord->family_planning_medical_record->update([
                'health_worker_id' => $data['handled_by'] ?? $familyPlanningRecord->family_planning_medical_record->health_worker_id,
                'patient_name' => $familyPlanningRecord->patient->full_name,
                'occupation' => $data['occupation'] ?? $familyPlanningRecord->family_planning_medical_record->occupation,
                'religion' => $data['religion'] ?? $familyPlanningRecord->family_planning_medical_record->religion,
                'philhealth_no' => $data['philhealth_no'] ?? $familyPlanningRecord->family_planning_medical_record->philhealth_no,
                'blood_pressure' => $data['blood_pressure'] ?? $familyPlanningRecord->family_planning_medical_record->blood_pressure,
                'temperature' => $data['temperature'] ?? $familyPlanningRecord->family_planning_medical_record->temperature,
                'pulse_rate' => $data['pulse_rate'] ?? $familyPlanningRecord->family_planning_medical_record->pulse_rate,
                'respiratory_rate' => $data['respiratory_rate'] ?? $familyPlanningRecord->family_planning_medical_record->respiratory_rate,
                'height' => $data['height'] ?? $familyPlanningRecord->family_planning_medical_record->height,
                'weight' => $data['weight'] ?? $familyPlanningRecord->family_planning_medical_record->weight
            ]);
            // update case record
            $familyPlanningRecord->family_planning_case_record->update([
                'client_name' => $familyPlanningRecord->patient->full_name,
                'client_id' => $data['client_id'] ?? $familyPlanningRecord->family_planning_case_record->client_id,
                'philhealth_no' => $data['philhealth_no'] ?? $familyPlanningRecord->family_planning_case_record->philhealth_no,
                'NHTS' => $data['NHTS'] ?? $familyPlanningRecord->family_planning_case_record->NHTS,
                'client_address' =>  $fullAddress ?? '',
                'client_date_of_birth' => $data['date_of_birth'] ?? $familyPlanningRecord->family_planning_case_record->client_date_of_birth,
                'client_age' => $data['age'] ?? $familyPlanningRecord->family_planning_case_record->client_age,
                'occupation' => $data['occupation'] ?? $familyPlanningRecord->family_planning_case_record->occupation,
                'client_contact_number' => $data['contact_number'] ?? $familyPlanningRecord->family_planning_case_record->client_contact_number,
                'client_civil_status' => $data['civil_status'] ?? $familyPlanningRecord->family_planning_case_record->client_civil_status,
                'client_religion' => $data['religion'] ?? $familyPlanningRecord->family_planning_case_record->client_religion
            ]);

            return response()->json(['message' => 'Updating Patient information Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // catch other runtime errors (like null property, query failure, etc.)
            return response()->json([
                'message' => $e->getMessage() // e.g. "Attempt to read property 'blood_pressure' on null"
            ], 500);
        }
    }

    public function viewCaseInfo($id)
    {
        try {
            $familyPlanCaseInfo = family_planning_case_records::with(['medical_history', 'obsterical_history', 'risk_for_sexually_transmitted_infection', 'physical_examinations'])->findOrFail($id);

            return response()->json(['caseInfo' => $familyPlanCaseInfo], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() // e.g. "Attempt to read property 'blood_pressure' on null"
            ], 500);
        }
    }

    //  this function will be used when the record is deleted

    public function addSideAcaseInfo(Request $request, $id)
    {
        try {
            $familyPlanCaseInfo = family_planning_case_records::where('type_of_record', 'Family Planning Client Assessment Record - Side A')->where('medical_record_case_id', $id)->get();

            if (count($familyPlanCaseInfo) > 0) {
                return response()->json(['message' => "Unable to create the record. A record already exists!"], 422);
            }

            $patientData = $request->validate([
                'side_A_add_client_fname' => 'required|string',
                'side_A_add_client_MI' => 'sometimes|nullable|string|max:2',
                'side_A_add_client_lname' => 'required|string',
                'side_A_add_client_date_of_birth' => 'sometimes|nullable|date',
                'side_A_add_client_age' => 'sometimes|nullable|numeric|max:100',
                'side_A_add_occupation' => 'sometimes|nullable|string',
                'side_A_add_client_civil_status' => 'sometimes|nullable|string',
                'side_A_add_client_religion' => 'sometimes|nullable|string',
            ]);

            $caseData = $request->validate([
                'side_A_add_client_id' => 'sometimes|nullable|string',
                'side_A_add_philhealth_no' => [
                    'sometimes',
                    'nullable',
                    'regex:/^\d{2}-\d{9}-\d{1}$/'
                ],
                'side_A_add_NHTS' => 'sometimes|nullable|string',
                'side_A_add_spouse_lname' => 'sometimes|nullable|string',
                'side_A_add_spouse_fname' => 'sometimes|nullable|string',
                'side_A_add_spouse_MI' => 'sometimes|nullable|string|max:2',
                'side_A_add_spouse_date_of_birth' => 'sometimes|nullable|date',
                'side_A_add_spouse_age' => 'sometimes|nullable|numeric|max:100',
                'side_A_add_spouse_occupation' => 'sometimes|nullable|string',

                'side_A_add_number_of_living_children' => 'sometimes|nullable|numeric|max:50',
                'side_A_add_plan_to_have_more_children' => 'sometimes|nullable|string',
                'side_A_add_average_montly_income' => 'sometimes|nullable|numeric',
                'side_A_add_family_planning_type_of_patient' => 'sometimes|nullable|string',
                'side_A_add_new_acceptor_reason_for_FP' => 'sometimes|nullable|string',
                'side_A_add_current_user_reason_for_FP' => 'sometimes|nullable|string',
                'side_A_add_current_method_reason' => 'sometimes|nullable|string',
                'side_A_add_previously_used_method' => 'sometimes|nullable|array',

                // acknowledgement
                'side_A_add_choosen_method' => 'sometimes|nullable|string',
                'side_A_add_family_planning_signature_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
                'side_A_add_family_planning_date_of_acknowledgement' => 'sometimes|nullable|date',
                'side_A_add_family_planning_acknowlegement_consent_signature_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
                'side_A_add_family_planning_date_of_acknowledgement_consent' => 'sometimes|nullable|date',
                'side_A_add_current_user_type' => 'sometimes|nullable|string',
                'side_A_add_health_worker_id' => 'required'
            ]);

            // medical history
            $medicalHistoryData = $request->validate([
                'side_A_add_severe_headaches_migraine' => 'sometimes|nullable|string',
                'side_A_add_history_of_stroke' => 'sometimes|nullable|string',
                'side_A_add_non_traumatic_hemtoma' => 'sometimes|nullable|string',
                'side_A_add_history_of_breast_cancer' => 'sometimes|nullable|string',
                'side_A_add_severe_chest_pain' => 'sometimes|nullable|string',
                'side_A_add_cough' => 'sometimes|nullable|string',
                'side_A_add_jaundice' => 'sometimes|nullable|string',
                'side_A_add_unexplained_vaginal_bleeding' => 'sometimes|nullable|string',
                'side_A_add_abnormal_vaginal_discharge' => 'sometimes|nullable|string',
                'side_A_add_abnormal_phenobarbital' => 'sometimes|nullable|string',
                'side_A_add_smoker' => 'sometimes|nullable|string',
                'side_A_add_with_dissability' => 'sometimes|nullable|string',
                'side_A_add_if_with_dissability_specification' => 'sometimes|nullable|string',
            ]);

            // Obsterical history
            $obstericalHistoryData = $request->validate([
                'side_A_add_G' => 'sometimes|nullable|numeric|max:20',
                'side_A_add_P' => 'sometimes|nullable|numeric|max:20',
                'side_A_add_full_term' => 'sometimes|nullable|numeric|max:20',
                'side_A_add_abortion' => 'sometimes|nullable|numeric|max:20',
                'side_A_add_premature' => 'sometimes|nullable|numeric|max:20',
                'side_A_add_living_children' => 'sometimes|nullable|numeric|max:20',
                'side_A_add_date_of_last_delivery' => 'sometimes|nullable|date',
                'side_A_add_type_of_last_delivery' => 'sometimes|nullable|string',
                'side_A_add_date_of_last_delivery_menstrual_period' => 'sometimes|nullable|date',
                'side_A_add_date_of_previous_delivery_menstrual_period' => 'sometimes|nullable|date',
                'side_A_add_type_of_menstrual' => 'sometimes|nullable|string',
                'side_A_add_Dysmenorrhea' => 'sometimes|nullable|string',
                'side_A_add_hydatidiform_mole' => 'sometimes|nullable|string',
                'side_A_add_ectopic_pregnancy' => 'sometimes|nullable|string',
            ]);

            //  RISK FOR SEXUALLY TRANSMITTED INFECTIONS & RISKS FOR VIOLENCE AGAINTS WOMEN (VAW)
            $riskData = $request->validate([
                'side_A_add_infection_abnormal_discharge_from_genital_area' => 'sometimes|nullable|string',
                'side_A_add_origin_of_abnormal_discharge' => 'sometimes|nullable|string',
                'side_A_add_scores_or_ulcer' => 'sometimes|nullable|string',
                'side_A_add_pain_or_burning_sensation' => 'sometimes|nullable|string',
                'side_A_add_history_of_sexually_transmitted_infection' => 'sometimes|nullable|string',
                'side_A_add_sexually_transmitted_disease' => 'sometimes|nullable|string',

                'side_A_add_history_of_domestic_violence_of_VAW' => 'sometimes|nullable|string',
                'side_A_add_unpleasant_relationship_with_partner' => 'sometimes|nullable|string',
                'side_A_add_partner_does_not_approve' => 'sometimes|nullable|string',
                'side_A_add_referred_to' => 'sometimes|nullable|string',
                'side_A_add_reffered_to_others' => 'sometimes|nullable|string',
            ]);

            // physical examination
            $physicalExaminationData = $request->validate([
                'side_A_add_blood_pressure' => 'sometimes|nullable|numeric',
                'side_A_add_pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'side_A_add_height'            => 'nullable|numeric|between:30,300', // cm range
                'side_A_add_weight'            => 'nullable|numeric|between:1,500',  // kg range
                'side_A_add_skin_type' => 'sometimes|nullable|string',
                'side_A_add_conjuctiva_type' => 'sometimes|nullable|string',
                'side_A_add_breast_type' => 'sometimes|nullable|string',
                'side_A_add_abdomen_type' => 'sometimes|nullable|string',
                'side_A_add_extremites_type' => 'sometimes|nullable|string',
                'side_A_add_extremites_UID_type' => 'sometimes|nullable|string',
                'side_A_add_cervical_abnormalities_type' => 'sometimes|nullable|string',
                'side_A_add_cervical_consistency_type' => 'sometimes|nullable|string',
                'side_A_add_uterine_position_type' => 'sometimes|nullable|string',
                'side_A_add_uterine_depth_text' => 'sometimes|nullable|numeric',
                'side_A_add_neck_type' => 'sometimes|nullable|string',
            ]);

            // update patient info first
            $previoulyMethod = implode(",", $caseData['side_A_add_previously_used_method']);

            // update the case
            $familyPlanningCaseRecord = family_planning_case_records::create([
                'medical_record_case_id' => $id,
                'health_worker_id' => $caseData['side_A_add_health_worker_id'],
                'client_id' => $caseData['side_A_add_client_id'] ?? null,
                'philhealth_no' => $caseData['side_A_add_philhealth_no'] ?? null,
                'NHTS' => $caseData['side_A_add_NHTS'] ?? null,
                'client_name' => trim(($patientData['side_A_add_client_fname'] . ' ' . $patientData['side_A_add_client_MI'] . ' ' . $patientData['side_A_add_client_lname'])) ?? null,
                'client_date_of_birth' => $patientData['side_A_add_client_date_of_birth'] ?? null,
                'client_age' => $patientData['side_A_add_client_age'] ?? null,
                'occupation' => $patientData['side_A_add_occupation'] ?? null,
                'client_contact_number' => $patientData['side_A_add_client_contact_number'] ?? null,
                'client_civil_status' => $patientData['side_A_add_client_civil_status'] ?? null,
                'client_religion' => $patientData['side_A_add_client_religion'] ?? null,
                'spouse_lname' => $caseData['side_A_add_spouse_lname'] ?? null,
                'spouse_fname' => $caseData['side_A_add_spouse_fname'] ?? null,
                'spouse_MI' => $caseData['side_A_add_spouse_MI'] ?? null,
                'spouse_date_of_birth' => $caseData['side_A_add_spouse_date_of_birth'] ?? null,
                'spouse_age' => $caseData['side_A_add_spouse_age'] ?? null,
                'spouse_occupation' => $caseData['side_A_add_spouse_occupation'] ?? null,
                'number_of_living_children' => $caseData['side_A_add_number_of_living_children'] ?? null,
                'plan_to_have_more_children' => $caseData['side_A_add_plan_to_have_more_children'] ?? null,

                'average_montly_income' => $caseData['side_A_add_average_montly_income'] ?? null,
                'type_of_patient' => $caseData['side_A_add_family_planning_type_of_patient'] ?? null,
                'new_acceptor_reason_for_FP' => $caseData['side_A_add_new_acceptor_reason_for_FP'] ?? null,
                'current_user_reason_for_FP' => $caseData['side_A_add_current_user_reason_for_FP'] ?? null,
                'current_method_reason' => $caseData['side_A_add_current_method_reason'] ?? null,
                'previously_used_method' => $previoulyMethod ?? null,
                'choosen_method' => $caseData['side_A_add_choosen_method'] ?? null,
                'signature_image' => $caseData['side_A_add_family_planning_signature_image'] ?? null,
                'date_of_acknowledgement' => $caseData['side_A_add_family_planning_date_of_acknowledgement'] ?? null,
                'acknowledgement_consent_signature_image' => $caseData['side_A_add_family_planning_acknowlegement_consent_signature_image'] ?? null,
                'date_of_acknowledgement_consent' => $caseData['side_A_add_family_planning_date_of_acknowledgement_consent'] ?? null,
                'current_user_type' => $caseData['side_A_add_current_user_type'] ?? null,
                'status' => 'Done'
            ]);

            family_planning_medical_histories::create([
                'case_id' => $familyPlanningCaseRecord->id,
                'severe_headaches_migraine' => $medicalHistoryData['side_A_add_severe_headaches_migraine'] ?? null,
                'history_of_stroke' => $medicalHistoryData['side_A_add_history_of_stroke'] ?? null,
                'non_traumatic_hemtoma' => $medicalHistoryData['side_A_add_non_traumatic_hemtoma'] ?? null,
                'history_of_breast_cancer' => $medicalHistoryData['side_A_add_history_of_breast_cancer'] ?? null,
                'severe_chest_pain' => $medicalHistoryData['side_A_add_severe_chest_pain'] ?? null,
                'cough' => $medicalHistoryData['side_A_add_cough'] ?? null,
                'jaundice' => $medicalHistoryData['side_A_add_jaundice'] ?? null,
                'unexplained_vaginal_bleeding' => $medicalHistoryData['side_A_add_unexplained_vaginal_bleeding'] ?? null,
                'abnormal_vaginal_discharge' => $medicalHistoryData['side_A_add_abnormal_vaginal_discharge'] ?? null,
                'abnormal_phenobarbital' => $medicalHistoryData['side_A_add_abnormal_phenobarbital'] ?? null,
                'smoker' => $medicalHistoryData['side_A_add_smoker'] ?? null,
                'with_dissability' => $medicalHistoryData['side_A_add_with_dissability'] ?? null,
                'if_with_dissability_specification' => $medicalHistoryData['side_A_add_if_with_dissability_specification'] ?? null,
            ]);

            family_planning_obsterical_histories::create([
                'case_id' => $familyPlanningCaseRecord->id,
                'G' => $obstericalHistoryData['side_A_add_G'] ?? null,
                'P' => $obstericalHistoryData['side_A_add_P'] ?? null,
                'full_term' => $obstericalHistoryData['side_A_add_full_term'] ?? null,
                'abortion' => $obstericalHistoryData['side_A_add_abortion'] ?? null,
                'premature' => $obstericalHistoryData['side_A_add_premature'] ?? null,
                'living_children' => $obstericalHistoryData['side_A_add_living_children'] ?? null,
                'date_of_last_delivery' => $obstericalHistoryData['side_A_add_date_of_last_delivery'] ?? null,
                'type_of_last_delivery' => $obstericalHistoryData['side_A_add_type_of_last_delivery'] ?? null,
                'date_of_last_delivery_menstrual_period' => $obstericalHistoryData['side_A_add_date_of_last_delivery_menstrual_period'] ?? null,
                'date_of_previous_delivery_menstrual_period' => $obstericalHistoryData['side_A_add_date_of_previous_delivery_menstrual_period '] ?? null,
                'type_of_menstrual' => $obstericalHistoryData['side_A_add_type_of_menstrual'] ?? null,
                'Dysmenorrhea' => $obstericalHistoryData['side_A_add_Dysmenorrhea'] ?? null,
                'hydatidiform_mole' => $obstericalHistoryData['side_A_add_hydatidiform_mole'] ?? null,
                'ectopic_pregnancy' => $obstericalHistoryData['side_A_add_ectopic_pregnancy'] ?? null,
            ]);

            // risk for sexuall transmitted update
            risk_for_sexually_transmitted_infections::create([
                'case_id' => $familyPlanningCaseRecord->id,
                'infection_abnormal_discharge_from_genital_area' => $riskData['side_A_add_infection_abnormal_discharge_from_genital_area'] ??  null,
                'origin_of_abnormal_discharge' => $riskData['side_A_add_origin_of_abnormal_discharge'] ??  null,
                'scores_or_ulcer' => $riskData['side_A_add_scores_or_ulcer'] ??  null,
                'pain_or_burning_sensation' => $riskData['side_A_add_pain_or_burning_sensation'] ??  null,
                'history_of_sexually_transmitted_infection' => $riskData['side_A_add_history_of_sexually_transmitted_infection'] ??  null,
                'sexually_transmitted_disease' => $riskData['side_A_add_sexually_transmitted_disease'] ??  null,
                'history_of_domestic_violence_of_VAW' => $riskData['side_A_add_history_of_domestic_violence_of_VAW'] ??  null,
                'unpleasant_relationship_with_partner' => $riskData['side_A_add_unpleasant_relationship_with_partner'] ??  null,
                'partner_does_not_approve' => $riskData['side_A_add_partner_does_not_approve'] ??  null,
                'referred_to' => $riskData['side_A_add_referred_to'] ??  null,
                'reffered_to_others' => $riskData['side_A_add_reffered_to_others'] ??  null,
            ]);

            // update physical examination


            family_planning_physical_examinations::create([
                'case_id' => $familyPlanningCaseRecord->id,
                'blood_pressure' => $medicalData['side_A_add_blood_pressure'] ?? null,
                'pulse_rate' => $medicalData['side_A_add_pulse_rate'] ?? null,
                'height' => $medicalData['side_A_add_height'] ?? null,
                'weight' => $medicalData['side_A_add_height'] ?? null,

                'skin_type' => $physicalExaminationData['side_A_add_skin_type'] ?? null,
                'conjuctiva_type' => $physicalExaminationData['side_A_add_conjuctiva_type'] ?? null,
                'breast_type' => $physicalExaminationData['side_A_add_breast_type'] ?? null,
                'abdomen_type' => $physicalExaminationData['side_A_add_abdomen_type'] ?? null,
                'extremites_type' => $physicalExaminationData['side_A_add_extremites_type'] ?? null,
                'extremites_UID_type' => $physicalExaminationData['side_A_add_extremites_UID_type'] ?? null,
                'cervical_abnormalities_type' => $physicalExaminationData['side_A_add_cervical_abnormalities_type'] ?? null,
                'cervical_consistency_type' => $physicalExaminationData['side_A_add_cervical_consistency_type'] ?? null,
                'uterine_position_type' => $physicalExaminationData['side_A_add_uterine_position_type'] ?? null,
                'uterine_depth_text' => $physicalExaminationData['side_A_add_uterine_depth_text'] ?? null,
                'neck_type' => $physicalExaminationData['side_A_add_neck_type'] ?? null
            ]);

            return response()->json(['message' => 'Family Planning Patient Case information is updated Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    // ------------------------------------------------------------------------------------------------------------------


    public function updateCaseInfo(Request $request, $id)
    {
        try {
            $familyPlanCaseInfo = family_planning_case_records::with(['medical_history', 'obsterical_history', 'risk_for_sexually_transmitted_infection', 'physical_examinations'])->findOrFail($id);
            // get the medical
            $medical_case_record = medical_record_cases::with(['patient', 'family_planning_medical_record'])->findOrFail($familyPlanCaseInfo->medical_record_case_id);

            $patientData = $request->validate(
                [
                    'edit_client_fname' => 'required|string',
                    'edit_client_MI' => 'required|string|max:2',
                    'edit_client_lname' => 'required|string',
                    'edit_client_date_of_birth' => 'sometimes|nullable|date',
                    'edit_client_age' => 'sometimes|nullable|numeric|max:100',
                    'edit_occupation' => 'sometimes|nullable|string',
                    'edit_client_civil_status' => 'sometimes|nullable|string',
                    'edit_client_religion' => 'sometimes|nullable|string',
                ],
                [],
                [ // Custom attribute names
                    'edit_client_fname' => 'first name',
                    'edit_client_MI' => 'middle initial',
                    'edit_client_lname' => 'last name',
                    'edit_client_date_of_birth' => 'date of birth',
                    'edit_client_age' => 'age',
                    'edit_occupation' => 'occupation',
                    'edit_client_civil_status' => 'civil status',
                    'edit_client_religion' => 'religion',
                ]
            );

            $caseData = $request->validate(
                [
                    'edit_client_id' => 'sometimes|nullable|string',
                    'edit_philhealth_no' => [
                        'sometimes',
                        'nullable',
                        'regex:/^\d{2}-\d{9}-\d{1}$/'
                    ],
                    'edit_NHTS' => 'sometimes|nullable|string',
                    'edit_spouse_lname' => 'sometimes|nullable|string',
                    'edit_spouse_fname' => 'sometimes|nullable|string',
                    'edit_spouse_MI' => 'sometimes|nullable|string|max:2',
                    'edit_spouse_date_of_birth' => 'sometimes|nullable|date',
                    'edit_spouse_age' => 'sometimes|nullable|numeric|max:100',
                    'edit_spouse_occupation' => 'sometimes|nullable|string',

                    'edit_number_of_living_children' => 'sometimes|nullable|numeric|max:50',
                    'edit_plan_to_have_more_children' => 'sometimes|nullable|string',
                    'edit_average_montly_income' => 'sometimes|nullable|numeric',
                    'edit_family_planning_type_of_patient' => 'sometimes|nullable|string',
                    'edit_new_acceptor_reason_for_FP' => 'sometimes|nullable|string',
                    'edit_current_user_reason_for_FP' => 'sometimes|nullable|string',
                    'edit_current_method_reason' => 'sometimes|nullable|string',
                    'edit_previously_used_method' => 'sometimes|nullable|array',

                    // acknowledgement
                    'edit_choosen_method' => 'sometimes|nullable|string',
                    'edit_family_planning_signature_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
                    'edit_family_planning_date_of_acknowledgement' => 'sometimes|nullable|date',
                    'edit_family_planning_acknowlegement_consent_signature_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
                    'edit_family_planning_date_of_acknowledgement_consent' => 'sometimes|nullable|date',
                    'edit_current_user_type' => 'sometimes|nullable|string'
                ],
                [],
                [ // ✅ Custom attribute names is for removing the edit_
                    'edit_client_id' => 'client ID',
                    'edit_philhealth_no' => 'PhilHealth number',
                    'edit_NHTS' => 'NHTS status',
                    'edit_spouse_lname' => 'spouse last name',
                    'edit_spouse_fname' => 'spouse first name',
                    'edit_spouse_MI' => 'spouse middle initial',
                    'edit_spouse_date_of_birth' => 'spouse date of birth',
                    'edit_spouse_age' => 'spouse age',
                    'edit_spouse_occupation' => 'spouse occupation',

                    'edit_number_of_living_children' => 'number of living children',
                    'edit_plan_to_have_more_children' => 'plan to have more children',
                    'edit_average_montly_income' => 'average monthly income',
                    'edit_family_planning_type_of_patient' => 'type of family planning patient',
                    'edit_new_acceptor_reason_for_FP' => 'reason for new acceptor of family planning',
                    'edit_current_user_reason_for_FP' => 'reason for current user of family planning',
                    'edit_current_method_reason' => 'reason for current method',
                    'edit_previously_used_method' => 'previously used method',

                    'edit_choosen_method' => 'chosen method',
                    'edit_family_planning_signature_image' => 'client signature',
                    'edit_family_planning_date_of_acknowledgement' => 'date of acknowledgement',
                    'edit_family_planning_acknowlegement_consent_signature_image' => 'consent signature',
                    'edit_family_planning_date_of_acknowledgement_consent' => 'date of acknowledgement consent',
                    'edit_current_user_type' => 'current user type',
                ]
            );

            // medical history
            $medicalHistoryData = $request->validate([
                'edit_severe_headaches_migraine' => 'sometimes|nullable|string',
                'edit_history_of_stroke' => 'sometimes|nullable|string',
                'edit_non_traumatic_hemtoma' => 'sometimes|nullable|string',
                'edit_history_of_breast_cancer' => 'sometimes|nullable|string',
                'edit_severe_chest_pain' => 'sometimes|nullable|string',
                'edit_cough' => 'sometimes|nullable|string',
                'edit_jaundice' => 'sometimes|nullable|string',
                'edit_unexplained_vaginal_bleeding' => 'sometimes|nullable|string',
                'edit_abnormal_vaginal_discharge' => 'sometimes|nullable|string',
                'edit_abnormal_phenobarbital' => 'sometimes|nullable|string',
                'edit_smoker' => 'sometimes|nullable|string',
                'edit_with_dissability' => 'sometimes|nullable|string',
                'edit_if_with_dissability_specification' => 'sometimes|nullable|string',
            ]);

            // Obsterical history
            $obstericalHistoryData = $request->validate([
                'edit_G' => 'sometimes|nullable|numeric|max:20',
                'edit_P' => 'sometimes|nullable|numeric|max:20',
                'edit_full_term' => 'sometimes|nullable|numeric|max:20',
                'edit_abortion' => 'sometimes|nullable|numeric|max:20',
                'edit_premature' => 'sometimes|nullable|numeric|max:20',
                'edit_living_children' => 'sometimes|nullable|numeric|max:20',
                'edit_date_of_last_delivery' => 'sometimes|nullable|date',
                'edit_type_of_last_delivery' => 'sometimes|nullable|string',
                'edit_date_of_last_delivery_menstrual_period' => 'sometimes|nullable|date',
                'edit_date_of_previous_delivery_menstrual_period' => 'sometimes|nullable|date',
                'edit_type_of_menstrual' => 'sometimes|nullable|string',
                'edit_Dysmenorrhea' => 'sometimes|nullable|string',
                'edit_hydatidiform_mole' => 'sometimes|nullable|string',
                'edit_ectopic_pregnancy' => 'sometimes|nullable|string',
            ]);

            //  RISK FOR SEXUALLY TRANSMITTED INFECTIONS & RISKS FOR VIOLENCE AGAINTS WOMEN (VAW)
            $riskData = $request->validate([
                'edit_infection_abnormal_discharge_from_genital_area' => 'sometimes|nullable|string',
                'edit_origin_of_abnormal_discharge' => 'sometimes|nullable|string',
                'edit_scores_or_ulcer' => 'sometimes|nullable|string',
                'edit_pain_or_burning_sensation' => 'sometimes|nullable|string',
                'edit_history_of_sexually_transmitted_infection' => 'sometimes|nullable|string',
                'edit_sexually_transmitted_disease' => 'sometimes|nullable|string',

                'edit_history_of_domestic_violence_of_VAW' => 'sometimes|nullable|string',
                'edit_unpleasant_relationship_with_partner' => 'sometimes|nullable|string',
                'edit_partner_does_not_approve' => 'sometimes|nullable|string',
                'edit_referred_to' => 'sometimes|nullable|string',
                'edit_reffered_to_others' => 'sometimes|nullable|string',
            ]);

            // physical examination
            $physicalExaminationData = $request->validate([
                'edit_blood_pressure' => 'sometimes|nullable|numeric',
                'edit_pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'edit_height'            => 'nullable|numeric|between:30,300', // cm range
                'edit_weight'            => 'nullable|numeric|between:1,500',  // kg range
                'edit_skin_type' => 'sometimes|nullable|string',
                'edit_conjuctiva_type' => 'sometimes|nullable|string',
                'edit_breast_type' => 'sometimes|nullable|string',
                'edit_abdomen_type' => 'sometimes|nullable|string',
                'edit_extremites_type' => 'sometimes|nullable|string',
                'edit_extremites_UID_type' => 'sometimes|nullable|string',
                'edit_cervical_abnormalities_type' => 'sometimes|nullable|string',
                'edit_cervical_consistency_type' => 'sometimes|nullable|string',
                'edit_uterine_position_type' => 'sometimes|nullable|string',
                'edit_uterine_depth_text' => 'sometimes|nullable|numeric',
                'edit_neck_type' => 'sometimes|nullable|string',
            ]);

            // update patient info first
            $medical_case_record->patient->update([
                'first_name' => $patientData['edit_client_fname'] ?? $medical_case_record->patient->first_name,
                'middle_initial' => $patientData['edit_client_MI'] ?? $medical_case_record->patient->middle_initial,
                'last_name' => $patientData['edit_client_lname'] ?? $medical_case_record->patient->last_name,
                'full_name' => trim(($patientData['edit_client_fname'] . ' ' . $patientData['edit_client_MI'] . ' ' . $patientData['edit_client_lname'])) ?? $medical_case_record->patient->full_name,
                'age' => $patientData['edit_client_age'] ?? $medical_case_record->patient->age,
                'contact_number' => $patientData['edit_client_contact_number'] ?? $medical_case_record->patient->age,
                'date_of_birth' => $patientData['edit_client_date_of_birth'] ?? $medical_case_record->patient->age,
                'civil_status' => $patientData['edit_client_civil_status'] ?? $medical_case_record->patient->civil_status
            ]);

            $medical_case_record->family_planning_medical_record->update([
                'patient_name' => trim(($patientData['edit_client_fname'] . ' ' . $patientData['edit_client_MI'] . ' ' . $patientData['edit_client_lname'])) ?? $medical_case_record->patient->full_name,
                'occupation' => $patientData['edit_occupation'] ?? $medical_case_record->family_planning_medical_record->occupation,
                'blood_pressure' => $physicalExaminationData['edit_blood_pressure'] ?? $medical_case_record->family_planning_medical_record->blood_pressure,
                'pulse_rate' => $physicalExaminationData['edit_pulse_rate'] ?? $medical_case_record->family_planning_medical_record->pulse_rate,
                'height' => $physicalExaminationData['edit_height'] ?? $medical_case_record->family_planning_medical_record->height,
                'weight' => $physicalExaminationData['edit_weight'] ?? $medical_case_record->family_planning_medical_record->weight,
                'religion' => $patientData['edit_client_religion'] ?? $medical_case_record->family_planning_medical_record->religion
            ]);

            // refresh
            $medical_case_record->patient->refresh();

            $previoulyMethod = implode(",", $caseData['edit_previously_used_method']);

            // update the case
            $familyPlanCaseInfo->update([
                'client_id' => $caseData['edit_client_id'] ?? $familyPlanCaseInfo->client_id,
                'philhealth_no' => $caseData['edit_philhealth_no'] ?? $familyPlanCaseInfo->philhealth_no,
                'NHTS' => $caseData['edit_NHTS'] ?? $familyPlanCaseInfo->NHTS,
                'client_name' => $medical_case_record->patient->full_name,
                'client_date_of_birth' => $patientData['edit_client_date_of_birth'] ?? $familyPlanCaseInfo->client_date_of_birth,
                'client_age' => $patientData['edit_client_age'] ?? $familyPlanCaseInfo->client_age,
                'occupation' => $patientData['edit_occupation'] ?? $familyPlanCaseInfo->occupation,
                'client_contact_number' => $patientData['edit_client_contact_number'] ?? $familyPlanCaseInfo->client_contact_number,
                'client_civil_status' => $patientData['edit_client_civil_status'] ?? $familyPlanCaseInfo->client_civil_status,
                'client_religion' => $patientData['edit_client_religion'] ?? $familyPlanCaseInfo->client_religion,
                'spouse_lname' => $caseData['edit_spouse_lname'] ?? $familyPlanCaseInfo->spouse_lname,
                'spouse_fname' => $caseData['edit_spouse_fname'] ?? $familyPlanCaseInfo->spouse_fname,
                'spouse_MI' => $caseData['edit_spouse_MI'] ?? $familyPlanCaseInfo->spouse_MI,
                'spouse_date_of_birth' => $caseData['edit_spouse_date_of_birth'] ?? $familyPlanCaseInfo->spouse_date_of_birth,
                'spouse_age' => $caseData['edit_spouse_age'] ?? $familyPlanCaseInfo->spouse_age,
                'spouse_occupation' => $caseData['edit_spouse_occupation'] ?? $familyPlanCaseInfo->spouse_occupation,
                'number_of_living_children' => $caseData['edit_number_of_living_children'] ?? $familyPlanCaseInfo->number_of_living_children,
                'plan_to_have_more_children' => $caseData['edit_plan_to_have_more_children'] ?? $familyPlanCaseInfo->plan_to_have_more_children,

                'average_montly_income' => $caseData['edit_average_montly_income'] ?? $familyPlanCaseInfo->average_montly_income,
                'type_of_patient' => $caseData['edit_family_planning_type_of_patient'] ?? $familyPlanCaseInfo->type_of_patient,
                'new_acceptor_reason_for_FP' => $caseData['edit_new_acceptor_reason_for_FP'] ?? $familyPlanCaseInfo->new_acceptor_reason_for_FP,
                'current_user_reason_for_FP' => $caseData['edit_current_user_reason_for_FP'] ?? $familyPlanCaseInfo->current_user_reason_for_FP,
                'current_method_reason' => $caseData['edit_current_method_reason'] ?? $familyPlanCaseInfo->current_method_reason,
                'previously_used_method' => $previoulyMethod ?? $familyPlanCaseInfo->previously_used_method,
                'choosen_method' => $caseData['edit_choosen_method'] ?? $familyPlanCaseInfo->choosen_method,
                'signature_image' => $caseData['edit_family_planning_signature_image'] ?? $familyPlanCaseInfo->signature_image,
                'date_of_acknowledgement' => $caseData['edit_family_planning_date_of_acknowledgement'] ?? $familyPlanCaseInfo->date_of_acknowledgement,
                'acknowledgement_consent_signature_image' => $caseData['edit_family_planning_acknowlegement_consent_signature_image'] ?? $familyPlanCaseInfo->acknowledgement_consent_signature_image,
                'date_of_acknowledgement_consent' => $caseData['edit_family_planning_date_of_acknowledgement_consent'] ?? $familyPlanCaseInfo->date_of_acknowledgement_consent,
                'current_user_type' => $caseData['edit_current_user_type'] ?? $familyPlanCaseInfo->current_user_type,
                'status' => 'Done'
            ]);

            $familyPlanCaseInfo->medical_history->update([
                'severe_headaches_migraine' => $medicalHistoryData['edit_severe_headaches_migraine'] ?? $familyPlanCaseInfo->medical_history->severe_headaches_migraine,
                'history_of_stroke' => $medicalHistoryData['edit_history_of_stroke'] ?? $familyPlanCaseInfo->medical_history->history_of_stroke,
                'non_traumatic_hemtoma' => $medicalHistoryData['edit_non_traumatic_hemtoma'] ?? $familyPlanCaseInfo->medical_history->non_traumatic_hemtoma,
                'history_of_breast_cancer' => $medicalHistoryData['edit_history_of_breast_cancer'] ?? $familyPlanCaseInfo->medical_history->history_of_breast_cancer,
                'severe_chest_pain' => $medicalHistoryData['edit_severe_chest_pain'] ?? $familyPlanCaseInfo->medical_history->severe_chest_pain,
                'cough' => $medicalHistoryData['edit_cough'] ?? $familyPlanCaseInfo->medical_history->cough,
                'jaundice' => $medicalHistoryData['edit_jaundice'] ?? $familyPlanCaseInfo->medical_history->jaundice,
                'unexplained_vaginal_bleeding' => $medicalHistoryData['edit_unexplained_vaginal_bleeding'] ?? $familyPlanCaseInfo->medical_history->unexplained_vaginal_bleeding,
                'abnormal_vaginal_discharge' => $medicalHistoryData['edit_abnormal_vaginal_discharge'] ?? $familyPlanCaseInfo->medical_history->abnormal_vaginal_discharge,
                'abnormal_phenobarbital' => $medicalHistoryData['edit_abnormal_phenobarbital'] ?? $familyPlanCaseInfo->medical_history->abnormal_phenobarbital,
                'smoker' => $medicalHistoryData['edit_smoker'] ?? $familyPlanCaseInfo->medical_history->smoker,
                'with_dissability' => $medicalHistoryData['edit_with_dissability'] ?? $familyPlanCaseInfo->medical_history->with_dissability,
                'if_with_dissability_specification' => $medicalHistoryData['edit_if_with_dissability_specification'] ?? $familyPlanCaseInfo->medical_history->if_with_dissability_specification,
            ]);

            $familyPlanCaseInfo->obsterical_history->update([
                'G' => $obstericalHistoryData['edit_G'] ?? $familyPlanCaseInfo->obsterical_history->G,
                'P' => $obstericalHistoryData['edit_P'] ?? $familyPlanCaseInfo->obsterical_history->P,
                'full_term' => $obstericalHistoryData['edit_full_term'] ?? $familyPlanCaseInfo->obsterical_history->full_term,
                'abortion' => $obstericalHistoryData['edit_abortion'] ?? $familyPlanCaseInfo->obsterical_history->abortion,
                'premature' => $obstericalHistoryData['edit_premature'] ?? $familyPlanCaseInfo->obsterical_history->premature,
                'living_children' => $obstericalHistoryData['edit_living_children'] ?? $familyPlanCaseInfo->obsterical_history->living_children,
                'date_of_last_delivery' => $obstericalHistoryData['edit_date_of_last_delivery'] ?? $familyPlanCaseInfo->obsterical_history->date_of_last_delivery,
                'type_of_last_delivery' => $obstericalHistoryData['edit_type_of_last_delivery'] ?? $familyPlanCaseInfo->obsterical_history->type_of_last_delivery,
                'date_of_last_delivery_menstrual_period' => $obstericalHistoryData['edit_date_of_last_delivery_menstrual_period'] ?? $familyPlanCaseInfo->obsterical_history->date_of_last_delivery_menstrual_period,
                'date_of_previous_delivery_menstrual_period' => $obstericalHistoryData['edit_date_of_previous_delivery_menstrual_period '] ?? $familyPlanCaseInfo->obsterical_history->date_of_previous_delivery_menstrual_period,
                'type_of_menstrual' => $obstericalHistoryData['edit_type_of_menstrual'] ?? $familyPlanCaseInfo->obsterical_history->type_of_menstrual,
                'Dysmenorrhea' => $obstericalHistoryData['edit_Dysmenorrhea'] ?? $familyPlanCaseInfo->obsterical_history->Dysmenorrhea,
                'hydatidiform_mole' => $obstericalHistoryData['edit_hydatidiform_mole'] ?? $familyPlanCaseInfo->obsterical_history->hydatidiform_mole,
                'ectopic_pregnancy' => $obstericalHistoryData['edit_ectopic_pregnancy'] ?? $familyPlanCaseInfo->obsterical_history->ectopic_pregnancy,
            ]);

            // risk for sexuall transmitted update
            $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->update([
                'infection_abnormal_discharge_from_genital_area' => $riskData['edit_infection_abnormal_discharge_from_genital_area'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->infection_abnormal_discharge_from_genital_area,
                'origin_of_abnormal_discharge' => $riskData['edit_origin_of_abnormal_discharge'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->origin_of_abnormal_discharge,
                'scores_or_ulcer' => $riskData['edit_scores_or_ulcer'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->scores_or_ulcer,
                'pain_or_burning_sensation' => $riskData['edit_pain_or_burning_sensation'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->pain_or_burning_sensation,
                'history_of_sexually_transmitted_infection' => $riskData['edit_history_of_sexually_transmitted_infection'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->history_of_sexually_transmitted_infection,
                'sexually_transmitted_disease' => $riskData['edit_sexually_transmitted_disease'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->sexually_transmitted_disease,
                'history_of_domestic_violence_of_VAW' => $riskData['edit_history_of_domestic_violence_of_VAW'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->history_of_domestic_violence_of_VAW,
                'unpleasant_relationship_with_partner' => $riskData['edit_unpleasant_relationship_with_partner'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->unpleasant_relationship_with_partner,
                'partner_does_not_approve' => $riskData['edit_partner_does_not_approve'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->partner_does_not_approve,
                'referred_to' => $riskData['edit_referred_to'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->referred_to,
                'reffered_to_others' => $riskData['edit_reffered_to_others'] ??  $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->reffered_to_others,
            ]);

            // update physical examination


            $familyPlanCaseInfo->physical_examinations->update([
                'blood_pressure' => $medicalData['edit_blood_pressure'] ?? $familyPlanCaseInfo->physical_examinations->blood_pressure,
                'pulse_rate' => $medicalData['edit_pulse_rate'] ?? $familyPlanCaseInfo->physical_examinations->pulse_rate,
                'height' => $medicalData['edit_height'] ?? $familyPlanCaseInfo->physical_examinations->height,
                'weight' => $medicalData['edit_height'] ?? $familyPlanCaseInfo->physical_examinations->weight,

                'skin_type' => $physicalExaminationData['edit_skin_type'] ?? $familyPlanCaseInfo->physical_examinations->skin_type,
                'conjuctiva_type' => $physicalExaminationData['edit_conjuctiva_type'] ?? $familyPlanCaseInfo->physical_examinations->conjuctiva_type,
                'breast_type' => $physicalExaminationData['edit_breast_type'] ?? $familyPlanCaseInfo->physical_examinations->breast_type,
                'abdomen_type' => $physicalExaminationData['edit_abdomen_type'] ?? $familyPlanCaseInfo->physical_examinations->abdomen_type,
                'extremites_type' => $physicalExaminationData['edit_extremites_type'] ?? $familyPlanCaseInfo->physical_examinations->extremites_type,
                'extremites_UID_type' => $physicalExaminationData['edit_extremites_UID_type'] ?? $familyPlanCaseInfo->physical_examinations->extremites_UID_type,
                'cervical_abnormalities_type' => $physicalExaminationData['edit_cervical_abnormalities_type'] ?? $familyPlanCaseInfo->physical_examinations->cervical_abnormalities_type,
                'cervical_consistency_type' => $physicalExaminationData['edit_cervical_consistency_type'] ?? $familyPlanCaseInfo->physical_examinations->cervical_consistency_type,
                'uterine_position_type' => $physicalExaminationData['edit_uterine_position_type'] ?? $familyPlanCaseInfo->physical_examinations->uterine_position_type,
                'uterine_depth_text' => $physicalExaminationData['edit_uterine_depth_text'] ?? $familyPlanCaseInfo->physical_examinations->uterine_depth_text,
                'neck_type' => $physicalExaminationData['edit_neck_type'] ?? $familyPlanCaseInfo->physical_examinations->neck_type
            ]);

            return response()->json(['message' => 'Family Planning Patient Case information is updated Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }


    public function addSideBrecord(Request $request)
    {
        try {
            $data = $request->validate([
                'side_b_medical_record_case_id' => 'required',
                'side_b_health_worker_id' => 'required',
                'side_b_date_of_visit' => 'sometimes|nullable|date',
                'side_b_medical_findings' => 'sometimes|nullable|string',
                'side_b_method_accepted' => 'sometimes|nullable|string',
                'side_b_name_n_signature' => 'sometimes|nullable|string',
                'side_b_date_of_follow_up_visit' => 'sometimes|nullable|date',
                'baby_Less_than_six_months_question' => 'sometimes|nullable|string',
                'sexual_intercouse_or_mesntrual_period_question' => 'sometimes|nullable|string',
                'baby_last_4_weeks_question' => 'sometimes|nullable|string',
                'menstrual_period_in_seven_days_question' => 'sometimes|nullable|string',
                'miscarriage_or_abortion_question' => 'sometimes|nullable|string',
                'contraceptive_question' => 'sometimes|nullable|string'
            ]);

            // add the data
            family_planning_side_b_records::create([
                'medical_record_case_id' => $data['side_b_medical_record_case_id'],
                'health_worker_id' => $data['side_b_health_worker_id'],
                'date_of_visit' => $data['side_b_date_of_visit'] ?? null,
                'medical_findings' => $data['side_b_medical_findings'] ?? null,
                'method_accepted' => $data['side_b_method_accepted'] ?? null,
                'signature_of_the_provider' => $data['side_b_name_n_signature'] ?? null,
                'date_of_follow_up_visit' => $data['side_b_date_of_follow_up_visit'] ?? null,
                'baby_Less_than_six_months_question' => $data['baby_Less_than_six_months_question'] ?? null,
                'sexual_intercouse_or_mesntrual_period_question' => $data['sexual_intercouse_or_mesntrual_period_question'] ?? null,
                'baby_last_4_weeks_question' => $data['baby_last_4_weeks_question'] ?? null,
                'menstrual_period_in_seven_days_question' => $data['menstrual_period_in_seven_days_question'] ?? null,
                'miscarriage_or_abortion_question' => $data['miscarriage_or_abortion_question'] ?? null,
                'contraceptive_question' => $data['contraceptive_question'] ?? null

            ]);
            return response()->json(['message' => 'Family Planning Assessment Record Successfully Added'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() // e.g. "Attempt to read property 'blood_pressure' on null"
            ], 500);
        }
    }
    public function sideBrecords($id)
    {
        try {
            $sideBrecord = family_planning_side_b_records::findorFail($id);

            return response()->json(['sideBrecord' => $sideBrecord], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function updateSideBrecord(Request $request, $id)
    {
        try {
            $sideBrecord = family_planning_side_b_records::findOrFail($id);
            $data = $request->validate([
                'edit_side_b_medical_record_case_id' => 'required',
                'edit_side_b_health_worker_id' => 'required',
                'edit_side_b_date_of_visit' => 'sometimes|nullable|date',
                'edit_side_b_medical_findings' => 'sometimes|nullable|string',
                'edit_side_b_method_accepted' => 'sometimes|nullable|string',
                'edit_side_b_name_n_signature' => 'sometimes|nullable|string',
                'edit_side_b_date_of_follow_up_visit' => 'sometimes|nullable|date',
                'edit_baby_Less_than_six_months_question' => 'sometimes|nullable|string',
                'edit_sexual_intercouse_or_mesntrual_period_question' => 'sometimes|nullable|string',
                'edit_baby_last_4_weeks_question' => 'sometimes|nullable|string',
                'edit_menstrual_period_in_seven_days_question' => 'sometimes|nullable|string',
                'edit_miscarriage_or_abortion_question' => 'sometimes|nullable|string',
                'edit_contraceptive_question' => 'sometimes|nullable|string'
            ]);

            $sideBrecord->update([
                'medical_record_case_id' => $data['edit_side_b_medical_record_case_id'],
                'health_worker_id' => $data['edit_side_b_health_worker_id'],
                'date_of_visit' => $data['edit_side_b_date_of_visit'] ?? $sideBrecord->date_of_visit,
                'medical_findings' => $data['edit_side_b_medical_findings'] ?? $sideBrecord->medical_findings,
                'method_accepted' => $data['edit_side_b_method_accepted'] ?? $sideBrecord->method_accepted,
                'signature_of_the_provider' => $data['edit_side_b_name_n_signature'] ?? $sideBrecord->signature_of_the_provider,
                'date_of_follow_up_visit' => $data['edit_side_b_date_of_follow_up_visit'] ?? $sideBrecord->date_of_follow_up_visit,
                'baby_Less_than_six_months_question' => $data['edit_baby_Less_than_six_months_question'] ?? $sideBrecord->baby_Less_than_six_months_question,
                'sexual_intercouse_or_mesntrual_period_question' => $data['edit_sexual_intercouse_or_mesntrual_period_question'] ?? $sideBrecord->sexual_intercouse_or_mesntrual_period_question,
                'baby_last_4_weeks_question' => $data['edit_baby_last_4_weeks_question'] ?? $sideBrecord->baby_last_4_weeks_question,
                'menstrual_period_in_seven_days_question' => $data['edit_menstrual_period_in_seven_days_question'] ?? $sideBrecord->menstrual_period_in_seven_days_question,
                'miscarriage_or_abortion_question' => $data['edit_miscarriage_or_abortion_question'] ?? $sideBrecord->miscarriage_or_abortion_question,
                'contraceptive_question' => $data['edit_contraceptive_question'] ?? $sideBrecord->contraceptive_question,
                'status' => 'Done'
            ]);

            return response()->json(['message' => 'Family Planning Assessment Record Successfully Updated'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() // e.g. "Attempt to read property 'blood_pressure' on null"
            ], 500);
        }
    }
}
