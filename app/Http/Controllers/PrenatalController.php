<?php

namespace App\Http\Controllers;

use App\Models\donor_names;
use App\Models\family_planning_case_records;
use App\Models\family_planning_medical_histories;
use App\Models\family_planning_medical_records;
use App\Models\family_planning_obsterical_histories;
use App\Models\family_planning_physical_examinations;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\pregnancy_checkups;
use App\Models\pregnancy_history_questions;
use App\Models\pregnancy_plans;
use App\Models\pregnancy_timeline_records;
use App\Models\prenatal_assessment;
use App\Models\prenatal_assessments;
use App\Models\prenatal_case_records;
use App\Models\prenatal_check_ups;
use App\Models\prenatal_medical_records;
use App\Models\risk_for_sexually_transmitted_infections;
use App\Models\staff;
use App\Models\wra_masterlists;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PrenatalController extends Controller
{
    //

    public function addPatient(Request $request)
    {
        try {
            $patientData = $request->validate([
                'type_of_patient' => 'required',
                'first_name' => ['required', 'string', Rule::unique('patients')->where(function ($query) use ($request) {
                    return $query->where('first_name', $request->first_name)
                        ->where('last_name', $request->last_name);
                })],
                'last_name' => 'required|string',
                'middle_initial' => 'sometimes|nullable|string',
                'date_of_birth' => 'sometimes|nullable|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'required|numeric',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'required|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'street' => 'required',
                'brgy' => 'required',
                'suffix' => 'sometimes|nullable|string'
            ]);

            $medicalCaseData = $request->validate([
                'family_head_name' => 'sometimes|nullable|string',
                'family_serial_no' => 'sometimes|nullable|numeric',
                'blood_type' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
                'philHealth_number' => 'sometimes|nullable|string',
                'family_planning' => 'sometimes|nullable|string'
            ]);

            // case record
            $prenatalCaseData = $request->validate([
                'G' => 'sometimes|nullable|numeric',
                'P' => 'sometimes|nullable|numeric',
                'T' => 'sometimes|nullable|numeric',
                'premature' => 'sometimes|nullable|numeric',
                'abortion' => 'sometimes|nullable|numeric',
                'living_children' => 'sometimes|nullable|numeric',
                'preg_year' => 'sometimes|nullable|array',
                'type_of_delivery' => 'sometimes|nullable|array',
                'place_of_delivery' => 'sometimes|nullable|array',
                'birth_attendant' => 'sometimes|nullable|array',
                'compilation' => 'sometimes|nullable|array',
                'outcome' => 'sometimes|nullable|array',
                'LMP' => 'required|date',
                'expected_delivery' => 'required|date',
                'menarche' => 'sometimes|nullable|numeric',
                'TT1' => 'sometimes|nullable|numeric',
                'TT2' => 'sometimes|nullable|numeric',
                'TT3' => 'sometimes|nullable|numeric',
                'TT4' => 'sometimes|nullable|numeric',
                'TT5' => 'sometimes|nullable|numeric',
                'nurse_decision' => 'sometimes|nullable|string',
                'blood_pressure' => [
                    'sometimes',
                    'nullable',
                    'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                ],
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
                'add_prenatal_planning' => 'nullable|string:max:2000'

            ]);

            // assessment validation
            $assessment = $request->validate([
                'spotting' => 'sometimes|nullable|string',
                'edema' => 'sometimes|nullable|string',
                'severe_headache' => 'sometimes|nullable|string',
                'blumming_of_vission' => 'sometimes|nullable|string',
                'watery_discharge' => 'sometimes|nullable|string',
                'severe_vomiting' => 'sometimes|nullable|string',
                'hx_of_smoking' => 'sometimes|nullable|string',
                'alcohol_drinker' => 'sometimes|nullable|string',
                'drug_intake' => 'sometimes|nullable|string'
            ]);

            // pregnancy history questions validation
            $pregnancy_question = $request->validate([
                'number_of_children' => 'sometimes|nullable|numeric',
                'answer_1' => 'sometimes|nullable|string',
                'answer_2' => 'sometimes|nullable|string',
                'answer_3' => 'sometimes|nullable|string',
                'answer_4' => 'sometimes|nullable|string',
                'q2_answer1' => 'sometimes|nullable|string',
                'q2_answer2' => 'sometimes|nullable|string',
                'q2_answer3' => 'sometimes|nullable|string',
                'q2_answer4' => 'sometimes|nullable|string',
                'q2_answer5' => 'sometimes|nullable|string',
            ]);

            // pregnancy plan validation

            $pregnancy_plan = $request->validate([
                'midwife_name' => 'sometimes|nullable|string',
                'place_of_pregnancy' => 'sometimes|nullable|string',
                'authorized_by_philhealth' => 'sometimes|nullable|string',
                'cost_of_pregnancy' => 'sometimes|nullable|number',
                'payment_method' => 'sometimes|nullable|string',
                'transportation_mode' => 'sometimes|nullable|string',
                'accompany_person_to_hospital' => 'sometimes|nullable|string',
                'accompany_through_pregnancy' => 'sometimes|nullable|string',
                'care_person' => 'sometimes|nullable|string',
                'emergency_person_name' => 'sometimes|nullable|string',
                'emergency_person_residency' => 'sometimes|nullable|string',
                'emergency_person_contact_number' => 'sometimes|nullable|string',
                'signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'signature_data' => 'sometimes|nullable|string',
                'names_of_donor' => 'sometimes|nullable|array'
            ]);


            // insertion of the information

            // PATIENT INFO
            // create the patient information record
            $middle = substr($patientData['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $middleName = $patientData['middle_initial'] ? ucwords(strtolower($patientData['middle_initial'])) : '';
            $parts = [
                strtolower($patientData['first_name']),
                $middle,
                strtolower($patientData['last_name']),
                $patientData['suffix'] ?? null
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));

            $prenatalPatient = patients::create([
                'user_id' => null,
                'first_name'     => ucwords(strtolower($patientData['first_name'])),
                'middle_initial' => $middleName,
                'last_name'      => ucwords(strtolower($patientData['last_name'])),
                'full_name' => $fullName,
                'age' => $patientData['age'] ?? null,
                'sex' => isset($patientData['sex']) ? ucfirst($patientData['sex']) : null,
                'civil_status' => $patientData['civil_status'] ?? null,
                'contact_number' => $patientData['contact_number'] ?? null,
                'date_of_birth' => $patientData['date_of_birth'] ?? null,
                'profile_image' => 'images/default_profile.png',
                'nationality' => $patientData['nationality'] ?? null,
                'date_of_registration' => $patientData['date_of_registration'] ?? null,
                'place_of_birth' => $patientData['place_of_birth'] ?? null,
                'suffix' => $patientData['suffix'] ?? '',
            ]);

            // use the id of the created patient for medical case record
            $prenatalPatientId =  $prenatalPatient->id;

            // add the patient address
            // dd($patient->id);
            $blk_n_street = explode(',', $patientData['street']);
            // dd($blk_n_street);
            $patientAddress  = patient_addresses::create([
                'patient_id' => $prenatalPatientId,
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
                'patient_id' => $prenatalPatientId,
                'type_of_case' => $patientData['type_of_patient'],
            ]);

            // get the id of the medical case for prenatal medical record and case
            $medicalCaseId = $medicalCase->id;

            // create a record for the prenatal medical record
            $prenatalMedicalRecord = prenatal_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'family_head_name' => $medicalCaseData['family_head_name'] ?? null,
                'blood_type' => $medicalCaseData['blood_type'] ?? null,
                'religion' => $medicalCaseData['religion'] ?? null,
                'philHealth_number' => $medicalCaseData['philHealth_number'] ?? null,
                'family_serial_no' => $medicalCaseData['family_serial_no'] ?? null,
                'family_planning_decision' => $medicalCaseData['family_planning'] ?? null,
                'health_worker_id' => $patientData['handled_by'],
                'type_of_record' => 'Medical Record'
            ]);

            // create a record for prenatal case
            $prenatalCaseRecord = prenatal_case_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'patient_name' => $prenatalPatient->full_name,
                'G' => $prenatalCaseData['G'] ?? null,
                'P' => $prenatalCaseData['P'] ?? null,
                'T' => $prenatalCaseData['T'] ?? null,
                'premature' => $prenatalCaseData['premature'] ?? null,
                'abortion' => $prenatalCaseData['abortion'] ?? null,
                'living_children' => $prenatalCaseData['living_children'],
                'LMP' => $prenatalCaseData['LMP'],
                'expected_delivery' => $prenatalCaseData['expected_delivery'],
                'menarche' => $prenatalCaseData['menarche'],
                'tetanus_toxoid_1' => $prenatalCaseData['TT1'] ?? null,
                'tetanus_toxoid_2' => $prenatalCaseData['TT2'] ?? null,
                'tetanus_toxoid_3' => $prenatalCaseData['TT3'] ?? null,
                'tetanus_toxoid_4' => $prenatalCaseData['TT4'] ?? null,
                'tetanus_toxoid_5' => $prenatalCaseData['TT5'] ?? null,
                'decision' => $prenatalCaseData['nurse_decision'] ?? null,
                'blood_pressure' => $prenatalCaseData['blood_pressure'] ?? null,
                'temperature' => $prenatalCaseData['temperature'] ?? null,
                'pulse_rate' => $prenatalCaseData['pulse_rate'] ?? null,
                'respiratory_rate' => $prenatalCaseData['respiratory_rate'] ?? null,
                'height' => $prenatalCaseData['height'] ?? null,
                'weight' => $prenatalCaseData['weight'] ?? null,
                'health_worker_id' => $patientData['handled_by'],
                'type_of_record' => 'Case Record',
                'planning' => $prenatalCaseData['add_prenatal_planning'] ?? null
            ]);

            // insert the pregnancy timeline
            $isPregYear = $prenatalCaseData['preg_year'] ?? null;
            if ($isPregYear) {
                foreach ($prenatalCaseData['preg_year'] as $index => $year) {
                    pregnancy_timeline_records::create([
                        'prenatal_case_record_id' => $prenatalCaseRecord->id,
                        'year' => $year ?? null,
                        'type_of_delivery' => $prenatalCaseData['type_of_delivery'][$index] ?? null,
                        'place_of_delivery' => $prenatalCaseData['place_of_delivery'][$index] ?? null,
                        'birth_attendant' => $prenatalCaseData['birth_attendant'][$index] ?? null,
                        'compilation' => $prenatalCaseData['compilation'][$index] ?? null,
                        'outcome' => $prenatalCaseData['outcome'][$index] ?? null,
                    ]);
                };
            }



            // insert assessment
            $prenatalAssessment = prenatal_assessments::create([
                'prenatal_case_record_id' => $prenatalCaseRecord->id,
                'spotting' => $assessment['spotting'] ?? null,
                'edema' => $assessment['edema'] ?? null,
                'severe_headache' => $assessment['severe_headache'] ?? null,
                'blumming_vission' => $assessment['blumming_of_vission'] ?? null,
                'water_discharge' => $assessment['water_discharge'] ?? null,
                'severe_vomitting' => $assessment['severe_vomiting'] ?? null,
                'hx_smoking' => $assessment['hx_of_smoking'] ?? null,
                'alchohol_drinker' => $assessment['alcohol_drinker'] ?? null,
                'drug_intake' => $assessment['drug_intake'] ?? null,
            ]);



            // pregnancy history questions
            $pregnancyQuestions = pregnancy_history_questions::create([
                'prenatal_case_record_id' => $prenatalCaseRecord->id,
                'number_of_children' => $pregnancy_question['number_of_children'] ?? null,
                'answer_1' => $pregnancy_question['answer_1'] ?? null,
                'answer_2' => $pregnancy_question['answer_2'] ?? null,
                'answer_3' => $pregnancy_question['answer_3'] ?? null,
                'answer_4' => $pregnancy_question['answer_4'] ?? null,
                'q2_answer1' => $pregnancy_question['q2_answer1'] ?? null,
                'q2_answer2' => $pregnancy_question['q2_answer2'] ?? null,
                'q2_answer3' => $pregnancy_question['q2_answer3'] ?? null,
                'q2_answer4' => $pregnancy_question['q2_answer4'] ?? null,
                'q2_answer5' => $pregnancy_question['q2_answer5'] ?? null,
            ]);

            // pregnancy plan
            $signaturePath = null;

            // If user uploaded an image file
            if ($request->hasFile('signature_image')) {
                $signaturePath = $this->compressAndSaveSignature($request->file('signature_image'));
            }
            // If user drew a signature
            else if ($request->filled('signature_data')) {
                $signaturePath = $this->saveCanvasSignature($request->signature_data);
            }


            $pregnancyPlanRecord = pregnancy_plans::create([
                'medical_record_case_id' => $medicalCaseId,
                'patient_name' => $prenatalPatient->full_name,
                'midwife_name' => $pregnancy_plan['midwife_name'] ?? null,
                'place_of_pregnancy' => $pregnancy_plan['place_of_pregnancy'] ?? null,
                'authorized_by_philhealth' => $pregnancy_plan['authorized_by_philhealth'] ?? null,
                'cost_of_pregnancy' => $pregnancy_plan['cost_of_pregnancy'] ?? null,
                'payment_method' => $pregnancy_plan['payment_method'] ?? null,
                'transportation_mode' => $pregnancy_plan['transportation_mode'] ?? null,
                'accompany_person_to_hospital' => $pregnancy_plan['accompany_person_to_hospital'] ?? null,
                'accompany_through_pregnancy' =>  $pregnancy_plan['accompany_through_pregnancy'] ?? null,
                'care_person' => $pregnancy_plan['care_person'] ?? null,
                'emergency_person_name' => $pregnancy_plan['emergency_person_name'] ?? null,
                'emergency_person_residency' => $pregnancy_plan['emergency_person_residency'] ?? null,
                'emergency_person_contact_number' => $pregnancy_plan['emergency_person_contact_number'] ?? null,
                'signature' =>  $signaturePath ?? null,
                'type_of_record' => 'Pregnancy Plan Record'
            ]);

            // insert 
            $isDonorNames = $pregnancy_plan['names_of_donor'] ?? null;
            if ($isDonorNames) {
                foreach ($pregnancy_plan['names_of_donor'] as $index => $name) {
                    donor_names::create([
                        'pregnancy_plan_id' => $pregnancyPlanRecord->id,
                        'donor_name' => $name
                    ]);
                };
            }

            // if the family planning answer is yes, then we will create a family planning record for the pregnancy patient
            $isFamilyPlan = $medicalCaseData['family_planning'] ?? null;

            if ($isFamilyPlan != null && $isFamilyPlan === 'yes') {
                // add record for medical_case table
                $familyPlanningMedicalCase = medical_record_cases::create([
                    'patient_id' => $prenatalPatient->id,
                    'type_of_case' => 'family-planning',
                ]);

                $familyPlanningMedicalCaseId = $familyPlanningMedicalCase->id; //medical record id


                // CREATE THE MEDICAL RECORD
                family_planning_medical_records::create([
                    'medical_record_case_id' => $familyPlanningMedicalCaseId,
                    'health_worker_id' => $patientData['handled_by'],
                    'patient_name' => $prenatalPatient->full_name,
                    'occupation' =>  null,
                    'religion' => $medicalCaseData['religion'] ?? null,
                    'philhealth_no' =>  null,
                    'blood_pressure' => $prenatalCaseData['blood_pressure'] ?? null,
                    'temperature' => $prenatalCaseData['temperature'] ?? null,
                    'pulse_rate' => $prenatalCaseData['pulse_rate'] ?? null,
                    'respiratory_rate' => $prenatalCaseData['respiratory_rate'] ?? null,
                    'height' => $prenatalCaseData['height'] ?? null,
                    'weight' => $prenatalCaseData['weight'] ?? null,
                ]);

                $previoulyMethod = null;

                // CREATE THE CASE RECORD
                $caseRecord = family_planning_case_records::create([
                    'medical_record_case_id' =>  $familyPlanningMedicalCaseId,
                    'health_worker_id' =>  $patientData['handled_by'],
                    'client_id' =>  null,
                    'philhealth_no' =>  null,
                    'NHTS' =>  null,
                    'client_name' =>  $prenatalPatient->full_name,
                    'client_date_of_birth' => $prenatalPatient['date_of_birth'] ?? null,
                    'client_age' => $prenatalPatient['age'] ?? null,
                    'client_suffix' => $patientData['suffix'] ?? '',
                    'occupation' => null,
                    'client_address' => $fullAddress,
                    'client_contact_number' => $prenatalPatient['contact_number'] ?? null,
                    'client_civil_status' => $prenatalPatient['civil_status'] ?? null,
                    'client_religion' => $medicalCaseData['religion'] ?? null,
                    'spouse_lname' =>  null,
                    'spouse_fname' =>  null,
                    'spouse_MI' =>  null,
                    'spouse_date_of_birth' =>  null,
                    'spouse_age' =>  null,
                    'spouse_occupation' =>  null,
                    'number_of_living_children' => $prenatalCaseData['living_children'] ?? null,
                    'plan_to_have_more_children' =>  null,

                    'average_montly_income' => null,
                    'type_of_patient' => null,
                    'new_acceptor_reason_for_FP' => null,
                    'current_user_reason_for_FP' =>  null,
                    'current_method_reason' => null,
                    'previously_used_method' =>  null,
                    'choosen_method' => null,
                    'signature_image' =>  null,
                    'date_of_acknowledgement' =>  null,
                    'acknowledgement_consent_signature_image' =>  null,
                    'date_of_acknowledgement_consent' =>  null,
                    'current_user_type' =>  null,
                ]);

                $caseId = $caseRecord->id;

                // medical history
                $medicalHistories = family_planning_medical_histories::create([
                    'case_id' => $caseId,
                    'severe_headaches_migraine' =>  null,
                    'history_of_stroke' =>  null,
                    'non_traumatic_hemtoma' =>  null,
                    'history_of_breast_cancer' =>  null,
                    'severe_chest_pain' =>  null,
                    'cough' =>  null,
                    'jaundice' =>  null,
                    'unexplained_vaginal_bleeding' =>  null,
                    'abnormal_vaginal_discharge' =>  null,
                    'abnormal_phenobarbital' =>  null,
                    'smoker' =>  null,
                    'with_dissability' =>  null,
                    'if_with_dissability_specification' =>  null,
                ]);
                // obsterical history
                $obstericalHistories = family_planning_obsterical_histories::create([
                    'case_id' => $caseId,
                    'G' => $prenatalCaseData['G']  ?? null,
                    'P' => $prenatalCaseData['P']  ?? null,
                    'full_term' => null,
                    'abortion' => $prenatalCaseData['abortion'] ?? null,
                    'premature' => $prenatalCaseData['premature']  ?? null,
                    'living_children' => $prenatalCaseData['living_children'] ?? null,
                    'date_of_last_delivery' => null,
                    'type_of_last_delivery' =>  null,
                    'date_of_last_delivery_menstrual_period' => null,
                    'date_of_previous_delivery_menstrual_period' =>  null,
                    'type_of_menstrual' =>  null,
                    'Dysmenorrhea' =>  null,
                    'hydatidiform_mole' =>  null,
                    'ectopic_pregnancy' =>  null,
                ]);

                // III. RISK FOR SEXUALLY TRANSMITTED INFECTIONS

                $riskOfSexuallyTransmitted = risk_for_sexually_transmitted_infections::create([
                    'case_id' => $caseId,
                    'infection_abnormal_discharge_from_genital_area' =>  null,
                    'origin_of_abnormal_discharge' =>  null,
                    'scores_or_ulcer' =>  null,
                    'pain_or_burning_sensation' =>  null,
                    'history_of_sexually_transmitted_infection' =>  null,
                    'sexually_transmitted_disease' =>  null,
                    'history_of_domestic_violence_of_VAW' =>  null,
                    'unpleasant_relationship_with_partner' =>  null,
                    'partner_does_not_approve' =>  null,
                    'referred_to' =>  null,
                    'reffered_to_others' =>  null,
                ]);

                // PHYSICAL EXAMINATION
                $physicalExamination = family_planning_physical_examinations::create([
                    'case_id' => $caseId,
                    'blood_pressure' => $prenatalCaseData['blood_pressure'] ?? null,
                    'pulse_rate' => $prenatalCaseData['pulse_rate'] ?? null,
                    'height' => $prenatalCaseData['height']  ?? null,
                    'weight' => $prenatalCaseData['weight']  ?? null,

                    'skin_type' =>  null,
                    'conjuctiva_type' =>  null,
                    'breast_type' =>  null,
                    'abdomen_type' =>  null,
                    'extremites_type' =>  null,
                    'extremites_UID_type' =>  null,
                    'cervical_abnormalities_type' =>  null,
                    'cervical_consistency_type' =>  null,
                    'uterine_position_type' =>  null,
                    'uterine_depth_text' =>  null,
                    'neck_type' =>  null
                ]);

                // add side b record
                family_planning_side_b_records::create([
                    'medical_record_case_id' => $familyPlanningMedicalCaseId,
                    'health_worker_id' => $patientData['handled_by'],
                    'date_of_visit' =>  null,
                    'medical_findings' =>  null,
                    'method_accepted' =>  null,
                    'signature_of_the_provider' =>  null,
                    'date_of_follow_up_visit' =>  null,
                    'baby_Less_than_six_months_question' =>  null,
                    'sexual_intercouse_or_mesntrual_period_question' =>  null,
                    'baby_last_4_weeks_question' =>  null,
                    'menstrual_period_in_seven_days_question' =>  null,
                    'miscarriage_or_abortion_question' =>  null,
                    'contraceptive_question' =>  null
                ]);

                // create the wra record
                // --------------------------------------------------- WRA masterlist record -------------------------------------------------------------------------
                $method_of_FP = [
                    'modern' => ['Implant', 'IUD', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                    'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
                ];

                $modern_methods = [];
                $traditional_methods = [];

                $previously_methods = $caseData['previously_used_method'] ?? null;
                if ($previously_methods != null) {
                    foreach ($previously_methods as $method) {
                        if (in_array($method, $method_of_FP['modern'])) {
                            $modern_methods[] = $method;
                        } elseif (in_array($method, $method_of_FP['traditional'])) {
                            $traditional_methods[] = $method;
                        }
                    }
                }
                // convert them to string
                $converted_modern_methods = implode(",", $modern_methods);
                $converted_traditional_methods = implode(",", $traditional_methods);

                // check if the patient currently accept any modern methods
                $method_accepted = [];
                $currently_choosen_method = $caseData['choosen_method'] ?? null;
                if ($currently_choosen_method) {
                    $method_accepted = explode(",", $currently_choosen_method);
                }

                $accept_modern_FP = [];
                foreach ($method_accepted as $method) {

                    if (in_array($method, $method_of_FP['modern'])) {
                        $accept_modern_FP[] = $method;
                    }
                }
                $converted_accepted_modern_FP = implode(",", $accept_modern_FP);

                if ($prenatalPatient->age >= 10) {
                    $wra_masterlist = wra_masterlists::create([
                        'medical_record_case_id' => $familyPlanningMedicalCaseId,
                        'health_worker_id' => $patientData['handled_by'],
                        'address_id' => $patientAddress->id,
                        'patient_id' => $prenatalPatient->id,
                        'brgy_name' => $patientAddress->purok,
                        'house_hold_number' => null,
                        'name_of_wra' => $prenatalPatient->full_name,
                        'address' => $fullAddress,
                        'age' => $prenatalPatient->age ?? null,
                        'date_of_birth' => $prenatalPatient->date_of_birth ?? null,
                        'SE_status' => null,
                        'plan_to_have_more_children_yes' => null,
                        'plan_to_have_more_children_no' =>  null,
                        'current_FP_methods' => null,
                        'modern_FP' =>  null,
                        'traditional_FP' =>  null,
                        'currently_using_any_FP_method_no' =>  null,
                        'shift_to_modern_method' => null,
                        'wra_with_MFP_unmet_need' => 'no',
                        'wra_accept_any_modern_FP_method' => null,
                        'selected_modern_FP_method' => null,
                        'date_when_FP_method_accepted' =>  null
                    ]);
                }
            } else {
                if ($prenatalPatient->age >= 10) {
                    $wra_masterlist = wra_masterlists::create([
                        'medical_record_case_id' => null,
                        'health_worker_id' => $patientData['handled_by'],
                        'address_id' => $patientAddress->id,
                        'patient_id' => $prenatalPatient->id,
                        'brgy_name' => $patientAddress->purok,
                        'house_hold_number' => null,
                        'name_of_wra' => $prenatalPatient->full_name,
                        'address' => $fullAddress,
                        'age' => $prenatalPatient->age ?? null,
                        'date_of_birth' => $prenatalPatient->date_of_birth ?? null,
                        'SE_status' => null,
                        'plan_to_have_more_children_yes' => null,
                        'plan_to_have_more_children_no' =>  null,
                        'current_FP_methods' => null,
                        'modern_FP' =>  null,
                        'traditional_FP' =>  null,
                        'currently_using_any_FP_method_no' =>  null,
                        'shift_to_modern_method' => null,
                        'wra_with_MFP_unmet_need' => 'yes',
                        'wra_accept_any_modern_FP_method' => null,
                        'selected_modern_FP_method' => null,
                        'date_when_FP_method_accepted' =>  null
                    ]);
                }
            }

            return response()->json(['message' => 'Patient has been added'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function updateDetails(Request $request, $id)
    {
        try {
            $prenatalRecord = medical_record_cases::with(['patient', 'prenatal_medical_record'])->where('id', $id)->firstOrFail();
            $address = patient_addresses::where('patient_id', $prenatalRecord->patient->id)->firstOrFail();
            $caseRecord = prenatal_case_records::where('medical_record_case_id', $prenatalRecord->id)->firstOrFail();

            $data = $request->validate([
                'first_name' => ['required', 'string', Rule::unique('patients')->where(function ($query) use ($request) {
                    return $query->where('first_name', $request->first_name)
                        ->where('last_name', $request->last_name);
                })->ignore($prenatalRecord->patient_id)],
                'last_name' => 'required|string',
                'middle_initial' => 'sometimes|nullable|string',
                'date_of_birth' => 'sometimes|nullable|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'required|numeric',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'required|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'family_head' => 'sometimes|nullable|string',
                'civil_status' => 'sometimes|nullable|string',
                'blood_type' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
                'philhealth' => 'sometimes|nullable|string',
                'philhealth_number' => 'sometimes|nullable|string',
                'family_planning' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy' => 'required',
                'blood_pressure' => [
                    'sometimes',
                    'nullable',
                    'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                ],
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
                'number_of_children' => 'sometimes|nullable|numeric',
                'answer_1' => 'sometimes|nullable|string',
                'answer_2' => 'sometimes|nullable|string',
                'answer_3' => 'sometimes|nullable|string',
                'answer_4' => 'sometimes|nullable|string',
                'q2_answer1' => 'sometimes|nullable|string',
                'q2_answer2' => 'sometimes|nullable|string',
                'q2_answer3' => 'sometimes|nullable|string',
                'q2_answer4' => 'sometimes|nullable|string',
                'q2_answer5' => 'sometimes|nullable|string',
                'family_serial_no' => 'sometimes|nullable|numeric',
                'nurse_decision' => 'sometimes|nullable|numeric',
                'suffix' => 'sometimes|nullable|string'
            ]);

            $middle = substr($data['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $middleName = $data['middle_initial'] ? ucwords(strtolower($data['middle_initial'])) : '';
            $parts = [
                strtolower($data['first_name']),
                $middle,
                strtolower($data['last_name']),
                $data['suffix'] ?? null
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));
            $sex = isset($data['sex']) ? $data['sex'] : $prenatalRecord->patient->sex;
            // update the patient data first
            $prenatalRecord->patient->update([
                'first_name' => ucwords($data['first_name']) ?? ucwords($prenatalRecord->patient->first_name),
                'middle_initial' => $middleName,
                'last_name' => ucwords($data['last_name']) ?? ucwords($prenatalRecord->patient->last_name),
                'full_name' => $fullName,
                'age' => $data['age'] ?? $prenatalRecord->patient->age,
                'sex' => $sex ? ucfirst($sex) : null,
                'civil_status' => $data['civil_status'] ?? '',
                'contact_number' => $data['contact_number'] ?? '',
                'date_of_birth' => $data['date_of_birth'] ?? $prenatalRecord->patient->date_of_birth,
                'nationality' => $data['nationality'] ?? '',
                'date_of_registration' => $data['date_of_registration'] ?? $prenatalRecord->patient->date_of_registration,
                'place_of_birth' => $data['place_of_birth'] ?? $prenatalRecord->patient->place_of_birth,
                'suffix' => $data['suffix'] ?? '',
            ]);

            // update the address
            // Parse address - limit to 2 parts maximum
            $blk_n_street = explode(',', $data['street'], 2);

            $address->update([
                'house_number' => trim($blk_n_street[0]),
                'street' => isset($blk_n_street[1]) ? trim($blk_n_street[1]) : '',
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

            // update the case
            $caseRecord->update([
                'decision' => $data['nurse_decision'] ?? $caseRecord->decision
            ]);

            $prenatalRecord->prenatal_medical_record->update([
                'family_head_name' => $data['family_head'] ?? $prenatalRecord->prenatal_medical_record->family_head_name,
                'blood_type' => $data['blood_type'] ?? $prenatalRecord->prenatal_medical_record->blood_type,
                'religion' => $data['religion'] ?? $prenatalRecord->prenatal_medical_record->religion,
                'philHealth_number' => $data['philhealth_number'] ?? $prenatalRecord->prenatal_medical_record->philHealth_number,
                'family_serial_no' => $data['family_serial_no'] ?? $prenatalRecord->prenatal_medical_record->family_serial_no,
                'family_planning_decision' => $data['family_planning'] ?? $prenatalRecord->prenatal_medical_record->family_planning_decision,
                'health_worker_id' => $data['handled_by'] ?? $prenatalRecord->prenatal_medical_record->health_worker_id,
            ]);
            // update the case info
            $prenatalCaseRecord = prenatal_case_records::where('medical_record_case_id', $prenatalRecord->id)->where('status', '!=', 'Archived')->firstOrFail();
            $prenatalCaseRecord->update([
                'patient_name' => $prenatalRecord->patient->full_name,
                'health_worker_id' => $data['handled_by'] ?? $prenatalRecord->$prenatalCaseRecord->health_worker_id,
                'blood_pressure' => $data['blood_pressure'] ?? null,
                'temperature' => $data['temperature'] ?? null,
                'pulse_rate' => $data['pulse_rate'] ?? null,
                'respiratory_rate' => $data['respiratory_rate'] ?? null,
                'height' => $data['height'] ?? null,
                'weight' => $data['weight'] ?? null,
                'status' => 'Active'
            ]);

            // update the pregnancy history
            $pregnancyHistory = pregnancy_history_questions::where('prenatal_case_record_id', $prenatalCaseRecord->id)->firstOrfail();
            $pregnancyHistory->update([
                'number_of_children' => $data['number_of_children'] ?? $pregnancyHistory->number_of_children,
                'answer_1' => $data['answer_1'] ?? $pregnancyHistory->answer_1,
                'answer_2' => $data['answer_2'] ?? $pregnancyHistory->answer_2,
                'answer_3' => $data['answer_3'] ?? $pregnancyHistory->answer_3,
                'answer_4' => $data['answer_4'] ?? $pregnancyHistory->answer_4,
                'q2_answer1' => $data['q2_answer1'] ?? $pregnancyHistory->q2_answer1,
                'q2_answer2' => $data['q2_answer2'] ?? $pregnancyHistory->q2_answer2,
                'q2_answer3' => $data['q2_answer3'] ?? $pregnancyHistory->q2_answer3,
                'q2_answer4' => $data['q2_answer4'] ?? $pregnancyHistory->q2_answer4,
                'q2_answer5' => $data['q2_answer5'] ?? $pregnancyHistory->q2_answer5,
            ]);

            // update the case
            // update pregnancy plan patient name
            $pregnancyPlanRecord = pregnancy_plans::where('medical_record_case_id', $id)->firstOrFail();
            $pregnancyPlanRecord->update([
                'patient_name' => $prenatalRecord->patient->full_name
            ]);

            $isFamilyPlan = $data['family_planning'] ?? null;

            if ($isFamilyPlan != null && $isFamilyPlan === 'yes') {
                // check if there is an existing family plan
                $hasExistingFamilyPlan = medical_record_cases::where('patient_id', $prenatalRecord->patient->id)->where('type_of_case', 'family-planning')->exists();

                if (!empty($hasExistingFamilyPlan)) {
                    $existingFamilyPlan = medical_record_cases::where('patient_id', $prenatalRecord->patient->id)->where('type_of_case', 'family-planning')->first();

                    if ($existingFamilyPlan->status == 'Archived') {
                        $family_planning_sideA = family_planning_case_records::where('medical_record_case_id', $existingFamilyPlan->id)->first() ?? null;
                        $family_planning_sideB = family_planning_side_b_records::where('medical_record_case_id', $existingFamilyPlan->id)->first() ?? null;
                        $wra_record = wra_masterlists::where('patient_id', $prenatalRecord->patient->id)->first();

                        // make the records archived
                        $existingFamilyPlan->update(['status' => 'Active']);
                        if ($family_planning_sideA && $family_planning_sideB && $wra_record) {
                            $family_planning_sideA->update([
                                'status' => 'Active',
                            ]);

                            $family_planning_sideB->update([
                                'status' => 'Active'
                            ]);
                            $wra_record->update([
                                'plan_to_have_more_children_yes' => null,
                                'plan_to_have_more_children_no' =>  null,
                                'current_FP_methods' => null,
                                'modern_FP' =>  null,
                                'traditional_FP' =>  null,
                                'currently_using_any_FP_method_no' =>  null,
                                'shift_to_modern_method' => null,
                                'wra_with_MFP_unmet_need' => 'no',
                                'wra_accept_any_modern_FP_method' => null,
                                'selected_modern_FP_method' => null,
                                'date_when_FP_method_accepted' =>  null
                            ]);
                            $message = 'family planning deactivated';
                        }

                        $message = 'Family planning reactivated and patient information updated successfully';
                    }

                    $message = 'Family planning already exists and is active';
                }
                // add record for medical_case table
                $existingFamilyPlan = medical_record_cases::where('patient_id', $prenatalRecord->patient->id)->where('type_of_case', 'family-planning')->first();
                if ($existingFamilyPlan) {
                    $familyPlanningMedicalCase = $existingFamilyPlan;
                } else {
                    $familyPlanningMedicalCase = medical_record_cases::create([
                        'patient_id' => $prenatalRecord->patient->id,
                        'type_of_case' => 'family-planning',
                    ]);
                }


                $familyPlanningMedicalCaseId = $familyPlanningMedicalCase->id; //medical record id

                $existingMedicalRecord = family_planning_medical_records::where("medical_record_case_id", $familyPlanningMedicalCaseId)->first();

                if (!$existingMedicalRecord) {
                    // CREATE THE MEDICAL RECORD
                    family_planning_medical_records::create([
                        'medical_record_case_id' => $familyPlanningMedicalCaseId,
                        'health_worker_id' => $data['handled_by'],
                        'patient_name' => $prenatalRecord->patient->full_name,
                        'occupation' =>  null,
                        'religion' => $data['religion'] ?? null,
                        'philhealth_no' =>  null,
                        'blood_pressure' => $data['blood_pressure'] ?? null,
                        'temperature' => $data['temperature'] ?? null,
                        'pulse_rate' => $data['pulse_rate'] ?? null,
                        'respiratory_rate' => $data['respiratory_rate'] ?? null,
                        'height' => $data['height'] ?? null,
                        'weight' => $data['weight'] ?? null,
                    ]);
                }


                $previoulyMethod = null;
                $family_planning_sideA = family_planning_case_records::where('medical_record_case_id', $existingFamilyPlan->id)->where('status', "!=", 'Archived')->first();
                $family_planning_sideB = family_planning_side_b_records::where('medical_record_case_id', $existingFamilyPlan->id)->where('status', "!=", 'Archived')->first();
                if (!$family_planning_sideA && !$family_planning_sideB) {

                    // CREATE THE CASE RECORD
                    $caseRecord = family_planning_case_records::create([
                        'medical_record_case_id' =>  $familyPlanningMedicalCaseId,
                        'health_worker_id' =>  $data['handled_by'],
                        'client_id' =>  null,
                        'philhealth_no' =>  null,
                        'NHTS' =>  null,
                        'client_name' =>  $prenatalRecord->patient->full_name,
                        'client_date_of_birth' => $data['date_of_birth'] ?? null,
                        'client_suffix' => $data['suffix'] ?? '',
                        'client_age' => $data['age'] ?? null,
                        'occupation' => null,
                        'client_address' => $fullAddress,
                        'client_contact_number' => $data['contact_number'] ?? null,
                        'client_civil_status' => $data['civil_status'] ?? null,
                        'client_religion' => $medicalCaseData['religion'] ?? null,
                        'spouse_lname' =>  null,
                        'spouse_fname' =>  null,
                        'spouse_MI' =>  null,
                        'spouse_date_of_birth' =>  null,
                        'spouse_age' =>  null,
                        'spouse_occupation' =>  null,
                        'number_of_living_children' => $data['living_children'] ?? null,
                        'plan_to_have_more_children' =>  null,

                        'average_montly_income' => null,
                        'type_of_patient' => null,
                        'new_acceptor_reason_for_FP' => null,
                        'current_user_reason_for_FP' =>  null,
                        'current_method_reason' => null,
                        'previously_used_method' =>  null,
                        'choosen_method' => null,
                        'signature_image' =>  null,
                        'date_of_acknowledgement' =>  null,
                        'acknowledgement_consent_signature_image' =>  null,
                        'date_of_acknowledgement_consent' =>  null,
                        'current_user_type' =>  null,
                    ]);

                    $caseId = $caseRecord->id;

                    // medical history
                    $medicalHistories = family_planning_medical_histories::create([
                        'case_id' => $caseId,
                        'severe_headaches_migraine' =>  null,
                        'history_of_stroke' =>  null,
                        'non_traumatic_hemtoma' =>  null,
                        'history_of_breast_cancer' =>  null,
                        'severe_chest_pain' =>  null,
                        'cough' =>  null,
                        'jaundice' =>  null,
                        'unexplained_vaginal_bleeding' =>  null,
                        'abnormal_vaginal_discharge' =>  null,
                        'abnormal_phenobarbital' =>  null,
                        'smoker' =>  null,
                        'with_dissability' =>  null,
                        'if_with_dissability_specification' =>  null,
                    ]);
                    // obsterical history
                    $obstericalHistories = family_planning_obsterical_histories::create([
                        'case_id' => $caseId,
                        'G' =>  null,
                        'P' => null,
                        'full_term' => null,
                        'abortion' =>  null,
                        'premature' =>  null,
                        'living_children' =>  null,
                        'date_of_last_delivery' => null,
                        'type_of_last_delivery' =>  null,
                        'date_of_last_delivery_menstrual_period' => null,
                        'date_of_previous_delivery_menstrual_period' =>  null,
                        'type_of_menstrual' =>  null,
                        'Dysmenorrhea' =>  null,
                        'hydatidiform_mole' =>  null,
                        'ectopic_pregnancy' =>  null,
                    ]);

                    // III. RISK FOR SEXUALLY TRANSMITTED INFECTIONS

                    $riskOfSexuallyTransmitted = risk_for_sexually_transmitted_infections::create([
                        'case_id' => $caseId,
                        'infection_abnormal_discharge_from_genital_area' =>  null,
                        'origin_of_abnormal_discharge' =>  null,
                        'scores_or_ulcer' =>  null,
                        'pain_or_burning_sensation' =>  null,
                        'history_of_sexually_transmitted_infection' =>  null,
                        'sexually_transmitted_disease' =>  null,
                        'history_of_domestic_violence_of_VAW' =>  null,
                        'unpleasant_relationship_with_partner' =>  null,
                        'partner_does_not_approve' =>  null,
                        'referred_to' =>  null,
                        'reffered_to_others' =>  null,
                    ]);

                    // PHYSICAL EXAMINATION
                    $physicalExamination = family_planning_physical_examinations::create([
                        'case_id' => $caseId,
                        'blood_pressure' => $prenatalCaseData['blood_pressure'] ?? null,
                        'pulse_rate' => $prenatalCaseData['pulse_rate'] ?? null,
                        'height' => $prenatalCaseData['height']  ?? null,
                        'weight' => $prenatalCaseData['weight']  ?? null,

                        'skin_type' =>  null,
                        'conjuctiva_type' =>  null,
                        'breast_type' =>  null,
                        'abdomen_type' =>  null,
                        'extremites_type' =>  null,
                        'extremites_UID_type' =>  null,
                        'cervical_abnormalities_type' =>  null,
                        'cervical_consistency_type' =>  null,
                        'uterine_position_type' =>  null,
                        'uterine_depth_text' =>  null,
                        'neck_type' =>  null
                    ]);

                    // add side b record
                    family_planning_side_b_records::create([
                        'medical_record_case_id' => $familyPlanningMedicalCaseId,
                        'health_worker_id' => $data['handled_by'],
                        'date_of_visit' =>  null,
                        'medical_findings' =>  null,
                        'method_accepted' =>  null,
                        'signature_of_the_provider' =>  null,
                        'date_of_follow_up_visit' =>  null,
                        'baby_Less_than_six_months_question' =>  null,
                        'sexual_intercouse_or_mesntrual_period_question' =>  null,
                        'baby_last_4_weeks_question' =>  null,
                        'menstrual_period_in_seven_days_question' =>  null,
                        'miscarriage_or_abortion_question' =>  null,
                        'contraceptive_question' =>  null
                    ]);
                }


                // create the wra record
                // --------------------------------------------------- WRA masterlist record -------------------------------------------------------------------------
                $method_of_FP = [
                    'modern' => ['Implant', 'IUD', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                    'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
                ];

                $modern_methods = [];
                $traditional_methods = [];

                $previously_methods = $caseData['previously_used_method'] ?? null;
                if ($previously_methods != null) {
                    foreach ($previously_methods as $method) {
                        if (in_array($method, $method_of_FP['modern'])) {
                            $modern_methods[] = $method;
                        } elseif (in_array($method, $method_of_FP['traditional'])) {
                            $traditional_methods[] = $method;
                        }
                    }
                }
                // convert them to string
                $converted_modern_methods = implode(",", $modern_methods);
                $converted_traditional_methods = implode(",", $traditional_methods);

                // check if the patient currently accept any modern methods
                $method_accepted = [];
                $currently_choosen_method = $caseData['choosen_method'] ?? null;
                if ($currently_choosen_method) {
                    $method_accepted = explode(",", $currently_choosen_method);
                }

                $accept_modern_FP = [];
                foreach ($method_accepted as $method) {

                    if (in_array($method, $method_of_FP['modern'])) {
                        $accept_modern_FP[] = $method;
                    }
                }
                $converted_accepted_modern_FP = implode(",", $accept_modern_FP);

                if ($prenatalRecord->patient->age >= 10) {
                    // check if there is wra record 
                    $wra_masterlist = wra_masterlists::where('patient_id', $prenatalRecord->patient->id)->first();
                    if ($wra_masterlist) {
                        $wra_masterlist->update([
                            'brgy_name' => $address->purok,
                            'house_hold_number' => null,
                            'name_of_wra' => $prenatalRecord->patient->full_name,
                            'address' => $fullAddress,
                            'age' => $prenatalRecord->patient->age ?? null,
                            'date_of_birth' => $prenatalRecord->patient->date_of_birth ?? null,
                        ]);
                    } else {
                        $wra_masterlist = wra_masterlists::create([
                            'medical_record_case_id' => $familyPlanningMedicalCaseId,
                            'health_worker_id' => $data['handled_by'],
                            'address_id' => $address->id,
                            'patient_id' => $prenatalRecord->patient->id,
                            'brgy_name' => $address->purok,
                            'house_hold_number' => null,
                            'name_of_wra' => $prenatalRecord->patient->full_name,
                            'address' => $fullAddress,
                            'age' => $prenatalRecord->patient->age ?? null,
                            'date_of_birth' => $prenatalRecord->patient->date_of_birth ?? null,
                            'SE_status' => null,
                            'plan_to_have_more_children_yes' => null,
                            'plan_to_have_more_children_no' =>  null,
                            'current_FP_methods' => null,
                            'modern_FP' =>  null,
                            'traditional_FP' =>  null,
                            'currently_using_any_FP_method_no' =>  null,
                            'shift_to_modern_method' => null,
                            'wra_with_MFP_unmet_need' => 'no',
                            'wra_accept_any_modern_FP_method' => null,
                            'selected_modern_FP_method' => null,
                            'date_when_FP_method_accepted' =>  null
                        ]);
                    }
                }
            } else {
                if ($isFamilyPlan != null && $isFamilyPlan === 'no') {
                    $hasExistingFamilyPlan = medical_record_cases::where('patient_id', $prenatalRecord->patient->id)->where('type_of_case', 'family-planning')->where('status', '!=', 'Archived')->exists();

                    if (!empty($hasExistingFamilyPlan)) {
                        $existingFamilyPlan = medical_record_cases::where('patient_id', $prenatalRecord->patient->id)->where('type_of_case', 'family-planning')->where('status', '!=', 'Archived')->first();

                        $family_planning_sideA = family_planning_case_records::where('medical_record_case_id', $existingFamilyPlan->id)->where('status', '!=', 'Archived')->first() ?? null;
                        $family_planning_sideB = family_planning_side_b_records::where('medical_record_case_id', $existingFamilyPlan->id)->where('status', '!=', 'Archived')->first() ?? null;
                        $wra_record = wra_masterlists::where('patient_id', $prenatalRecord->patient->id)->first();

                        // make the records archived
                        $existingFamilyPlan->update(['status' => 'Archived']);
                        if ($family_planning_sideA && $family_planning_sideB && $wra_record) {
                            $family_planning_sideA->update([
                                'status' => 'Archived',
                            ]);

                            $family_planning_sideB->update([
                                'status' => 'Archived'
                            ]);
                            $wra_record->update([
                                'plan_to_have_more_children_yes' => null,
                                'plan_to_have_more_children_no' =>  null,
                                'current_FP_methods' => null,
                                'modern_FP' =>  null,
                                'traditional_FP' =>  null,
                                'currently_using_any_FP_method_no' =>  null,
                                'shift_to_modern_method' => null,
                                'wra_with_MFP_unmet_need' => 'yes',
                                'wra_accept_any_modern_FP_method' => null,
                                'selected_modern_FP_method' => null,
                                'date_when_FP_method_accepted' =>  null
                            ]);
                        }


                        return response()->json(['errors' => 'family planning exist'], 200);
                    }
                }
                if ($prenatalRecord->patient->age >= 10) {
                    $wra_masterlist = wra_masterlists::where('patient_id', $prenatalRecord->patient->id)->first();
                    if ($wra_masterlist) {
                        $wra_masterlist->update([
                            'brgy_name' => $address->purok,
                            'house_hold_number' => null,
                            'name_of_wra' => $prenatalRecord->patient->full_name,
                            'address' => $fullAddress,
                            'age' => $prenatalRecord->patient->age ?? null,
                            'date_of_birth' => $prenatalRecord->patient->date_of_birth ?? null,
                        ]);
                    } else {
                        $wra_masterlist = wra_masterlists::create([
                            'medical_record_case_id' => null,
                            'health_worker_id' => $data['handled_by'],
                            'address_id' => $address->id,
                            'patient_id' => $prenatalRecord->patient->id,
                            'brgy_name' => $address->purok,
                            'house_hold_number' => null,
                            'name_of_wra' => $prenatalRecord->patient->full_name,
                            'address' => $fullAddress,
                            'age' => $prenatalRecord->patient->age ?? null,
                            'date_of_birth' => $prenatalRecord->patient->date_of_birth ?? null,
                            'SE_status' => null,
                            'plan_to_have_more_children_yes' => null,
                            'plan_to_have_more_children_no' =>  null,
                            'current_FP_methods' => null,
                            'modern_FP' =>  null,
                            'traditional_FP' =>  null,
                            'currently_using_any_FP_method_no' =>  null,
                            'shift_to_modern_method' => null,
                            'wra_with_MFP_unmet_need' => 'yes',
                            'wra_accept_any_modern_FP_method' => null,
                            'selected_modern_FP_method' => null,
                            'date_when_FP_method_accepted' =>  null
                        ]);
                    }
                }
            }

            // update the wra record
            $wra_record = wra_masterlists::where('patient_id', $prenatalRecord->patient->id)->where('status', 'Active')->first() ?? null;

            if ($wra_record) {
                $wra_record->update([
                    'name_of_wra' => $prenatalRecord->patient->full_name,
                    'age' => $prenatalRecord->patient->age,
                    'date_of_birth' => $prenatalRecord->patient->date_of_birth,
                    'address' => $fullAddress
                ]);
            }


            return response()->json([
                'message' => 'Updating Patient information Successfully',
                'recordVerification' => $message ?? null
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function viewPregnancyPlan($id)
    {
        try {
            $pregnancyRecord = pregnancy_plans::with('donor_name')->findOrFail($id);

            return response()->json([
                'pregnancyPlan' => $pregnancyRecord
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function updateCase(Request $request, $id)
    {
        try {
            // get the case record of the patient
            $caseRecord = prenatal_case_records::findOrFail($id);
            // get the prenatal timeline record using the id of the case record then delete to reset
            $prenatalTimelineRecord = pregnancy_timeline_records::where('prenatal_case_record_id', $id)->delete();
            // case record
            $data = $request->validate([
                'G' => 'sometimes|nullable|numeric',
                'P' => 'sometimes|nullable|numeric',
                'T' => 'sometimes|nullable|numeric',
                'premature' => 'sometimes|nullable|numeric',
                'abortion' => 'sometimes|nullable|numeric',
                'living_children' => 'sometimes|nullable|numeric',
                'preg_year' => 'sometimes|nullable|array',
                'type_of_delivery' => 'sometimes|nullable|array',
                'place_of_delivery' => 'sometimes|nullable|array',
                'birth_attendant' => 'sometimes|nullable|array',
                'compilation' => 'sometimes|nullable|array',
                'outcome' => 'sometimes|nullable|array',
                'LMP' => 'required|date',
                'expected_delivery' => 'required|date',
                'menarche' => 'sometimes|nullable|numeric',
                'tt1' => 'sometimes|nullable|numeric',
                'tt2' => 'sometimes|nullable|numeric',
                'tt3' => 'sometimes|nullable|numeric',
                'tt4' => 'sometimes|nullable|numeric',
                'tt5' => 'sometimes|nullable|numeric',
                'edit_case_blood_pressure' => [
                    'sometimes',
                    'nullable',
                    'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                ],
                'edit_case_temperature' => 'nullable|numeric|between:30,45',
                'edit_case_pulse_rate' => 'nullable|string|max:20',
                'edit_case_respiratory_rate' => 'nullable|integer|min:5|max:60',
                'edit_case_height' => 'nullable|numeric|between:30,300',
                'edit_case_weight' => 'nullable|numeric|between:1,500',
                'edit_case_planning' => 'nullable|string|max:2000',
            ], [
                // Required fields
                'LMP.required' => 'The LMP field is required.',
                'LMP.date' => 'The LMP field must be a valid date.',
                'expected_delivery.required' => 'The expected delivery field is required.',
                'expected_delivery.date' => 'The expected delivery field must be a valid date.',

                // Numeric fields
                'G.numeric' => 'The G field must be a number.',
                'P.numeric' => 'The P field must be a number.',
                'T.numeric' => 'The T field must be a number.',
                'premature.numeric' => 'The premature field must be a number.',
                'abortion.numeric' => 'The abortion field must be a number.',
                'living_children.numeric' => 'The living children field must be a number.',
                'menarche.numeric' => 'The menarche field must be a number.',

                // TT fields
                'tt1.numeric' => 'The TT1 field must be a number.',
                'tt2.numeric' => 'The TT2 field must be a number.',
                'tt3.numeric' => 'The TT3 field must be a number.',
                'tt4.numeric' => 'The TT4 field must be a number.',
                'tt5.numeric' => 'The TT5 field must be a number.',

                // Array fields
                'preg_year.array' => 'The pregnancy year field must be an array.',
                'type_of_delivery.array' => 'The type of delivery field must be an array.',
                'place_of_delivery.array' => 'The place of delivery field must be an array.',
                'birth_attendant.array' => 'The birth attendant field must be an array.',
                'compilation.array' => 'The compilation field must be an array.',
                'outcome.array' => 'The outcome field must be an array.',

                // Vital signs and measurements
                'edit_case_blood_pressure.regex' => 'The blood pressure format is invalid. Expected format: systolic/diastolic (e.g., 120/80).',
                'edit_case_temperature.numeric' => 'The temperature field must be a number.',
                'edit_case_temperature.between' => 'The temperature must be between 30 and 45 degrees Celsius.',
                'edit_case_pulse_rate.string' => 'The pulse rate field must be a string.',
                'edit_case_pulse_rate.max' => 'The pulse rate field must not exceed 20 characters.',
                'edit_case_respiratory_rate.integer' => 'The respiratory rate field must be an integer.',
                'edit_case_respiratory_rate.min' => 'The respiratory rate must be at least 5 breaths per minute.',
                'edit_case_respiratory_rate.max' => 'The respiratory rate must not exceed 60 breaths per minute.',
                'edit_case_height.numeric' => 'The height field must be a number.',
                'edit_case_height.between' => 'The height must be between 30 and 300 cm.',
                'edit_case_weight.numeric' => 'The weight field must be a number.',
                'edit_case_weight.between' => 'The weight must be between 1 and 500 kg.',
                'edit_case_planning.string' => 'The planning field must be a string.',
                'edit_case_planning.max' => 'The planning field must not exceed 2000 characters.',
            ]);

            // Assessment validation
            $assessment = $request->validate([
                'spotting' => 'sometimes|nullable|string',
                'edema' => 'sometimes|nullable|string',
                'severe_headache' => 'sometimes|nullable|string',
                'blurring_of_vission' => 'sometimes|nullable|string',
                'watery_discharge' => 'sometimes|nullable|string',
                'severe_vomiting' => 'sometimes|nullable|string',
                'hx_smoking' => 'sometimes|nullable|string',
                'alcohol_drinker' => 'sometimes|nullable|string',
                'drug_intake' => 'sometimes|nullable|string',
            ], [
                'spotting.string' => 'The spotting field must be a valid text value.',
                'edema.string' => 'The edema field must be a valid text value.',
                'severe_headache.string' => 'The severe headache field must be a valid text value.',
                'blurring_of_vission.string' => 'The blurring of vision field must be a valid text value.',
                'watery_discharge.string' => 'The watery discharge field must be a valid text value.',
                'severe_vomiting.string' => 'The severe vomiting field must be a valid text value.',
                'hx_smoking.string' => 'The smoking history field must be a valid text value.',
                'alcohol_drinker.string' => 'The alcohol drinker field must be a valid text value.',
                'drug_intake.string' => 'The drug intake field must be a valid text value.',
            ]);

            // if it passess the validation,then:
            // update the values
            $caseRecord->update([
                'G' => $data['G'] ?? null,
                'P' => $data['P'] ?? null,
                'T' => $data['T'] ?? null,
                'premature' => $data['premature'] ?? null,
                'abortion' => $data['abortion'] ?? null,
                'living_children' => $data['living_children'] ?? null,
                'LMP' => $data['LMP'] ?? $caseRecord->LMP,
                'expected_delivery' => $data['expected_delivery'] ?? $caseRecord->expected_delivery,
                'menarche' => $data['menarche'] ?? null,
                'tetanus_toxoid_1' => $data['tt1'] ?? null,
                'tetanus_toxoid_2' => $data['tt2'] ?? null,
                'tetanus_toxoid_3' => $data['tt3'] ?? null,
                'tetanus_toxoid_4' => $data['tt4'] ?? null,
                'tetanus_toxoid_5' => $data['tt5'] ?? null,
                'decision' => $data['nurse_decision'] ?? null,
                'blood_pressure' => $data['edit_case_blood_pressure'] ?? null,
                'pulse_rate'  => $data['edit_case_pulse_rate'] ?? null,
                'temperature' => $data['edit_case_temperature'] ?? null,
                'respiratory_rate' => $data['edit_case_respiratory_rate'] ?? null,
                'height' => $data['edit_case_height'] ?? null,
                'weight' => $data['edit_case_weight'] ?? null,
                'planning' => $data['edit_case_planning'] ?? null,

            ]);

            // after resetting the record of pregnancy timeline add new record
            $pregnancyYearArray = $data['preg_year'] ?? null;
            if ($pregnancyYearArray  != null) {
                foreach ($pregnancyYearArray  as $index => $year) {
                    pregnancy_timeline_records::create([
                        'prenatal_case_record_id' => $id,
                        'year' => $year ?? null,
                        'type_of_delivery' => $data['type_of_delivery'][$index] ?? null,
                        'place_of_delivery' => $data['place_of_delivery'][$index] ?? null,
                        'birth_attendant' => $data['birth_attendant'][$index] ?? null,
                        'compilation' => $data['compilation'][$index] ?? null,
                        'outcome' => $data['outcome'][$index] ?? null,
                    ]);
                }
            }


            // update the assessment
            $assessmentRecord = prenatal_assessments::where('prenatal_case_record_id', $id)->firstOrFail();

            $assessmentRecord->update([
                'spotting' => $assessment['spotting'] ?? 'no',
                'edema' => $assessment['edema'] ?? 'no',
                'severe_headache' => $assessment['severe_headache'] ?? 'no',
                'blumming_vission' => $assessment['blurring_of_vission'] ?? 'no',
                'water_discharge' => $assessment['watery_discharge'] ??  'no',
                'severe_vomitting' => $assessment['severe_vomiting'] ??  'no',
                'hx_smoking' => $assessment['hx_smoking'] ??  'no',
                'alchohol_drinker' => $assessment['alcohol_drinker'] ?? 'no',
                'drug_intake' => $assessment['drug_intake'] ?? 'no'
            ]);

            // bind the pregnancy_history_questions
            $archiveRecord = prenatal_case_records::with('pregnancy_history_questions')
                ->where("status", 'Archived')
                ->whereHas('pregnancy_history_questions')
                ->first();

            if ($archiveRecord) {
                // Get the first pregnancy_history_question from the collection
                $pregnancyHistoryQuestion = $archiveRecord->pregnancy_history_questions->first();

                if ($pregnancyHistoryQuestion) {
                    $pregnancyHistoryQuestion->update([
                        'prenatal_case_record_id' => $caseRecord->id
                    ]);
                }
            }



            return response()->json(['message' => 'Patient Info is Updated'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function updatePregnancyPlan(Request $request, $id)
    {
        try {
            $pregnancyPlanRecord = pregnancy_plans::findOrFail($id);

            $data = $request->validate([
                'midwife_name' => 'sometimes|nullable|string',
                'place_of_pregnancy' => 'sometimes|nullable|string',
                'authorized_by_philhealth' => 'sometimes|nullable|string',
                'cost_of_pregnancy' => 'sometimes|nullable|numeric',
                'payment_method' => 'sometimes|nullable|string',
                'transportation_mode' => 'sometimes|nullable|string',
                'accompany_person_to_hospital' => 'sometimes|nullable|string',
                'accompany_through_pregnancy' => 'sometimes|nullable|string',
                'care_person' => 'sometimes|nullable|string',
                'emergency_person_name' => 'sometimes|nullable|string',
                'emergency_person_residency' => 'sometimes|nullable|string',
                'emergency_person_contact_number' => 'sometimes|nullable|string',
                'edit_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'edit_signature_data' => 'sometimes|nullable|string',
                'donor_names' => 'sometimes|nullable|array'
            ]);

            // update
            $signaturePath = $pregnancyPlanRecord->signature; // Keep old signature by default

            // Check if new signature provided (drawn)
            if ($request->filled('edit_signature_data')) {
                // Delete old file if exists
                if ($pregnancyPlanRecord->signature) {
                    Storage::delete('public/' . $pregnancyPlanRecord->signature);
                }
                $signaturePath = $this->saveCanvasSignature($request->edit_signature_data);
            }
            // Check if new signature provided (uploaded)
            else if ($request->hasFile('edit_signature_image')) {
                // Delete old file if exists
                if ($pregnancyPlanRecord->signature) {
                    Storage::delete('public/' . $pregnancyPlanRecord->signature);
                }
                $signaturePath = $this->compressAndSaveSignature($request->file('edit_signature_image'));
            }


            $pregnancyPlanRecord->update([
                'midwife_name' => $data['midwife_name'] ?? $pregnancyPlanRecord->midwife_name,
                'place_of_pregnancy' => $data['place_of_pregnancy'] ?? null,
                'authorized_by_philhealth' => $data['authorized_by_philhealth'] ?? null,
                'cost_of_pregnancy' => $data['cost_of_pregnancy'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'transportation_mode' => $data['transportation_mode'] ?? null,
                'accompany_person_to_hospital' => $data['accompany_person_to_hospital'] ?? null,
                'accompany_through_pregnancy' =>  $data['accompany_through_pregnancy'] ?? null,
                'care_person' => $data['care_person'] ?? null,
                'emergency_person_name' => $data['emergency_person_name'] ?? null,
                'emergency_person_residency' => $data['emergency_person_residency'] ?? null,
                'emergency_person_contact_number' => $data['emergency_person_contact_number'] ?? null,
                'signature' => $signaturePath ?? null,
                'type_of_record' => 'Pregnancy Plan Record'
            ]);
            // delete all the donor name as update logic
            $donorNamesRecord = donor_names::where('pregnancy_plan_id', $pregnancyPlanRecord->id)->delete();

            if (isset($data['donor_names']) && !empty($data['donor_names'])) {
                foreach ($data['donor_names'] as $name) {
                    if (!empty(trim($name))) { // Also check if name is not empty
                        donor_names::create([
                            'pregnancy_plan_id' => $id,
                            'donor_name' => trim($name)
                        ]);
                    }
                }
            };

            return response()->json(['message' => 'Updating Patient Pregnancy Plan Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }
    public function viewPrenatalDetail($id)
    {
        try {
            $prenatalRecord = medical_record_cases::with([
                'patient',
                'prenatal_case_record.pregnancy_history_questions',
                'prenatal_medical_record'
            ])->where('id', $id)->firstOrFail();

            $prenatalCaseRecord = prenatal_case_records::with('pregnancy_history_questions')
                ->where('medical_record_case_id', $prenatalRecord->id)
                ->where("status", '!=', 'Archived')
                ->firstOrFail();

            $healthWorker = staff::where('user_id', $prenatalCaseRecord->health_worker_id)
                ->firstOrFail();

            return response()->json([
                'prenatalRecord'      => $prenatalRecord,
                'prenatalCaseRecord'  => $prenatalCaseRecord,
                'healthWorker'        => $healthWorker,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Record not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadPregnancyCheckup(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    // Required fields
                    'check_up_full_name'       => 'required|string|max:255',

                    // Optional fields
                    'health_worker_id'         => 'nullable|exists:staff,user_id',
                    'check_up_time'            => 'nullable|date_format:H:i',
                    'check_up_blood_pressure'  => [
                        'sometimes',
                        'nullable',
                        'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                    ],
                    'check_up_temperature'     => 'nullable|numeric|min:20|max:100',
                    'check_up_pulse_rate'      => 'nullable|integer|min:30|max:250',
                    'check_up_respiratory_rate' => 'nullable|integer|min:5|max:80',
                    'check_up_height'          => 'nullable|numeric|between:30,250',
                    'check_up_weight'          => 'nullable|numeric|min:1|between:1,300',

                    // Symptom questions (all optional but strings)
                    'abdomen_question'                 => 'nullable|string|max:255',
                    'abdomen_question_remarks'         => 'nullable|string|max:500',
                    'vaginal_question'                 => 'nullable|string|max:255',
                    'vaginal_question_remarks'         => 'nullable|string|max:500',
                    'headache_question'                => 'nullable|string|max:255',
                    'headache_question_remarks'        => 'nullable|string|max:500',
                    'blurry_vission_question'          => 'nullable|string|max:255',
                    'blurry_vission_question_remarks'  => 'nullable|string|max:500',
                    'urination_question'               => 'nullable|string|max:255',
                    'urination_question_remarks'       => 'nullable|string|max:500',
                    'baby_move_question'               => 'nullable|string|max:255',
                    'baby_move_question_remarks'       => 'nullable|string|max:500',
                    'decreased_baby_movement'          => 'nullable|string|max:255',
                    'decreased_baby_movement_remarks'  => 'nullable|string|max:500',
                    'other_symptoms_question'          => 'nullable|string|max:255',
                    'other_symptoms_question_remarks'  => 'nullable|string|max:500',

                    // Final remarks
                    'overall_remarks'           => 'nullable|string|max:1000',
                    'date_of_comeback' => 'required|date'
                ],
                [], // this is empty because we didn't customize the error message for each field we just change the name 
                [
                    // Replace attribute names for cleaner error messages
                    'check_up_time'             => 'Time',
                    'check_up_blood_pressure'   => 'Blood Pressure',
                    'check_up_temperature'      => 'Temperature',
                    'check_up_pulse_rate'       => 'Pulse Rate',
                    'check_up_respiratory_rate' => 'Respiratory Rate',
                    'check_up_height'           => 'Height',
                    'check_up_weight'           => 'Weight',

                    'abdomen_question'                 => 'Abdomen Question',
                    'abdomen_question_remarks'         => 'Abdomen Remarks',
                    'vaginal_question'                 => 'Vaginal Question',
                    'vaginal_question_remarks'         => 'Vaginal Remarks',
                    'headache_question'                => 'Headache Question',
                    'headache_question_remarks'        => 'Headache Remarks',
                    'blurry_vission_question'          => 'Blurry Vision Question',
                    'blurry_vission_question_remarks'  => 'Blurry Vision Remarks',
                    'urination_question'               => 'Urination Question',
                    'urination_question_remarks'       => 'Urination Remarks',
                    'baby_move_question'               => 'Baby Movement Question',
                    'baby_move_question_remarks'       => 'Baby Movement Remarks',
                    'decreased_baby_movement'          => 'Decreased Baby Movement',
                    'decreased_baby_movement_remarks'  => 'Decreased Baby Movement Remarks',
                    'other_symptoms_question'          => 'Other Symptoms Question',
                    'other_symptoms_question_remarks'  => 'Other Symptoms Remarks',

                    'overall_remarks'           => 'Overall Remarks',
                ]
            );

            // data insertion
            $prenatalCheckup = pregnancy_checkups::create([
                'medical_record_case_id'    => $id,
                'patient_name'              => $request->check_up_full_name,
                'health_worker_id'          => $request->health_worker_id,
                'check_up_time'             => $request->check_up_time,
                'check_up_blood_pressure'   => $request->check_up_blood_pressure,
                'check_up_temperature'      => $request->check_up_temperature,
                'check_up_pulse_rate'       => $request->check_up_pulse_rate,
                'check_up_respiratory_rate' => $request->check_up_respiratory_rate,
                'check_up_height'           => $request->check_up_height,
                'check_up_weight'           => $request->check_up_weight,

                'abdomen_question'                 => $request->abdomen_question,
                'abdomen_question_remarks'         => $request->abdomen_question_remarks,
                'vaginal_question'                 => $request->vaginal_question,
                'vaginal_question_remarks'         => $request->vaginal_question_remarks,
                'headache_question'                => $request->headache_question,
                'headache_question_remarks'        => $request->headache_question_remarks,
                'blurry_vission_question'          => $request->blurry_vission_question,
                'blurry_vission_question_remarks'  => $request->blurry_vission_question_remarks,
                'urination_question'               => $request->urination_question,
                'urination_question_remarks'       => $request->urination_question_remarks,
                'baby_move_question'               => $request->baby_move_question,
                'baby_move_question_remarks'       => $request->baby_move_question_remarks,
                'decreased_baby_movement'          => $request->decreased_baby_movement,
                'decreased_baby_movement_remarks'  => $request->decreased_baby_movement_remarks,
                'other_symptoms_question'          => $request->other_symptoms_question,
                'other_symptoms_question_remarks'  => $request->other_symptoms_question_remarks,

                'overall_remarks' => $request->overall_remarks,
                'status' => 'Done',
                'date_of_comeback' => $request->date_of_comeback
            ]);
            return response()->json(['message' => 'Prenatal Check-up info is added successfully'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 404);
        };
    }

    public function viewCheckUpInfo($id)
    {
        try {
            $pregnancy_checkup = pregnancy_checkups::findOrFail($id);
            $healthWorker = staff::where('user_id', $pregnancy_checkup->health_worker_id)->firstOrFail();
            return response()->json([
                'pregnancy_checkup_info' => $pregnancy_checkup,
                'healthWorker' => $healthWorker
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
    public function updatePregnancyCheckUp(Request $request, $id)
    {
        try {
            $checkUp = pregnancy_checkups::findOrFail($id);
            $request->validate(
                [
                    // Required fields
                    'edit_check_up_full_name'       => 'required|string|max:255',

                    // Optional fields
                    'edit_health_worker_id'         => 'nullable|exists:staff,user_id',
                    'edit_check_up_time'            => 'nullable|date_format:H:i:s',
                    'edit_check_up_blood_pressure'  => [
                        'sometimes',
                        'nullable',
                        'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                    ],
                    'edit_check_up_temperature'     => 'nullable|numeric|min:20|max:45',
                    'edit_check_up_pulse_rate'      => 'nullable|integer|min:30|max:250',
                    'edit_check_up_respiratory_rate' => 'nullable|integer|min:5|max:80',
                    'edit_check_up_height'          => 'nullable|numeric|between:30,250',
                    'edit_check_up_weight'          => 'nullable|numeric|between:1,300',

                    // Symptom questions (all optional but strings)
                    'edit_abdomen_question'                 => 'nullable|string|max:255',
                    'edit_abdomen_question_remarks'         => 'nullable|string|max:500',
                    'edit_vaginal_question'                 => 'nullable|string|max:255',
                    'edit_vaginal_question_remarks'         => 'nullable|string|max:500',
                    'edit_headache_question'                => 'nullable|string|max:255',
                    'edit_headache_question_remarks'        => 'nullable|string|max:500',
                    'edit_swelling_question'                => 'nullable|string|max:255',
                    'edit_swelling_question_remarks'        => 'nullable|string|max:500',
                    'edit_blurry_vission_question'          => 'nullable|string|max:255',
                    'edit_blurry_vission_question_remarks'  => 'nullable|string|max:500',
                    'edit_urination_question'               => 'nullable|string|max:255',
                    'edit_urination_question_remarks'       => 'nullable|string|max:500',
                    'edit_baby_move_question'               => 'nullable|string|max:255',
                    'edit_baby_move_question_remarks'       => 'nullable|string|max:500',
                    'edit_decreased_baby_movement'          => 'nullable|string|max:255',
                    'edit_decreased_baby_movement_remarks'  => 'nullable|string|max:500',
                    'edit_other_symptoms_question'          => 'nullable|string|max:255',
                    'edit_other_symptoms_question_remarks'  => 'nullable|string|max:500',

                    // Final remarks
                    'edit_overall_remarks' => 'nullable|string|max:1000',
                    'edit_date_of_comeback' => 'required|date'
                ],
                [], // this is empty because we didn't customize the error message for each field we just change the name 
                [
                    // Replace attribute names for cleaner error messages
                    'edit_check_up_time'             => 'Time',
                    'edit_check_up_blood_pressure'   => 'Blood Pressure',
                    'edit_check_up_temperature'      => 'Temperature',
                    'edit_check_up_pulse_rate'       => 'Pulse Rate',
                    'edit_check_up_respiratory_rate' => 'Respiratory Rate',
                    'edit_check_up_height'           => 'Height',
                    'edit_check_up_weight'           => 'Weight',

                    'edit_abdomen_question'                 => 'Abdomen Question',
                    'edit_abdomen_question_remarks'         => 'Abdomen Remarks',
                    'edit_vaginal_question'                 => 'Vaginal Question',
                    'edit_vaginal_question_remarks'         => 'Vaginal Remarks',
                    'edit_headache_question'                => 'Headache Question',
                    'edit_headache_question_remarks'        => 'Headache Remarks',
                    'edit_blurry_vission_question'          => 'Blurry Vision Question',
                    'edit_blurry_vission_question_remarks'  => 'Blurry Vision Remarks',
                    'edit_urination_question'               => 'Urination Question',
                    'edit_urination_question_remarks'       => 'Urination Remarks',
                    'edit_baby_move_question'               => 'Baby Movement Question',
                    'edit_baby_move_question_remarks'       => 'Baby Movement Remarks',
                    'edit_decreased_baby_movement'          => 'Decreased Baby Movement',
                    'edit_decreased_baby_movement_remarks'  => 'Decreased Baby Movement Remarks',
                    'edit_other_symptoms_question'          => 'Other Symptoms Question',
                    'edit_other_symptoms_question_remarks'  => 'Other Symptoms Remarks',

                    'edit_overall_remarks'           => 'Overall Remarks',
                    'edit_date_of_comeback' => 'Date of Comeback'
                ]
            );

            // data insertion
            $checkUp->update([
                'patient_name'              => $request->edit_check_up_full_name ?? $checkUp->patient_name,
                'health_worker_id'          => $request->edit_health_worker_id ?? $checkUp->health_worker_id,
                'check_up_time'             => $request->edit_check_up_time ?? $checkUp->check_up_time,
                'check_up_blood_pressure'   => $request->edit_check_up_blood_pressure ?? $checkUp->check_up_blood_pressure,
                'check_up_temperature'      => $request->edit_check_up_temperature ?? $checkUp->check_up_temperature,
                'check_up_pulse_rate'       => $request->edit_check_up_pulse_rate ?? $checkUp->check_up_pulse_rate,
                'check_up_respiratory_rate' => $request->edit_check_up_respiratory_rate ?? $checkUp->check_up_respiratory_rate,
                'check_up_height'           => $request->edit_check_up_height ?? $checkUp->check_up_height,
                'check_up_weight'               => $request->edit_check_up_weight ?? $checkUp->check_up_weight,

                'abdomen_question'              => $request->edit_abdomen_question ?? $checkUp->abdomen_question,
                'abdomen_question_remarks'      => $request->edit_abdomen_question_remarks ?? $checkUp->abdomen_question_remarks,

                'vaginal_question'              => $request->edit_vaginal_question ?? $checkUp->vaginal_question,
                'vaginal_question_remarks'      => $request->edit_vaginal_question_remarks ?? $checkUp->vaginal_question_remarks,

                'headache_question'             => $request->edit_headache_question ?? $checkUp->headache_question,
                'headache_question_remarks'     => $request->edit_headache_question_remarks ?? $checkUp->headache_question_remarks,

                'swelling_question'             => $request->edit_swelling_question ?? $checkUp->swelling_question,
                'swelling_question_remarks'     => $request->edit_swelling_question_remarks ?? $checkUp->swelling_question_remarks,

                'blurry_vission_question'       => $request->edit_blurry_vission_question ?? $checkUp->blurry_vission_question,
                'blurry_vission_question_remarks' => $request->edit_blurry_vission_question_remarks ?? $checkUp->blurry_vission_question_remarks,

                'urination_question'            => $request->edit_urination_question ?? $checkUp->urination_question,
                'urination_question_remarks'    => $request->edit_urination_question_remarks ?? $checkUp->urination_question_remarks,

                'baby_move_question'            => $request->edit_baby_move_question ?? $checkUp->baby_move_question,
                'baby_move_question_remarks'    => $request->edit_baby_move_question_remarks ?? $checkUp->baby_move_question_remarks,

                'decreased_baby_movement'       => $request->edit_decreased_baby_movement ?? $checkUp->decreased_baby_movement,
                'decreased_baby_movement_remarks' => $request->edit_decreased_baby_movement_remarks ?? $checkUp->decreased_baby_movement_remarks,

                'other_symptoms_question'       => $request->edit_other_symptoms_question ?? $checkUp->other_symptoms_question,
                'other_symptoms_question_remarks' => $request->edit_other_symptoms_question_remarks ?? $checkUp->other_symptoms_question_remarks,

                'overall_remarks'               => $request->edit_overall_remarks ?? $checkUp->overall_remarks,
                'status' => 'Done',
                'date_of_comeback' => $request->edit_date_of_comeback
            ]);
            return response()->json(['message' => 'Prenatal Check-up info is updated successfully'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 404);
        };
    }

    public function archive($id)
    {
        try {
            $checkUpRecord = pregnancy_checkups::findOrFail($id);

            // update the status
            $checkUpRecord->update([
                'status' => 'Archived'
            ]);
            return response()->json([
                'message' => 'Prenatal Check-up Record is successfully deleted.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function addCase(Request $request)
    {
        try {

            $data = $request->validate([
                'add_prenatal_case_medical_record_case_id' => 'required',
                'add_prenatal_case_health_worker_id' => 'required',
                'add_prenatal_case_patient_name' => 'required',
                'add_G' => 'sometimes|nullable|numeric',
                'add_P' => 'sometimes|nullable|numeric',
                'add_T' => 'sometimes|nullable|numeric',
                'add_premature' => 'sometimes|nullable|numeric',
                'add_abortion' => 'sometimes|nullable|numeric',
                'add_living_children' => 'sometimes|nullable|numeric',
                'add_preg_year' => 'sometimes|nullable|array',
                'add_type_of_delivery' => 'sometimes|nullable|array',
                'add_place_of_delivery' => 'sometimes|nullable|array',
                'add_birth_attendant' => 'sometimes|nullable|array',
                'add_compilation' => 'sometimes|nullable|array',
                'add_outcome' => 'sometimes|nullable|array',
                'add_LMP' => 'required|date',
                'add_expected_delivery' => 'required|date',
                'add_menarche' => 'sometimes|nullable|numeric',
                'add_tt1' => 'sometimes|nullable|numeric',
                'add_tt2' => 'sometimes|nullable|numeric',
                'add_tt3' => 'sometimes|nullable|numeric',
                'add_tt4' => 'sometimes|nullable|numeric',
                'add_tt5' => 'sometimes|nullable|numeric',
                'add_case_temperature' => 'nullable|numeric|between:30,45',
                'add_case_pulse_rate' => 'nullable|string|max:20',
                'add_case_respiratory_rate' => 'nullable|integer|min:5|max:60',
                'add_case_height' => 'nullable|numeric|between:30,300',
                'add_case_weight' => 'nullable|numeric|between:1,500',
                'add_case_planning' => 'nullable|string|max:2000',
            ], [
                // Required fields
                'add_prenatal_case_medical_record_case_id.required' => 'The prenatal case medical record case id field is required.',
                'add_prenatal_case_health_worker_id.required' => 'The prenatal case health worker id field is required.',
                'add_prenatal_case_patient_name.required' => 'The prenatal case patient name field is required.',
                'add_LMP.required' => 'The LMP field is required.',
                'add_LMP.date' => 'The LMP field must be a valid date.',
                'add_expected_delivery.required' => 'The expected delivery field is required.',
                'add_expected_delivery.date' => 'The expected delivery field must be a valid date.',

                // Numeric fields
                'add_G.numeric' => 'The G field must be a number.',
                'add_P.numeric' => 'The P field must be a number.',
                'add_T.numeric' => 'The T field must be a number.',
                'add_premature.numeric' => 'The premature field must be a number.',
                'add_abortion.numeric' => 'The abortion field must be a number.',
                'add_living_children.numeric' => 'The living children field must be a number.',
                'add_menarche.numeric' => 'The menarche field must be a number.',

                // TT fields
                'add_tt1.numeric' => 'The tt1 field must be a number.',
                'add_tt2.numeric' => 'The tt2 field must be a number.',
                'add_tt3.numeric' => 'The tt3 field must be a number.',
                'add_tt4.numeric' => 'The tt4 field must be a number.',
                'add_tt5.numeric' => 'The tt5 field must be a number.',

                // Array fields
                'add_preg_year.array' => 'The pregnancy year field must be an array.',
                'add_type_of_delivery.array' => 'The type of delivery field must be an array.',
                'add_place_of_delivery.array' => 'The place of delivery field must be an array.',
                'add_birth_attendant.array' => 'The birth attendant field must be an array.',
                'add_compilation.array' => 'The compilation field must be an array.',
                'add_outcome.array' => 'The outcome field must be an array.',

                // Vital signs and measurements
                'add_case_temperature.numeric' => 'The temperature field must be a number.',
                'add_case_temperature.between' => 'The temperature must be between 30 and 45 degrees.',
                'add_case_pulse_rate.string' => 'The pulse rate field must be a string.',
                'add_case_pulse_rate.max' => 'The pulse rate field must not exceed 20 characters.',
                'add_case_respiratory_rate.integer' => 'The respiratory rate field must be an integer.',
                'add_case_respiratory_rate.min' => 'The respiratory rate must be at least 5.',
                'add_case_respiratory_rate.max' => 'The respiratory rate must not exceed 60.',
                'add_case_height.numeric' => 'The height field must be a number.',
                'add_case_height.between' => 'The height must be between 30 and 300 cm.',
                'add_case_weight.numeric' => 'The weight field must be a number.',
                'add_case_weight.between' => 'The weight must be between 1 and 500 kg.',
                'add_case_planning.string' => 'The planning field must be a string.',
                'add_case_planning.max' => 'The planning field must not exceed 2000 characters.',
            ]);

            // assessment validation
            $assessment = $request->validate([
                'add_spotting' => 'sometimes|nullable|string',
                'add_edema' => 'sometimes|nullable|string',
                'add_severe_headache' => 'sometimes|nullable|string',
                'add_blurring_of_vission' => 'sometimes|nullable|string',
                'add_watery_discharge' => 'sometimes|nullable|string',
                'add_severe_vomiting' => 'sometimes|nullable|string',
                'add_hx_smoking' => 'sometimes|nullable|string',
                'add_alcohol_drinker' => 'sometimes|nullable|string',
                'add_drug_intake' => 'sometimes|nullable|string'
            ], [
                'add_spotting.string' => 'The spotting field must be a string.',
                'add_edema.string' => 'The edema field must be a string.',
                'add_severe_headache.string' => 'The severe headache field must be a string.',
                'add_blurring_of_vission.string' => 'The blurring of vission field must be a string.',
                'add_watery_discharge.string' => 'The watery discharge field must be a string.',
                'add_severe_vomiting.string' => 'The severe vomiting field must be a string.',
                'add_hx_smoking.string' => 'The hx smoking field must be a string.',
                'add_alcohol_drinker.string' => 'The alcohol drinker field must be a string.',
                'add_drug_intake.string' => 'The drug intake field must be a string.',
            ]);
            // check first if there is existing record

            $existingCaseRecord = prenatal_case_records::where("medical_record_case_id", $data['add_prenatal_case_medical_record_case_id'])->where("status", '!=', 'Archived')->first();

            if (!empty($existingCaseRecord)) {
                return response()->json([
                    'errors' => 'There is existing record.'
                ], 422);
            }

            // update the values
            $caseRecord = prenatal_case_records::create([
                'medical_record_case_id' => $data['add_prenatal_case_medical_record_case_id'],
                'health_worker_id' => $data['add_prenatal_case_health_worker_id'],
                'patient_name' => $data['add_prenatal_case_patient_name'],
                'G' => $data['add_G'] ?? null,
                'P' => $data['add_P'] ?? null,
                'T' => $data['add_T'] ?? null,
                'premature' => $data['add_premature'] ?? null,
                'abortion' => $data['add_abortion'] ?? null,
                'living_children' => $data['add_living_children'] ?? null,
                'LMP' => $data['add_LMP'] ?? null,
                'expected_delivery' => $data['add_expected_delivery'] ?? null,
                'menarche' => $data['add_menarche'] ?? null,
                'tetanus_toxoid_1' => $data['add_tt1'] ?? null,
                'tetanus_toxoid_2' => $data['add_tt2'] ?? null,
                'tetanus_toxoid_3' => $data['add_tt3'] ?? null,
                'tetanus_toxoid_4' => $data['add_tt4'] ?? null,
                'tetanus_toxoid_5' => $data['add_tt5'] ?? null,
                'blood_pressure' => $data['add_case_blood_pressure'] ?? null,
                'pulse_rate'  => $data['add_case_pulse_rate'] ?? null,
                'temperature' => $data['add_case_temperature'] ?? null,
                'respiratory_rate' => $data['add_case_respiratory_rate'] ?? null,
                'height' => $data['add_case_height'] ?? null,
                'weight' => $data['add_case_weight'] ?? null,
                'planning' => $data['add_case_planning'] ?? null,
                'status' => 'Active',
                'type_of_record' => 'Case Record'
            ]);

            // after resetting the record of pregnancy timeline add new record
            $id = $caseRecord->id;
            $pregnancyYearArray = $data['add_preg_year'] ?? null;
            if ($pregnancyYearArray  != null) {
                foreach ($pregnancyYearArray  as $index => $year) {
                    pregnancy_timeline_records::create([
                        'prenatal_case_record_id' => $id,
                        'year' => $year ?? null,
                        'type_of_delivery' => $data['add_type_of_delivery'][$index] ?? null,
                        'place_of_delivery' => $data['add_place_of_delivery'][$index] ?? null,
                        'birth_attendant' => $data['add_birth_attendant'][$index] ?? null,
                        'compilation' => $data['add_compilation'][$index] ?? null,
                        'outcome' => $data['add_outcome'][$index] ?? null,
                    ]);
                }
            }




            $assessmentRecord = prenatal_assessments::create([
                'prenatal_case_record_id' => $id,
                'spotting' => $assessment['spotting'] ?? 'no',
                'edema' => $assessment['edema'] ?? 'no',
                'severe_headache' => $assessment['severe_headache'] ?? 'no',
                'blumming_vission' => $assessment['blurring_of_vission'] ?? 'no',
                'water_discharge' => $assessment['watery_discharge'] ??  'no',
                'severe_vomitting' => $assessment['severe_vomiting'] ??  'no',
                'hx_smoking' => $assessment['hx_smoking'] ??  'no',
                'alchohol_drinker' => $assessment['alcohol_drinker'] ?? 'no',
                'drug_intake' => $assessment['drug_intake'] ?? 'no'
            ]);

            // bind the pregnancy_history_questions
            $archiveRecord = prenatal_case_records::with('pregnancy_history_questions')
                ->where("status", 'Archived')
                ->whereHas('pregnancy_history_questions')
                ->first();

            if ($archiveRecord) {
                // Get the first pregnancy_history_question from the collection
                $pregnancyHistoryQuestion = $archiveRecord->pregnancy_history_questions->first();

                if ($pregnancyHistoryQuestion) {
                    $pregnancyHistoryQuestion->update([
                        'prenatal_case_record_id' => $caseRecord->id
                    ]);
                }
            }



            return response()->json(['message' => 'Patient Info is Uploaded'], 200);
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

    public function addPregnancyPlan(Request $request, $medicalRecordCaseId)
    {
        try {

            // check first if there is existing active record
            $existingPregnancyPlan = pregnancy_plans::where("medical_record_case_id", $medicalRecordCaseId)->where("status", '!=', 'Archived')->first();
            if (!empty($existingPregnancyPlan)) {
                return response()->json([
                    'errors' => 'Unable to add. A pregnancy plan already exists for this patient.'
                ], 422);
            }
            $data = $request->validate([
                'add_pregnancy_plan_patient_name' => 'required',
                'add_midwife_name' => 'sometimes|nullable|string',
                'add_place_of_pregnancy' => 'sometimes|nullable|string',
                'add_authorized_by_philhealth' => 'sometimes|nullable|string',
                'add_cost_of_pregnancy' => 'sometimes|nullable|numeric',
                'add_payment_method' => 'sometimes|nullable|string',
                'add_transportation_mode' => 'sometimes|nullable|string',
                'add_accompany_person_to_hospital' => 'sometimes|nullable|string',
                'add_accompany_through_pregnancy' => 'sometimes|nullable|string',
                'add_care_person' => 'sometimes|nullable|string',
                'add_emergency_person_name' => 'sometimes|nullable|string',
                'add_emergency_person_residency' => 'sometimes|nullable|string',
                'add_emergency_person_contact_number' => 'sometimes|nullable|string',
                'add_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_signature_data' => 'sometimes|nullable|string',
                'add_donor_names' => 'sometimes|nullable|array'
            ]);
            $signaturePath = null;
            // dd([
            //     'hasFile' => $request->hasFile('add_signature_image'),
            //     'hasDraw' => $request->filled('add_signature_data'),
            //     'fileValue' => $request->file('add_signature_image'),
            //     'drawValue' => $request->add_signature_data ? substr($request->add_signature_data, 0, 50) . '...' : null,
            //     'signaturePath' => $signaturePath
            // ]);

            // pregnancy plan


            // If user uploaded an image file
            if ($request->hasFile('add_signature_image')) {
                $signaturePath = $this->compressAndSaveSignature($request->file('add_signature_image'));
            }
            // If user drew a signature
            else if ($request->filled('add_signature_data')) {
                $signaturePath = $this->saveCanvasSignature($request->add_signature_data);
            }

            $pregnancyPlanRecord = pregnancy_plans::create([
                'medical_record_case_id' => $medicalRecordCaseId,
                'patient_name' => $data['add_pregnancy_plan_patient_name'],
                'midwife_name' => $data['add_midwife_name'] ?? null,
                'place_of_pregnancy' => $data['add_place_of_pregnancy'] ?? null,
                'authorized_by_philhealth' => $data['add_authorized_by_philhealth'] ?? null,
                'cost_of_pregnancy' => $data['add_cost_of_pregnancy'] ?? null,
                'payment_method' => $data['add_payment_method'] ?? null,
                'transportation_mode' => $data['add_transportation_mode'] ?? null,
                'accompany_person_to_hospital' => $data['add_accompany_person_to_hospital'] ?? null,
                'accompany_through_pregnancy' =>  $data['add_accompany_through_pregnancy'] ?? null,
                'care_person' => $data['add_care_person'] ?? null,
                'emergency_person_name' => $data['add_emergency_person_name'] ?? null,
                'emergency_person_residency' => $data['add_emergency_person_residency'] ?? null,
                'emergency_person_contact_number' => $data['add_emergency_person_contact_number'] ?? null,
                'signature' => $signaturePath ?? null,
                'type_of_record' => 'Pregnancy Plan Record'
            ]);
            // delete all the donor name as update logic

            $id = $pregnancyPlanRecord->id;

            if (isset($data['add_donor_names']) && !empty($data['donor_names'])) {
                foreach ($data['add_donor_names'] as $name) {
                    if (!empty(trim($name))) { // Also check if name is not empty
                        donor_names::create([
                            'pregnancy_plan_id' => $id,
                            'donor_name' => trim($name)
                        ]);
                    }
                }
            };

            return response()->json(['message' => 'Add Patient Pregnancy Plan Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function removeRecord($typeOfRecord, $id)
    {
        try {
            if ($typeOfRecord === 'case-record') {
                $prenatalCaseRecord = prenatal_case_records::findOrFail($id);
                $prenatalCaseRecord->update([
                    'status' => 'Archived'
                ]);

                return response()->json(['message' => 'Patient Prenatal Case is deleted Successfully'], 200);
            }

            if ($typeOfRecord === 'pregnancy-plan') {
                $pregnancyPlanRecord = pregnancy_plans::findOrFail($id);

                $pregnancyPlanRecord->update([
                    'status' => 'Archived'
                ]);
                return response()->json(['message' => 'Patient Pregnancy Plan is deleted Successfully'], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    private function compressAndSaveSignature($file)
    {
        $filename = time() . '_' . uniqid() . '.jpg';
        $path = storage_path('app/public/signatures/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/signatures'))) {
            mkdir(storage_path('app/public/signatures'), 0755, true);
        }

        // Process image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->scale(width: 800);
        $image->toJpeg(quality: 60);
        $image->save($path);

        return 'signatures/' . $filename;
    }

    private function saveCanvasSignature($base64Data)
    {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);

        $filename = time() . '_' . uniqid() . '.jpg';
        $path = storage_path('app/public/signatures/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/signatures'))) {
            mkdir(storage_path('app/public/signatures'), 0755, true);
        }

        // Process image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($imageData);
        $image->scale(width: 800);
        $image->toJpeg(quality: 60);
        $image->save($path);

        return 'signatures/' . $filename;
    }
}
