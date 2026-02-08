<?php

namespace App\Http\Controllers;

use App\Mail\PatientAccountCreated;
use App\Models\family_planning_case_records;
use App\Models\family_planning_medical_histories;
use App\Models\family_planning_medical_records;
use App\Models\family_planning_obsterical_histories;
use App\Models\family_planning_physical_examinations;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\prenatal_case_records;
use App\Models\risk_for_sexually_transmitted_infections;
use App\Models\User;
use App\Models\wra_masterlists;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FamilyPlanningController extends Controller
{
    //

    // generate random password
    public function generateSecurePassword($length = 8)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';

        // Ensure at least one character from each set
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill the rest randomly
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password to randomize character positions
        return str_shuffle($password);
    }
    
    public function addPatient(Request $request)
    {
        try {
            // validations
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
                'age' => 'required|numeric|min:10|max:49',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'required|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'street' => 'required',
                'brgy' => 'required',
                'civil_status' => 'sometimes|nullable|string',
                'suffix' => 'sometimes|nullable|string',
                'email' => 'required|email',
                'user_account' => 'sometimes|nullable|numeric'
            ], [
                'first_name.unique' => 'This patient already exists.',
            ]);

            $medicalData = $request->validate([
                'religion' => 'sometimes|nullable|string',
                'family_plan_occupation' => 'sometimes|nullable|string',
                'philhealth_no' => [
                    'sometimes',
                    'nullable',
                    'regex:/^\d{2}-\d{9}-\d{1}$/'
                ],
                'blood_pressure' => [
                    'sometimes',
                    'nullable',
                    'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                ],
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,300',  // kg range
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
                'spouse_suffix' => 'sometimes|nullable|string',

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
                'add_family_planning_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_family_planning_signature_data' => 'sometimes|nullable|string',
                'family_planning_date_of_acknowledgement' => 'sometimes|nullable|date',
                'add_family_planning_consent_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_family_planning_consent_signature_data' => 'sometimes|nullable|string',
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

            // side b info
            $sideBdata = $request->validate([
                'side_b_date_of_visit' => 'required|date',
                'side_b_medical_findings' => 'sometimes|nullable|string',
                'side_b_method_accepted' => 'sometimes|nullable|string',
                'add_side_b_name_n_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_side_b_name_n_signature_data' => 'sometimes|nullable|string',
                'side_b_date_of_follow_up_visit' => 'required|date',
                'baby_Less_than_six_months_question' => 'sometimes|nullable|string',
                'sexual_intercouse_or_mesntrual_period_question' => 'sometimes|nullable|string',
                'baby_last_4_weeks_question' => 'sometimes|nullable|string',
                'menstrual_period_in_seven_days_question' => 'sometimes|nullable|string',
                'miscarriage_or_abortion_question' => 'sometimes|nullable|string',
                'contraceptive_question' => 'sometimes|nullable|string'
            ]);

            // check if the email is valid
            if ($patientData['user_account']) {
                $errors = [];

                try {
                    $user = User::with('user_address')->findOrFail((int)$patientData['user_account']);

                    // Validate email
                    if ($user->email != $patientData['email']) {
                        $errors['email'] = ["Patient Account email doesn't match the email input value."];
                    }

                    // Validate house number (required)
                    if (isset($blk_n_street[0]) && $blk_n_street[0] != $user->user_address->house_number) {
                        $errors['street'] = ["House number doesn't match the patient account records."];
                    }

                    // Validate street (optional - only if provided)
                    if (isset($blk_n_street[1]) && !empty(trim($blk_n_street[1]))) {
                        if (trim($blk_n_street[1]) != $user->user_address->street) {
                            if (!isset($errors['street'])) {
                                $errors['street'] = [];
                            }
                            $errors['street'][] = "Street doesn't match the patient account records.";
                        }
                    }

                    // Validate barangay/purok
                    if ($patientData['brgy'] != $user->user_address->purok) {
                        $errors['brgy'] = ["Barangay doesn't match the patient account records."];
                    }

                    // If there are errors, return JSON response
                    if (!empty($errors)) {
                        return response()->json([
                            'message' => 'The given data does not match our records.',
                            'errors' => $errors
                        ], 422);
                    }

                    // If validation passes, continue with your logic...

                } catch (ModelNotFoundException $e) {
                    return response()->json([
                        'message' => 'Patient account not found.',
                        'errors' => [
                            'user_account' => ['The selected patient account does not exist.']
                        ]
                    ], 404);
                }
            }
            // ==================================================================================================================================

            // -------------------------------------------------------------------------------------------------------
            // INSERT THE DATA 
            $middle = substr($patientData['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $middleName = $patientData['middle_initial'] ? ucwords(strtolower($patientData['middle_initial'])) : '';
            $parts = [
                strtolower($patientData['first_name']),
                $middle,
                strtolower($patientData['last_name']),
                $patientData['suffix'] ?? null
            ];

            // address
            $blk_n_street = explode(',', $patientData['street']);

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));

            // create the patient record
            $familPlanningPatient = patients::create([
                'user_id' => null,
                'first_name' => ucwords(strtolower($patientData['first_name'])),
                'middle_initial' => $middleName,
                'last_name' => ucwords(strtolower($patientData['last_name'])),
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

            // create the patient account if not existed
            // Insert user data or update only
            if ($patientData['user_account']) {
                try {
                    $user = User::with('user_address')->findOrFail((int)$patientData['user_account']);

                    // Update existing user
                    $user->update([
                        'patient_record_id' => $familPlanningPatient->id,
                        'first_name' => ucwords(strtolower($patientData['first_name'])),
                        'middle_initial' => $middleName,
                        'last_name' => ucwords(strtolower($patientData['last_name'])),
                        'full_name' => $fullName,
                        'email' => $patientData['email'],
                        'contact_number' => $patientData['contact_number'] ?? null,
                        'date_of_birth' => $patientData['date_of_birth'] ?? null,
                        'suffix' => $patientData['suffix'] ?? null,
                        'patient_type' => $patientData['type_of_patient'],
                        'role' => 'patient',
                        'status' => 'active'
                    ]);

                    // Update or create user address
                    if ($user->user_address) {
                        $user->user_address->update([
                            'patient_id' => $familPlanningPatient->id,
                            'house_number' => $blk_n_street[0],
                            'street' => $blk_n_street[1] ?? null,
                            'purok' => $patientData['brgy']
                        ]);
                    } else {
                        // Create address if it doesn't exist
                        $user->user_address()->create([
                            'patient_id' => $familPlanningPatient->id,
                            'house_number' => $blk_n_street[0],
                            'street' => $blk_n_street[1] ?? null,
                            'purok' => $patientData['brgy']
                        ]);
                    }
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    // User account not found, but this shouldn't happen if validation passed
                    return response()->json([
                        'message' => 'Patient account not found.',
                        'errors' => [
                            'user_account' => ['The selected patient account does not exist.']
                        ]
                    ], 404);
                } catch (\Exception $e) {
                    // Log the error
                    // \Log::error('Error updating user account: ' . $e->getMessage());

                    return response()->json([
                        'message' => 'An error occurred while updating patient information.',
                        'errors' => [
                            'server' => ['Please try again or contact support.']
                        ]
                    ], 500);
                }
            } else {
                // Create new user account
                $temporaryPassword = $this->generateSecurePassword(8);
                try {
                    // Create user
                    $user = User::create([
                        'patient_record_id' => $familPlanningPatient->id,
                        'first_name' => ucwords(strtolower($patientData['first_name'])),
                        'middle_initial' => $middleName,
                        'last_name' => ucwords(strtolower($patientData['last_name'])),
                        'full_name' => $fullName,
                        'email' => $patientData['email'],
                        'contact_number' => $patientData['contact_number'] ?? null,
                        'date_of_birth' => $patientData['date_of_birth'] ?? null,
                        'suffix' => $patientData['suffix'] ?? null,
                        'patient_type' => $patientData['type_of_patient'],
                        'password' => Hash::make($temporaryPassword),
                        'role' => 'patient',
                        'status' => 'active',
                        'password' => bcrypt('default_password_123') // Set a default password or generate one
                    ]);

                    // Send email with credentials
                    Mail::to($user->email)->send(new PatientAccountCreated($user, $temporaryPassword));
                    // Create user address
                    $user->user_address()->create([
                        'patient_id' => $familPlanningPatient->id,
                        'house_number' => $blk_n_street[0],
                        'street' => $blk_n_street[1] ?? null,
                        'purok' => $patientData['brgy']
                    ]);
                } catch (\Exception $e) {
                    // Log the error

                    return response()->json([
                        'message' => 'An error occurred while creating patient account.',
                        'errors' => [
                            'server' => ['Please try again or contact support.']
                        ]
                    ], 500);
                }
            }
            //  =================================================================================================================================

            $familyPlanningPatientRecordId = $familPlanningPatient->id;

            // add the patient address
           
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

            $methods = $caseData['previously_used_method'] ?? null;
            $previoulyMethod = null;
            if ($methods) {

                $previoulyMethod = implode(",", $caseData['previously_used_method'] ?? []);
            }

            // signature 
            $signaturePath = null;
            $signatureConsentPath = null;

            // If user uploaded an image file
            if ($request->hasFile('add_family_planning_signature_image')) {
                $signaturePath = $this->compressAndSaveSignature($request->file('add_family_planning_signature_image'));
            }
            // If user drew a signature
            else if ($request->filled('add_family_planning_signature_data')) {
                $signaturePath = $this->saveCanvasSignature($request->add_family_planning_signature_data);
            }
            // signature consent
            if ($request->hasFile('add_family_planning_consent_signature_image')) {
                $signatureConsentPath = $this->compressAndSaveSignature($request->file('add_family_planning_consent_signature_image'));
            }
            // If user drew a signature
            else if ($request->filled('add_family_planning_consent_signature_data')) {
                $signatureConsentPath = $this->saveCanvasSignature($request->add_family_planning_consent_signature_data);
            }

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
                'client_suffix' => $patientData['suffix'] ?? '',
                'spouse_lname' => $caseData['spouse_lname'] ?? null,
                'spouse_fname' => $caseData['spouse_fname'] ?? null,
                'spouse_MI' => $caseData['spouse_MI'] ?? null,
                'spouse_date_of_birth' => $caseData['spouse_date_of_birth'] ?? null,
                'spouse_age' => $caseData['spouse_age'] ?? null,
                'spouse_occupation' => $caseData['spouse_occupation'] ?? null,
                'spouse_suffix' => $caseData['spouse_suffix'] ?? '',
                'number_of_living_children' => $caseData['number_of_living_children'] ?? null,
                'plan_to_have_more_children' => $caseData['plan_to_have_more_children'] ?? null,

                'average_montly_income' => $caseData['average_montly_income'] ?? null,
                'type_of_patient' => $caseData['family_planning_type_of_patient'] ?? null,
                'new_acceptor_reason_for_FP' => $caseData['new_acceptor_reason_for_FP'] ?? null,
                'current_user_reason_for_FP' => $caseData['current_user_reason_for_FP'] ?? null,
                'current_method_reason' => $caseData['current_method_reason'] ?? null,
                'previously_used_method' => $previoulyMethod ?? null,
                'choosen_method' => $caseData['choosen_method'] ?? null,
                'signature_image' => $signaturePath ?? null,
                'date_of_acknowledgement' => $caseData['family_planning_date_of_acknowledgement'] ?? null,
                'acknowledgement_consent_signature_image' => $signatureConsentPath ?? null,
                'date_of_acknowledgement_consent' => $caseData['family_planning_date_of_acknowledgement_consent'] ?? null,
                'current_user_type' => $caseData['current_user_type'] ?? null,
                'status' => 'Active'
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
                'weight' => $medicalData['weight'] ?? null,

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

            // side b signature
            // signature 
            $sideBsignaturePath = null;


            // If user uploaded an image file
            if ($request->hasFile('add_side_b_name_n_signature_image')) {
                $sideBsignaturePath = $this->compressAndSaveSignature($request->file('add_side_b_name_n_signature_image'));
            }
            // If user drew a signature
            else if ($request->filled('add_side_b_name_n_signature_data')) {
                $sideBsignaturePath = $this->saveCanvasSignature($request->add_side_b_name_n_signature_data);
            }

            // add side b record
            family_planning_side_b_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id' => $patientData['handled_by'],
                'date_of_visit' => $sideBdata['side_b_date_of_visit'] ?? null,
                'medical_findings' => $sideBdata['side_b_medical_findings'] ?? null,
                'method_accepted' => $sideBdata['side_b_method_accepted'] ?? null,
                'signature_of_the_provider' => $sideBsignaturePath ?? null,
                'date_of_follow_up_visit' => $sideBdata['side_b_date_of_follow_up_visit'] ?? null,
                'baby_Less_than_six_months_question' => $sideBdata['baby_Less_than_six_months_question'] ?? null,
                'sexual_intercouse_or_mesntrual_period_question' => $sideBdata['sexual_intercouse_or_mesntrual_period_question'] ?? null,
                'baby_last_4_weeks_question' => $sideBdata['baby_last_4_weeks_question'] ?? null,
                'menstrual_period_in_seven_days_question' => $sideBdata['menstrual_period_in_seven_days_question'] ?? null,
                'miscarriage_or_abortion_question' => $sideBdata['miscarriage_or_abortion_question'] ?? null,
                'contraceptive_question' => $sideBdata['contraceptive_question'] ?? null,
                'status' => 'Active'
            ]);

            // --------------------------------------------------- WRA masterlist record -------------------------------------------------------------------------
            $method_of_FP = [
                'modern' => ['Implant', 'IUD', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
            ];

            $modern_methods = [];
            $traditional_methods = [];

            if ($methods) {

                if ($caseData['previously_used_method'] != null) {
                    foreach ($caseData['previously_used_method'] as $method) {
                        if (in_array($method, $method_of_FP['modern'])) {
                            $modern_methods[] = $method;
                        } elseif (in_array($method, $method_of_FP['traditional'])) {
                            $traditional_methods[] = $method;
                        }
                    }
                }
            }
            // convert them to string
            $converted_modern_methods = implode(",", $modern_methods);
            $converted_traditional_methods = implode(",", $traditional_methods);

            // check if the patient currently accept any modern methods
            $method_accepted = [];
            if ($caseData['choosen_method']) {
                $method_accepted = explode(",", $caseData['choosen_method']);
            }

            $accept_modern_FP = [];
            foreach ($method_accepted as $method) {

                if (in_array($method, $method_of_FP['modern'])) {
                    $accept_modern_FP[] = $method;
                }
            }
            $converted_accepted_modern_FP = implode(",", $accept_modern_FP);

            if ($patientData['age'] >= 10) {
                $wra_masterlist = wra_masterlists::create([
                    'medical_record_case_id' => $medicalCaseId,
                    'health_worker_id' => $patientData['handled_by'],
                    'address_id' => $patientAddress->id,
                    'patient_id' => $familyPlanningPatientRecordId,
                    'brgy_name' => $patientAddress->purok,
                    'house_hold_number' => null,
                    'name_of_wra' => $familPlanningPatient->full_name,
                    'address' => $fullAddress,
                    'age' => $patientData['age'] ?? null,
                    'date_of_birth' => $patientData['date_of_birth'] ?? null,
                    'SE_status' => ($caseData['NHTS'] ?? null) === 'yes'
                        ? 'NHTS'
                        : (($caseData['NHTS'] ?? null) !== null ? 'Yes' : 'No'),
                    'plan_to_have_more_children_yes' => ($caseData['plan_to_have_more_children'] ?? null) === 'Yes' ? collect(
                        $caseData['new_acceptor_reason_for_FP'] ?? null,
                        $caseData['current_user_reason_for_FP'] ?? null,
                        $caseData['current_method_reason'] ?? null
                    )->first(fn($value) => !empty($value)) : null,
                    'plan_to_have_more_children_no' => ($caseData['plan_to_have_more_children'] ?? null) === 'No' ? 'limiting' : null,
                    'current_FP_methods' => ($caseData['family_planning_type_of_patient'] ?? null) === 'current user' ? $previoulyMethod : null,
                    'modern_FP' => $converted_modern_methods ?? null,
                    'traditional_FP' =>  $converted_traditional_methods ?? null,
                    'currently_using_any_FP_method_no' => empty($caseData['previously_used_method']) ? 'yes' : null,
                    'shift_to_modern_method' => null,
                    'wra_with_MFP_unmet_need' => 'no',
                    'wra_accept_any_modern_FP_method' => $converted_accepted_modern_FP != null ? 'yes' : 'no',
                    'selected_modern_FP_method' => $converted_accepted_modern_FP ?? null,
                    'date_when_FP_method_accepted' => !empty($converted_accepted_modern_FP)
                        ? ($caseData['family_planning_date_of_acknowledgement'] ?? null)
                        : null
                ]);
            }




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
            $familyPlanningMedicalRecord = family_planning_medical_records::where("medical_record_case_id", $familyPlanningRecord->id)->first();
            $familyPlanningCaseRecord = family_planning_case_records::where("medical_record_case_id", $familyPlanningRecord->id)->where("status", "!=", 'Archived')->first();
            $address = patient_addresses::where('patient_id', $familyPlanningRecord->patient->id)->firstOrFail();


            $data = $request->validate([
                'first_name' => [
                    'required',
                    'string',
                    Rule::unique('patients')
                        ->ignore($familyPlanningRecord->patient->id) // <-- IMPORTANT
                        ->where(function ($query) use ($request) {
                            return $query->where('first_name', $request->first_name)
                                ->where('last_name', $request->last_name);
                        }),
                ],
                'last_name' => 'required|string',
                'middle_initial' => 'sometimes|nullable|string',
                'date_of_birth' => 'sometimes|nullable|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric|max:100',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'required|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'civil_status' => 'sometimes|nullable|string',
                'occupation' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
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
                'weight'            => 'nullable|numeric|between:1,300',  // kg range
                'client_id' =>  'sometimes|nullable|numeric',
                'philhealth_no' => [
                    'sometimes',
                    'nullable',
                    'regex:/^\d{2}-\d{9}-\d{1}$/'
                ],
                'NHTS' => 'sometimes|nullable|string',
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
            $sex = isset($data['sex']) ? $data['sex']: null;
            // update the patient data first
            $familyPlanningRecord->patient->update([
                'first_name' => ucwords(strtolower($data['first_name'])) ?? ucwords(strtolower($familyPlanningRecord->patient->first_name)),
                'middle_initial' =>  $middleName,
                'last_name' => ucwords(strtolower($data['last_name'])) ?? ucwords(strtolower($familyPlanningRecord->patient->last_name)),
                'full_name' => $fullName ?? $familyPlanningRecord->patient->full_name,
                'age' => $data['age'] ?? $familyPlanningRecord->patient->age,
                'sex' => $sex ? ucfirst($sex) : null,
                'civil_status' => $data['civil_status'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'date_of_registration' => $data['date_of_registration'] ?? $familyPlanningRecord->patient->date_of_registration,
                'place_of_birth' => $data['place_of_birth'] ?? null,
                'suffix' => $data['suffix'] ?? ''
            ]);
            // update the address
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

            $familyPlanningRecord->patient->refresh();

            // update medical record
            $familyPlanningMedicalRecord->update([
                'health_worker_id' => $data['handled_by'] ?? $familyPlanningMedicalRecord->health_worker_id,
                'patient_name' => $familyPlanningRecord->patient->full_name,
                'occupation' => $data['occupation'] ?? null,
                'religion' => $data['religion'] ?? null,
                'philhealth_no' => $data['philhealth_no'] ?? null,
                'blood_pressure' => $data['blood_pressure'] ?? null,
                'temperature' => $data['temperature'] ?? null,
                'pulse_rate' => $data['pulse_rate'] ?? null,
                'respiratory_rate' => $data['respiratory_rate'] ?? null,
                'height' => $data['height'] ?? null,
                'weight' => $data['weight'] ?? null
            ]);
            // update case record

            if ($familyPlanningCaseRecord) {
                $familyPlanningCaseRecord->update([
                    'client_name' => $familyPlanningRecord->patient->full_name,
                    'client_id' => $data['client_id'] ?? $familyPlanningCaseRecord->client_id,
                    'philhealth_no' => $data['philhealth_no'] ?? null,
                    'NHTS' => $data['NHTS'] ?? null,
                    'client_address' =>  $fullAddress ?? '',
                    'client_date_of_birth' => $data['date_of_birth'] ?? $familyPlanningCaseRecord->client_date_of_birth,
                    'client_age' => $data['age'] ?? $familyPlanningCaseRecord->client_age,
                    'occupation' => $data['occupation'] ?? null,
                    'client_suffix' => $data['suffix'] ?? '',
                    'client_contact_number' => $data['contact_number'] ?? $familyPlanningCaseRecord->client_contact_number,
                    'client_civil_status' => $data['civil_status'] ?? null,
                    'client_religion' => $data['religion'] ?? null
                ]);
            }


            // update the prenatal and wra if the patient have those records
            $prenatalMedicalCaseRecord = medical_record_cases::where('patient_id', $familyPlanningRecord->patient->id)->where('type_of_case', 'prenatal')->first() ?? null;
            if ($prenatalMedicalCaseRecord) {
                $prenatalCase = prenatal_case_records::where('medical_record_case_id', $prenatalMedicalCaseRecord->id)->first() ?? null;
                if ($prenatalCase) {
                    $fullName = $fullName ?: $prenatalCase->patient_name;
                    $prenatalCase->update([
                        'patient_name' => $fullName
                    ]);
                }
            }
            // wra
            $wraRecord = wra_masterlists::where('patient_id', $familyPlanningRecord->patient->id)->first() ?? null;
            if ($wraRecord) {
                $wraRecord->update([
                    'name_of_wra' => $fullName ?? $wraRecord->name_of_wra
                ]);
            }

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
            $medicalRecord = medical_record_cases::with('patient')->where('id', $familyPlanCaseInfo->medical_record_case_id)->first();
            $address = patient_addresses::where('patient_id', $medicalRecord->patient_id)->first();
            return response()->json(['caseInfo' => $familyPlanCaseInfo, 'address' => $address], 200);
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
            $familyPlanCaseInfo = family_planning_case_records::where('type_of_record', 'Family Planning Client Assessment Record - Side A')
                ->where('medical_record_case_id', $id)
                ->where('status', '!=', 'Archived')
                ->get();

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
                'add_street' => 'required',
                'add_brgy' => 'required',
                'side_A_add_client_suffix' => 'sometimes|nullable|string'
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
                'side_A_add_spouse_suffix' => 'sometimes|nullable|string',

                'side_A_add_number_of_living_children' => 'sometimes|nullable|numeric|max:50',
                'side_A_add_plan_to_have_more_children' => 'sometimes|nullable|string',
                'side_A_add_average_montly_income' => 'sometimes|nullable|numeric',
                'side_A_add_type_of_patient' => 'sometimes|nullable|string',
                'side_A_add_new_acceptor_reason_for_FP' => 'sometimes|nullable|string',
                'side_A_add_current_user_reason_for_FP' => 'sometimes|nullable|string',
                'side_A_add_current_method_reason' => 'sometimes|nullable|string',
                'side_A_add_previously_used_method' => 'sometimes|nullable|array',

                // acknowledgement
                'side_A_add_choosen_method' => 'sometimes|nullable|string',

                'side_A_add_family_planning_acknowledgement_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'side_A_add_family_planning_acknowledgement_signature_data' => 'sometimes|nullable|string',

                'side_A_add_family_planning_date_of_acknowledgement' => 'sometimes|nullable|date',

                'side_A_add_family_planning_consent_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'side_A_add_family_planning_consent_signature_data' => 'sometimes|nullable|string',

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
                'side_A_add_blood_pressure' => [
                    'sometimes',
                    'nullable',
                    'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                ],
                'side_A_add_pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'side_A_add_height'            => 'nullable|numeric|between:30,300', // cm range
                'side_A_add_weight'            => 'nullable|numeric|between:1,300',  // kg range
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
            $previoulyMethod = implode(",", $caseData['side_A_add_previously_used_method'] ?? []);

            $signaturePath = null;
            $signatureConsentPath = null;

            // If user uploaded an image file
            if ($request->hasFile('side_A_add_family_planning_acknowledgement_signature_image')) {
                $signaturePath = $this->compressAndSaveSignature($request->file('side_A_add_family_planning_acknowledgement_signature_image'));
            }
            // If user drew a signature
            else if ($request->filled('side_A_add_family_planning_acknowledgement_signature_data')) {
                $signaturePath = $this->saveCanvasSignature($request->side_A_add_family_planning_acknowledgement_signature_data);
            }
            // signature consent
            if ($request->hasFile('side_A_add_family_planning_consent_signature_image')) {
                $signatureConsentPath = $this->compressAndSaveSignature($request->file('side_A_add_family_planning_consent_signature_image'));
            }
            // If user drew a signature
            else if ($request->filled('side_A_add_family_planning_consent_signature_data')) {
                $signatureConsentPath = $this->saveCanvasSignature($request->side_A_add_family_planning_consent_signature_data);
            }

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
                'client_suffix' => $patientData['side_A_add_client_suffix'] ?? '',
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
                'spouse_suffix' => $caseData['spouse_suffix'] ?? '',
                'number_of_living_children' => $caseData['side_A_add_number_of_living_children'] ?? null,
                'plan_to_have_more_children' => $caseData['side_A_add_plan_to_have_more_children'] ?? null,

                'average_montly_income' => $caseData['side_A_add_average_montly_income'] ?? null,
                'type_of_patient' => $caseData['side_A_add_type_of_patient'] ?? null,
                'new_acceptor_reason_for_FP' => $caseData['side_A_add_new_acceptor_reason_for_FP'] ?? null,
                'current_user_reason_for_FP' => $caseData['side_A_add_current_user_reason_for_FP'] ?? null,
                'current_method_reason' => $caseData['side_A_add_current_method_reason'] ?? null,
                'previously_used_method' => $previoulyMethod ?? null,
                'choosen_method' => $caseData['side_A_add_choosen_method'] ?? null,
                'signature_image' => $signaturePath ?? null,
                'date_of_acknowledgement' => $caseData['side_A_add_family_planning_date_of_acknowledgement'] ?? null,
                'acknowledgement_consent_signature_image' => $signatureConsentPath ?? null,
                'date_of_acknowledgement_consent' => $caseData['side_A_add_family_planning_date_of_acknowledgement_consent'] ?? null,
                'current_user_type' => $caseData['side_A_add_current_user_type'] ?? null,
                'status' => 'Active'
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
                'weight' => $medicalData['side_A_add_weight'] ?? null,

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
            // =====================================================================================================
            // ================================== WRA UPDATE =======================================================

            // update the wra masterlist
            // get the masterlist first
            $wra_masterlist_record = wra_masterlists::where('medical_record_case_id', $id)->first();


            $method_of_FP = [
                'modern' => ['Implant', 'UID', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
            ];

            $modern_methods = [];
            $traditional_methods = [];
            $previouslyMethods = $caseData['side_A_add_previously_used_method'] ?? null;

            if ($previouslyMethods) {
                foreach ($previouslyMethods as $method) {
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
            if ($caseData['side_A_add_choosen_method']) {
                $method_accepted = explode(",", $caseData['side_A_add_choosen_method']);
            }

            $accept_modern_FP = [];
            foreach ($method_accepted as $method) {

                if (in_array($method, $method_of_FP['modern'])) {
                    $accept_modern_FP[] = $method;
                }
            }
            $converted_accepted_modern_FP = implode(",", $accept_modern_FP);

            // get the patient infor
            $medical_case_record = medical_record_cases::with('patient')->where('id', $id)->first();

            $address = patient_addresses::where('patient_id',  $medical_case_record->patient->id)->firstOrFail();
            $blk_n_street = explode(',', $patientData['add_street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street' => $blk_n_street[1] ?? $address->street,
                'purok' => $patientData['add_brgy'] ?? $address->purok
            ]);

            // update other part
            $address->refresh();
            $newAddress = $address->house_number . ", " . $address->street . "," . $address->purok . "," . $address->barangay . "," . $address->city . "," . $address->province;
            if ($patientData['side_A_add_client_age'] >= 10) {
                $wra_masterlist_record->update([
                    'brgy_name' => $address->purok,
                    'name_of_wra' => $medical_case_record->patient->full_name,
                    'address' => $newAddress,
                    'age' => $patientData['side_A_add_client_age'] ?? null,
                    'date_of_birth' => $patientData['side_A_add_client_date_of_birth'] ?? $wra_masterlist_record->date_of_birth,
                    'SE_status' => ($caseData['side_A_add_NHTS'] ?? null) === 'yes'
                        ? 'NHTS'
                        : (($caseData['edit_NHTS'] ?? null) !== null ? 'Yes' : 'No'),
                    'plan_to_have_more_children_yes' => ($caseData['side_A_add_plan_to_have_more_children'] ?? null) === 'Yes'
                        ? collect([
                            $caseData['side_A_add_new_acceptor_reason_for_FP'] ?? null,
                            $caseData['side_A_add_current_user_reason_for_FP'] ?? null,
                            $caseData['side_A_add_current_method_reason'] ?? null,
                        ])->first(fn($value) => !empty($value))
                        : null,
                    'plan_to_have_more_children_no' => ($caseData['side_A_add_plan_to_have_more_children'] ?? null) === 'No' ? 'limiting' :  null,
                    'current_FP_methods' => ($caseData['side_A_add_type_of_patient'] ?? null) === 'current user' ? $previoulyMethod : $wra_masterlist_record->current_FP_methods,
                    'modern_FP' => $converted_modern_methods ?? null,
                    'traditional_FP' =>  $converted_traditional_methods ?? null,
                    'currently_using_any_FP_method_no' => empty($caseData['side_A_add_previously_used_method']) ? 'yes' : null,
                    'wra_accept_any_modern_FP_method' => $converted_accepted_modern_FP != null ? 'yes' : 'no',
                    'selected_modern_FP_method' => $converted_accepted_modern_FP ?? null,
                    'date_when_FP_method_accepted' =>
                    !empty($converted_accepted_modern_FP)
                        ? ($caseData['side_A_add_date_of_acknowledgement']
                            ?? $wra_masterlist_record->date_when_FP_method_accepted)
                        : $wra_masterlist_record->date_when_FP_method_accepted,


                ]);
            }

            return response()->json(['message' => 'Family Planning Client Assessment Record - Side A information is added Successfully'], 200);
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

            // dd($request);
            $patientData = $request->validate(
                [
                    'edit_client_fname' => 'required|string',
                    'edit_client_MI' => 'sometimes|nullable|string',
                    'edit_client_lname' => 'required|string',
                    'edit_client_date_of_birth' => 'sometimes|nullable|date',
                    'edit_client_age' => 'required|numeric|max:100',
                    'edit_occupation' => 'sometimes|nullable|string',
                    'edit_client_civil_status' => 'sometimes|nullable|string',
                    'edit_client_religion' => 'sometimes|nullable|string',
                    'edit_street' => 'required',
                    'edit_brgy' => 'required',
                    'edit_client_contact_number' => 'required|digits_between:7,12',
                    'edit_client_suffix' => 'sometimes|nullable|string',
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
                    'edit_spouse_MI' => 'sometimes|nullable|string',
                    'edit_spouse_date_of_birth' => 'sometimes|nullable|date',
                    'edit_spouse_age' => 'sometimes|nullable|numeric|max:100',
                    'edit_spouse_occupation' => 'sometimes|nullable|string',
                    'edit_spouse_suffix' => 'sometimes|nullable|string',

                    'edit_number_of_living_children' => 'sometimes|nullable|numeric|max:50',
                    'edit_plan_to_have_more_children' => 'sometimes|nullable|string',
                    'edit_average_montly_income' => 'sometimes|nullable|numeric',
                    'edit_type_of_patient' => 'sometimes|nullable|string',
                    'edit_new_acceptor_reason_for_FP' => 'sometimes|nullable|string',
                    'edit_current_user_reason_for_FP' => 'sometimes|nullable|string',
                    'edit_current_user_reason_for_FP_other' => 'sometimes|nullable|string',
                    'edit_current_method_reason' => 'sometimes|nullable|string',
                    'edit_previously_used_method' => 'sometimes|nullable|array',

                    // acknowledgement
                    'edit_choosen_method' => 'sometimes|nullable|string',
                    'edit_family_planning_acknowledgement_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                    'edit_family_planning_acknowledgement_signature_data' => 'sometimes|nullable|string',

                    'edit_date_of_acknowledgement' => 'sometimes|nullable|date',

                    'edit_family_planning_consent_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                    'edit_family_planning_consent_signature_data' => 'sometimes|nullable|string',

                    'edit_date_of_acknowledgement_consent' => 'sometimes|nullable|date',
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
                    'edit_type_of_patient' => 'type of family planning patient',
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
                'edit_blood_pressure' => [
                    'sometimes',
                    'nullable',
                    'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                ],
                'edit_pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'edit_height'            => 'nullable|numeric|between:30,300', // cm range
                'edit_weight'            => 'nullable|numeric|between:1,300',  // kg range
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

            $middle = substr($patientData['edit_client_MI'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $parts = [
                strtolower($patientData['edit_client_fname']),
                $middle,
                strtolower($patientData['edit_client_lname']),
                $patientData['edit_client_suffix'] ?? null
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));

            // update patient info first
            $medical_case_record->patient->update([
                'first_name' => $patientData['edit_client_fname'] ?? $medical_case_record->patient->first_name,
                'middle_initial' => $patientData['edit_client_MI'] ?? '',
                'last_name' => $patientData['edit_client_lname'] ?? $medical_case_record->patient->last_name,
                'full_name' => $fullName,
                'age' => $patientData['edit_client_age'] ?? $medical_case_record->patient->age,
                'contact_number' => $patientData['edit_client_contact_number'] ?? null,
                'date_of_birth' => $patientData['edit_client_date_of_birth'] ?? $medical_case_record->patient->date_of_birth,
                'civil_status' => $patientData['edit_client_civil_status'] ?? null,
                'suffix' => $patientData['edit_client_suffix'] ?? ''
            ]);
            $address = patient_addresses::where('patient_id',  $medical_case_record->patient->id)->firstOrFail();
            $blk_n_street = explode(',', $patientData['edit_street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street' => $blk_n_street[1] ?? $address->street,
                'purok' => $patientData['edit_brgy'] ?? $address->purok
            ]);

            // update other part
            $address->refresh();
            $newAddress = $address->house_number . ", " . $address->street . "," . $address->purok . "," . $address->barangay . "," . $address->city . "," . $address->province;


            $medical_case_record->family_planning_medical_record->update([
                'patient_name' => trim(($patientData['edit_client_fname'] . ' ' . $patientData['edit_client_MI'] . ' ' . $patientData['edit_client_lname'])) ?? $medical_case_record->patient->full_name,
                'occupation' => $patientData['edit_occupation'] ?? null,
                'blood_pressure' => $physicalExaminationData['edit_blood_pressure'] ?? null,
                'pulse_rate' => $physicalExaminationData['edit_pulse_rate'] ?? null,
                'height' => $physicalExaminationData['edit_height'] ?? null,
                'weight' => $physicalExaminationData['edit_weight'] ?? null,
                'religion' => $patientData['edit_client_religion'] ?? null
            ]);

            // refresh
            $medical_case_record->patient->refresh();
            $previoulyMethod = null;

            if (isset($caseData['edit_previously_used_method']) && !empty($caseData['edit_previously_used_method'])) {
                $previoulyMethod = implode(",", $caseData['edit_previously_used_method']);
            }

            if (!empty(trim($caseData['edit_current_user_reason_for_FP_other'] ?? ''))) {
                $currentReason = trim($caseData['edit_current_user_reason_for_FP_other']);
            } else {
                // Otherwise, use the radio selection
                $currentReason = $caseData['edit_current_user_reason_for_FP'] ?? $familyPlanCaseInfo->current_user_reason_for_FP;
            }

            // signature
            $signaturePath = $familyPlanCaseInfo->signature_image; // Keep old signature by default
            $consentSignaturePath = $familyPlanCaseInfo->acknowledgement_consent_signature_image;

            // Check if new signature provided (drawn)
            if ($request->filled('edit_family_planning_acknowledgement_signature_data')) {
                // Delete old file if exists
                if ($familyPlanCaseInfo->signature_image) {
                    Storage::disk('public')->delete($familyPlanCaseInfo->signature_image);
                }
                $signaturePath = $this->saveCanvasSignature($request->edit_family_planning_acknowledgement_signature_data);
            }
            // Check if new signature provided (uploaded)
            else if ($request->hasFile('edit_family_planning_acknowledgement_signature_image')) {
                // Delete old file if exists
                if ($familyPlanCaseInfo->signature_image) {
                    Storage::disk('public')->delete($familyPlanCaseInfo->signature_image);
                }
                $signaturePath = $this->compressAndSaveSignature($request->file('edit_family_planning_acknowledgement_signature_image'));
            }

            // consent part
            // Check if new signature provided (drawn)
            if ($request->filled('edit_family_planning_consent_signature_data')) {
                // Delete old file if exists
                if ($familyPlanCaseInfo->acknowledgement_consent_signature_image) {
                    Storage::disk('public')->delete($familyPlanCaseInfo->acknowledgement_consent_signature_image);
                }
                $consentSignaturePath = $this->saveCanvasSignature($request->edit_family_planning_consent_signature_data);
            }
            // Check if new signature provided (uploaded)
            else if ($request->hasFile('edit_family_planning_acknowledgement_signature_image')) {
                // Delete old file if exists
                if ($familyPlanCaseInfo->acknowledgement_consent_signature_image) {
                    Storage::disk('public')->delete($familyPlanCaseInfo->acknowledgement_consent_signature_image);
                }
                $consentSignaturePath = $this->compressAndSaveSignature($request->file('edit_family_planning_acknowledgement_signature_image'));
            }

            // update the case
            $familyPlanCaseInfo->update([
                'client_id' => $caseData['edit_client_id'] ?? null,
                'philhealth_no' => $caseData['edit_philhealth_no'] ??null,
                'NHTS' => $caseData['edit_NHTS'] ?? null,
                'client_name' => $medical_case_record->patient->full_name,
                'client_address' =>  $newAddress,
                'client_date_of_birth' => $patientData['edit_client_date_of_birth'] ?? $familyPlanCaseInfo->client_date_of_birth,
                'client_age' => $patientData['edit_client_age'] ?? $familyPlanCaseInfo->client_age,
                'occupation' => $patientData['edit_occupation'] ?? null,
                'client_contact_number' => $patientData['edit_client_contact_number'] ?? $familyPlanCaseInfo->client_contact_number,
                'client_civil_status' => $patientData['edit_client_civil_status'] ?? null,
                'client_religion' => $patientData['edit_client_religion'] ??null,
                'client_suffix' => $patientData['edit_client_suffix'] ?? '',
                'spouse_lname' => $caseData['edit_spouse_lname'] ?? null,
                'spouse_fname' => $caseData['edit_spouse_fname'] ?? null,
                'spouse_MI' => $caseData['edit_spouse_MI'] ?? null,
                'spouse_suffix' => $caseData['edit_spouse_suffix'] ?? '',
                'spouse_date_of_birth' => $caseData['edit_spouse_date_of_birth'] ?? null,
                'spouse_age' => $caseData['edit_spouse_age'] ?? null,
                'spouse_occupation' => $caseData['edit_spouse_occupation'] ?? null,
                'number_of_living_children' => $caseData['edit_number_of_living_children'] ?? null,
                'plan_to_have_more_children' => $caseData['edit_plan_to_have_more_children'] ?? null,

                'average_montly_income' => $caseData['edit_average_montly_income'] ?? null,
                'type_of_patient' => $caseData['edit_type_of_patient'] ?? null,
                'new_acceptor_reason_for_FP' => $caseData['edit_new_acceptor_reason_for_FP'] ?? null,
                'current_user_reason_for_FP' => $currentReason ?? null,
                'current_method_reason' => $caseData['edit_current_method_reason'] ?? null,
                'previously_used_method' => $previoulyMethod ?? $familyPlanCaseInfo->previously_used_method ?? null,
                'choosen_method' => $caseData['edit_choosen_method'] ?? null,
                'signature_image' => $signaturePath ?? $familyPlanCaseInfo->signature_image,
                'date_of_acknowledgement' => $caseData['edit_date_of_acknowledgement'] ?? null,
                'acknowledgement_consent_signature_image' =>  $consentSignaturePath ?? $familyPlanCaseInfo->acknowledgement_consent_signature_image,
                'date_of_acknowledgement_consent' => $caseData['edit_date_of_acknowledgement_consent'] ?? null,
                'current_user_type' => $caseData['edit_current_user_type'] ?? $familyPlanCaseInfo->current_user_type,
                'status' => 'Active'
            ]);

            $familyPlanCaseInfo->medical_history->update([
                'severe_headaches_migraine' => $medicalHistoryData['edit_severe_headaches_migraine'] ?? null,
                'history_of_stroke' => $medicalHistoryData['edit_history_of_stroke'] ?? null,
                'non_traumatic_hemtoma' => $medicalHistoryData['edit_non_traumatic_hemtoma'] ?? null,
                'history_of_breast_cancer' => $medicalHistoryData['edit_history_of_breast_cancer'] ?? null,
                'severe_chest_pain' => $medicalHistoryData['edit_severe_chest_pain'] ?? null,
                'cough' => $medicalHistoryData['edit_cough'] ?? null,
                'jaundice' => $medicalHistoryData['edit_jaundice'] ?? null,
                'unexplained_vaginal_bleeding' => $medicalHistoryData['edit_unexplained_vaginal_bleeding'] ?? null,
                'abnormal_vaginal_discharge' => $medicalHistoryData['edit_abnormal_vaginal_discharge'] ?? null,
                'abnormal_phenobarbital' => $medicalHistoryData['edit_abnormal_phenobarbital'] ?? null,
                'smoker' => $medicalHistoryData['edit_smoker'] ?? null,
                'with_dissability' => $medicalHistoryData['edit_with_dissability'] ?? null,
                'if_with_dissability_specification' => $medicalHistoryData['edit_if_with_dissability_specification'] ?? null,
            ]);

            $familyPlanCaseInfo->obsterical_history->update([
                'G' => $obstericalHistoryData['edit_G'] ?? null,
                'P' => $obstericalHistoryData['edit_P'] ?? null,
                'full_term' => $obstericalHistoryData['edit_full_term'] ?? null,
                'abortion' => $obstericalHistoryData['edit_abortion'] ?? null,
                'premature' => $obstericalHistoryData['edit_premature'] ?? null,
                'living_children' => $obstericalHistoryData['edit_living_children'] ?? null,
                'date_of_last_delivery' => $obstericalHistoryData['edit_date_of_last_delivery'] ?? null,
                'type_of_last_delivery' => $obstericalHistoryData['edit_type_of_last_delivery'] ?? null,
                'date_of_last_delivery_menstrual_period' => $obstericalHistoryData['edit_date_of_last_delivery_menstrual_period'] ?? null,
                'date_of_previous_delivery_menstrual_period' => $obstericalHistoryData['edit_date_of_previous_delivery_menstrual_period'] ?? null,
                'type_of_menstrual' => $obstericalHistoryData['edit_type_of_menstrual'] ?? null,
                'Dysmenorrhea' => $obstericalHistoryData['edit_Dysmenorrhea'] ?? null,
                'hydatidiform_mole' => $obstericalHistoryData['edit_hydatidiform_mole'] ?? null,
                'ectopic_pregnancy' => $obstericalHistoryData['edit_ectopic_pregnancy'] ?? null,
            ]);

            // risk for sexuall transmitted update
            $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->update([
                'infection_abnormal_discharge_from_genital_area' => $riskData['edit_infection_abnormal_discharge_from_genital_area'] ??  null,
                'origin_of_abnormal_discharge' => $riskData['edit_origin_of_abnormal_discharge'] ??  null,
                'scores_or_ulcer' => $riskData['edit_scores_or_ulcer'] ??  null,
                'pain_or_burning_sensation' => $riskData['edit_pain_or_burning_sensation'] ??  null,
                'history_of_sexually_transmitted_infection' => $riskData['edit_history_of_sexually_transmitted_infection'] ??  null,
                'sexually_transmitted_disease' => $riskData['edit_sexually_transmitted_disease'] ??  null,
                'history_of_domestic_violence_of_VAW' => $riskData['edit_history_of_domestic_violence_of_VAW'] ??  null,
                'unpleasant_relationship_with_partner' => $riskData['edit_unpleasant_relationship_with_partner'] ??  null,
                'partner_does_not_approve' => $riskData['edit_partner_does_not_approve'] ??  null,
                'referred_to' => $riskData['edit_referred_to'] ??  null,
                'reffered_to_others' => $riskData['edit_reffered_to_others'] ??  null,
            ]);

            // update physical examination


            $familyPlanCaseInfo->physical_examinations->update([
                'blood_pressure' => $physicalExaminationData['edit_blood_pressure'] ?? null,
                'pulse_rate' => $physicalExaminationData['edit_pulse_rate'] ?? null,
                'height' => $physicalExaminationData['edit_height'] ?? null,
                'weight' => $physicalExaminationData['edit_weight'] ?? null,

                'skin_type' => $physicalExaminationData['edit_skin_type'] ?? null,
                'conjuctiva_type' => $physicalExaminationData['edit_conjuctiva_type'] ?? null,
                'breast_type' => $physicalExaminationData['edit_breast_type'] ?? null,
                'abdomen_type' => $physicalExaminationData['edit_abdomen_type'] ?? null,
                'extremites_type' => $physicalExaminationData['edit_extremites_type'] ?? null,
                'extremites_UID_type' => $physicalExaminationData['edit_extremites_UID_type'] ?? null,
                'cervical_abnormalities_type' => $physicalExaminationData['edit_cervical_abnormalities_type'] ?? null,
                'cervical_consistency_type' => $physicalExaminationData['edit_cervical_consistency_type'] ?? null,
                'uterine_position_type' => $physicalExaminationData['edit_uterine_position_type'] ?? null,
                'uterine_depth_text' => $physicalExaminationData['edit_uterine_depth_text'] ?? null,
                'neck_type' => $physicalExaminationData['edit_neck_type'] ?? null
            ]);

            // update the wra masterlist
            // get the masterlist first
            $wra_masterlist_record = wra_masterlists::where('patient_id', $medical_case_record->patient_id)->first();


            $method_of_FP = [
                'modern' => ['Implant', 'UID', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
            ];

            $modern_methods = [];
            $traditional_methods = [];
            $previouslyMethods = $caseData['edit_previously_used_method'] ?? null;

            if ($previouslyMethods) {
                foreach ($caseData['edit_previously_used_method'] as $method) {
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
            if ($caseData['edit_choosen_method']) {
                $method_accepted = explode(",", $caseData['edit_choosen_method']);
            }

            $accept_modern_FP = [];
            foreach ($method_accepted as $method) {

                if (in_array($method, $method_of_FP['modern'])) {
                    $accept_modern_FP[] = $method;
                }
            }
            $converted_accepted_modern_FP = implode(",", $accept_modern_FP);

            $medical_case_record->patient->refresh();
            if ($patientData['edit_client_age'] >= 10) {
                $wra_masterlist_record->update([
                    'brgy_name' => $address->purok,
                    'name_of_wra' => $medical_case_record->patient->full_name,
                    'address' => $newAddress,
                    'age' => $patientData['edit_client_age'] ?? null,
                    'date_of_birth' => $patientData['edit_client_date_of_birth'] ?? $wra_masterlist_record->date_of_birth,
                    'SE_status' => ($caseData['edit_NHTS'] ?? null) === 'yes'
                        ? 'NHTS'
                        : (($caseData['edit_NHTS'] ?? null) !== null ? 'Yes' : 'No'),
                    'plan_to_have_more_children_yes' => ($caseData['edit_plan_to_have_more_children'] ?? null) === 'Yes'
                        ? collect([
                            $caseData['edit_new_acceptor_reason_for_FP'] ?? null,
                            $caseData['edit_current_user_reason_for_FP'] ?? null,
                            $caseData['edit_current_method_reason'] ?? null,
                        ])->first(fn($value) => !empty($value))
                        : null,
                    'plan_to_have_more_children_no' => ($caseData['edit_plan_to_have_more_children'] ?? null) === 'No' ? 'limiting' :  null,
                    'current_FP_methods' => ($caseData['edit_type_of_patient'] ?? null) === 'current user' ? $previoulyMethod : $wra_masterlist_record->current_FP_methods,
                    'modern_FP' => $converted_modern_methods ?? null,
                    'traditional_FP' =>  $converted_traditional_methods ?? null,
                    'currently_using_any_FP_method_no' => empty($caseData['edit_previously_used_method']) ? 'yes' : null,
                    'wra_accept_any_modern_FP_method' => $converted_accepted_modern_FP != null ? 'yes' : 'no',
                    'selected_modern_FP_method' => $converted_accepted_modern_FP ?? null,
                    'date_when_FP_method_accepted' =>
                    !empty($converted_accepted_modern_FP)
                        ? ($caseData['edit_date_of_acknowledgement']
                            ?? $wra_masterlist_record->date_when_FP_method_accepted)
                        : $wra_masterlist_record->date_when_FP_method_accepted,


                ]);
            }
            // update the selected method of side b
            $sideBrecord = family_planning_side_b_records::where('medical_record_case_id', $medical_case_record->id)->first();

            if ($sideBrecord) {
                $sideBrecord->update([
                    'method_accepted' => $caseData['edit_choosen_method'] ?? $sideBrecord->method_accepted,
                    'date_of_visit' =>  $caseData['edit_date_of_acknowledgement'] ?? $sideBrecord->date_of_visit
                ]);
            }

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
                'side_b_date_of_visit' => 'required|date',
                'side_b_medical_findings' => 'sometimes|nullable|string',
                'side_b_method_accepted' => 'required|string',
                'add_side_b_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_side_b_signature_data' => 'sometimes|nullable|string',
                'side_b_date_of_follow_up_visit' => 'required|date',
                'baby_Less_than_six_months_question' => 'sometimes|nullable|string',
                'sexual_intercouse_or_mesntrual_period_question' => 'sometimes|nullable|string',
                'baby_last_4_weeks_question' => 'sometimes|nullable|string',
                'menstrual_period_in_seven_days_question' => 'sometimes|nullable|string',
                'miscarriage_or_abortion_question' => 'sometimes|nullable|string',
                'contraceptive_question' => 'sometimes|nullable|string'
            ]);
            $sideBsignaturePath = null;

            // If user uploaded an image file
            if ($request->hasFile('add_side_b_signature_image')) {
                $sideBsignaturePath = $this->compressAndSaveSignature($request->file('add_side_b_signature_image'));
            }
            // If user drew a signature
            else if ($request->filled('add_side_b_signature_data')) {
                $sideBsignaturePath = $this->saveCanvasSignature($request->add_side_b_signature_data);
            }

            // add the data
            family_planning_side_b_records::create([
                'medical_record_case_id' => $data['side_b_medical_record_case_id'],
                'health_worker_id' => $data['side_b_health_worker_id'],
                'date_of_visit' => $data['side_b_date_of_visit'] ?? null,
                'medical_findings' => $data['side_b_medical_findings'] ?? null,
                'method_accepted' => $data['side_b_method_accepted'] ?? null,
                'signature_of_the_provider' => $sideBsignaturePath ?? null,
                'date_of_follow_up_visit' => $data['side_b_date_of_follow_up_visit'] ?? null,
                'baby_Less_than_six_months_question' => $data['baby_Less_than_six_months_question'] ?? null,
                'sexual_intercouse_or_mesntrual_period_question' => $data['sexual_intercouse_or_mesntrual_period_question'] ?? null,
                'baby_last_4_weeks_question' => $data['baby_last_4_weeks_question'] ?? null,
                'menstrual_period_in_seven_days_question' => $data['menstrual_period_in_seven_days_question'] ?? null,
                'miscarriage_or_abortion_question' => $data['miscarriage_or_abortion_question'] ?? null,
                'contraceptive_question' => $data['contraceptive_question'] ?? null,
                'status' => 'Active'

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
                'edit_side_b_date_of_visit' => 'required|date',
                'edit_side_b_medical_findings' => 'sometimes|nullable|string',
                'edit_side_b_method_accepted' => 'required|string',
                'edit_side_b_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'edit_side_b_signature_data' => 'sometimes|nullable|string',
                'edit_side_b_date_of_follow_up_visit' => 'sometimes|nullable|date',
                'edit_baby_Less_than_six_months_question' => 'sometimes|nullable|string',
                'edit_sexual_intercouse_or_mesntrual_period_question' => 'sometimes|nullable|string',
                'edit_baby_last_4_weeks_question' => 'sometimes|nullable|string',
                'edit_menstrual_period_in_seven_days_question' => 'sometimes|nullable|string',
                'edit_miscarriage_or_abortion_question' => 'sometimes|nullable|string',
                'edit_contraceptive_question' => 'sometimes|nullable|string'
            ]);

            $signaturePath = $sideBrecord->signature_of_the_provider;

            // Check if new signature provided (drawn)
            if ($request->filled('edit_side_b_signature_data')) {
                // Delete old file if exists
                if ($sideBrecord->signature_of_the_provider) {
                    Storage::disk('public')->delete($sideBrecord->signature_of_the_provider);
                }
                $signaturePath = $this->saveCanvasSignature($request->edit_side_b_signature_data);
            }
            // Check if new signature provided (uploaded)
            else if ($request->hasFile('edit_side_b_signature_image')) {
                // Delete old file if exists
                if ($sideBrecord->signature_of_the_provider) {
                    Storage::disk('public')->delete($sideBrecord->signature_of_the_provider);
                }
                $signaturePath = $this->compressAndSaveSignature($request->file('edit_side_b_signature_image'));
            }

            $sideBrecord->update([
                'medical_record_case_id' => $data['edit_side_b_medical_record_case_id'],
                'health_worker_id' => $data['edit_side_b_health_worker_id'],
                'date_of_visit' => $data['edit_side_b_date_of_visit'] ?? $sideBrecord->date_of_visit,
                'medical_findings' => $data['edit_side_b_medical_findings'] ?? null,
                'method_accepted' => $data['edit_side_b_method_accepted'] ?? null,
                'signature_of_the_provider' => $signaturePath ?? $sideBrecord->signature_of_the_provider,
                'date_of_follow_up_visit' => $data['edit_side_b_date_of_follow_up_visit'] ?? null,
                'baby_Less_than_six_months_question' => $data['edit_baby_Less_than_six_months_question'] ?? null,
                'sexual_intercouse_or_mesntrual_period_question' => $data['edit_sexual_intercouse_or_mesntrual_period_question'] ?? null,
                'baby_last_4_weeks_question' => $data['edit_baby_last_4_weeks_question'] ?? null,
                'menstrual_period_in_seven_days_question' => $data['edit_menstrual_period_in_seven_days_question'] ?? null,
                'miscarriage_or_abortion_question' => $data['edit_miscarriage_or_abortion_question'] ?? null,
                'contraceptive_question' => $data['edit_contraceptive_question'] ?? null,
                'status' => 'Active'
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
    public function removeRecord($type_of_record, $id)
    {
        if ($type_of_record == 'side-A') {
            try {
                $sideArecord = family_planning_case_records::where('id', $id)->first() ?? null;
                if (!$sideArecord) return;
                $sideArecord->update([
                    'status' => 'Archived'
                ]);
                // update the wra too
                $wraRecord = wra_masterlists::where('medical_record_case_id', $sideArecord->medical_record_case_id)->first() ?? null;
                if (!$wraRecord) return;

                $wraRecord->update([

                    'SE_status' => null,
                    'plan_to_have_more_children_yes' => null,
                    'plan_to_have_more_children_no' =>  null,
                    'current_FP_methods' => null,
                    'modern_FP' =>  null,
                    'traditional_FP' =>  null,
                    'currently_using_any_FP_method_no' => null,
                    'wra_accept_any_modern_FP_method' => null,
                    'selected_modern_FP_method' =>  null,
                    'date_when_FP_method_accepted' => null,

                ]);
                return response()->json([
                    'message' => 'Family Planning Client Assessment Record - Side A is deleted successfully'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ], 422);
            }
        }
        if ($type_of_record == 'side-B') {
            try {
                $sideBrecord = family_planning_side_b_records::where('id', $id)->first() ?? null;

                if (!$sideBrecord) return;

                $sideBrecord->update([
                    'status' => 'Archived'
                ]);

                return response()->json([
                    'message' => 'Record is deleted successfully.'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
                ], 422);
            }
        }
    }
    private function compressAndSaveSignature($file)
    {
        $filename = time() . '_' . uniqid() . '.jpg';
        $path = storage_path('app/public/signatures/family_planning/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/signatures/family_planning'))) {
            mkdir(storage_path('app/public/signatures/family_planning'), 0755, true);
        }

        // Process image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->scale(width: 800);
        $image->toJpeg(quality: 60);
        $image->save($path);

        return 'signatures/family_planning/' . $filename;
    }

    private function saveCanvasSignature($base64Data)
    {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);

        $filename = time() . '_' . uniqid() . '.jpg';
        $path = storage_path('app/public/signatures/family_planning/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/signatures/family_planning'))) {
            mkdir(storage_path('app/public/signatures/family_planning'), 0755, true);
        }

        // Process image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($imageData);
        $image->scale(width: 800);
        $image->toJpeg(quality: 60);
        $image->save($path);

        return 'signatures/family_planning/' . $filename;
    }
}
