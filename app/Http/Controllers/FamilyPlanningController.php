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
use App\Services\PatientUpdateService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
            $patientData = $request->validate([
                'patient_id'           => 'nullable|exists:patients,id',
                'type_of_patient'      => 'required',
                'first_name'           => [
                    'required',
                    'string',
                    Rule::unique('patients')->where(function ($query) use ($request) {
                        return $query->where('first_name', $request->first_name)
                            ->where('last_name', $request->last_name);
                    })->ignore($request->patient_id)
                ],
                'last_name'            => 'required|string',
                'middle_initial'       => 'sometimes|nullable|string',
                'date_of_birth'        => 'required|date|before_or_equal:today',
                'place_of_birth'       => 'sometimes|nullable|string',
                'age'                  => 'sometimes|nullable|numeric|min:10|max:49',
                'sex'                  => 'sometimes|nullable|string',
                'contact_number'       => 'required|digits_between:7,12',
                'nationality'          => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date|before_or_equal:today',
                'handled_by'           => 'required|exists:users,id',
                'street'               => 'required|string',
                'brgy'                 => 'required|string',
                'civil_status'         => 'sometimes|nullable|string',
                'suffix'               => 'sometimes|nullable|string',

                // Guardian account — optional, only used when notification_mode = guardian
                'guardian_account_id'  => 'nullable|exists:users,id',

                // Email: not required when guardian is linked or existing patient
                'email' => array_filter([
                    ($request->filled('guardian_account_id') || $request->filled('patient_id'))
                        ? 'nullable'
                        : 'required',
                    // Only validate format if email is actually provided
                    $request->filled('email') ? 'email' : null,
                    !$request->user_account && !$request->patient_id && !$request->filled('guardian_account_id')
                        ? Rule::unique('users', 'email')
                        : null,
                ]),

                'user_account'         => 'sometimes|nullable|numeric',
            ], [
                'patient_id.exists'                     => 'The selected patient record does not exist.',
                'type_of_patient.required'              => 'The type of patient field is required.',
                'first_name.required'                   => 'The first name field is required.',
                'first_name.string'                     => 'The first name must be a string.',
                'first_name.unique'                     => 'This patient already exists.',
                'last_name.required'                    => 'The last name field is required.',
                'last_name.string'                      => 'The last name must be a string.',
                'middle_initial.string'                 => 'The middle initial must be a string.',
                'date_of_birth.required'                => 'The date of birth field is required.',
                'date_of_birth.date'                    => 'The date of birth must be a valid date.',
                'date_of_birth.before_or_equal'         => 'The date of birth must be today or earlier.',
                'place_of_birth.string'                 => 'The place of birth must be a string.',
                'age.numeric'                           => 'The age must be a number.',
                'age.min'                               => 'The age must be at least :min.',
                'age.max'                               => 'The age may not be greater than :max.',
                'contact_number.required'               => 'The contact number field is required.',
                'contact_number.digits_between'         => 'The contact number must be between :min and :max digits.',
                'date_of_registration.required'         => 'The date of registration field is required.',
                'date_of_registration.date'             => 'The date of registration must be a valid date.',
                'handled_by.exists'                     => 'The selected health worker does not exist.',
                'handled_by_backup.exists'              => 'The selected health worker does not exist.',
                'guardian_account_id.exists'            => 'The selected guardian account does not exist.',
                'user_account.numeric'                  => 'The user account must be a number.',
                'date_of_registration.before_or_equal' => 'The date of registration must not be a future date.',
            ]);

            // ============================================================================
            // DETERMINE handled_by
            // ============================================================================
            $handledBy = $patientData['handled_by'] ?? $patientData['handled_by_backup'] ?? null;

            if (!$handledBy) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => ['handled_by' => ['The health worker field is required.']]
                ], 422);
            }

            // ============================================================================
            // DETERMINE notification mode
            // ============================================================================
            $guardianAccountId = $patientData['guardian_account_id'] ?? null;
            $isGuardianMode    = !empty($guardianAccountId);

            $medicalData = $request->validate([
                'religion'               => 'sometimes|nullable|string',
                'family_plan_occupation' => 'sometimes|nullable|string',
                'philhealth_no'          => ['sometimes', 'nullable', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
                'blood_pressure'         => ['sometimes', 'nullable', 'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'],
                'temperature'            => 'nullable|numeric|between:30,45',
                'pulse_rate'             => 'nullable|string|max:20',
                'respiratory_rate'       => 'nullable|integer|min:5|max:60',
                'height'                 => 'nullable|numeric|between:1,250',
                'weight'                 => 'nullable|numeric|between:1,250',
            ], [
                'family_plan_occupation.string' => 'The family plan occupation must be a string.',
                'philhealth_no.regex'           => 'The PhilHealth number format is invalid. Must be in format: XX-XXXXXXXXX-X',
                'blood_pressure.regex'          => 'The blood pressure format is invalid.',
                'pulse_rate.string'             => 'The pulse rate must be a string.',
                'pulse_rate.max'                => 'The pulse rate may not be greater than :max characters.',
                'respiratory_rate.integer'      => 'The respiratory rate must be an integer.',
                'respiratory_rate.min'          => 'The respiratory rate must be at least :min.',
                'respiratory_rate.max'          => 'The respiratory rate may not be greater than :max.',
            ]);

            $caseData = $request->validate([
                'client_id'                                        => 'sometimes|nullable|string',
                'philhealth_no'                                    => ['sometimes', 'nullable', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
                'NHTS'                                             => 'sometimes|nullable|string',
                'spouse_lname'                                     => 'sometimes|nullable|string',
                'spouse_fname'                                     => 'sometimes|nullable|string',
                'spouse_MI'                                        => 'sometimes|nullable|string|max:2',
                'spouse_date_of_birth'                             => 'sometimes|nullable|date|before_or_equal:today',
                'spouse_age'                                       => 'sometimes|nullable|numeric|max:100',
                'spouse_occupation'                                => 'sometimes|nullable|string',
                'spouse_suffix'                                    => 'sometimes|nullable|string',
                'number_of_living_children'                        => 'sometimes|nullable|numeric|max:50',
                'plan_to_have_more_children'                       => 'sometimes|nullable|string',
                'average_montly_income'                            => 'sometimes|nullable|numeric',
                'family_planning_type_of_patient'                  => 'sometimes|nullable|string',
                'new_acceptor_reason_for_FP'                       => 'sometimes|nullable|string',
                'current_user_reason_for_FP'                       => 'sometimes|nullable|string',
                'current_method_reason'                            => 'sometimes|nullable|string',
                'new_acceptor_reason_text'    => 'sometimes|nullable|string|max:255',
                'current_user_reason_text'    => 'sometimes|nullable|string|max:255',
                'current_method_reason_text'  => 'sometimes|nullable|string|max:255',
                'previously_used_method'                           => 'sometimes|nullable|array',
                'choosen_method' => [
                    'sometimes',
                    'nullable',
                    'string',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) return;

                        $allowedMethods = [
                            'Implant',
                            'Injectable',
                            'LAM',
                            'IUD',
                            'COC',
                            'SDM',
                            'BTL',
                            'POP',
                            'BBT',
                            'NSV',
                            'Condom',
                            'BOM/CMM/STM',
                        ];

                        $submitted = array_map('trim', explode(',', $value));

                        $allowedLower = array_map('strtolower', $allowedMethods);

                        foreach ($submitted as $method) {
                            if (!in_array(strtolower($method), $allowedLower)) {
                                $fail("Invalid method \"{$method}\". Allowed methods are: " . implode(', ', $allowedMethods) . '.');
                                return;
                            }
                        }
                    },
                ],
                'add_family_planning_signature_image'              => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_family_planning_signature_data'               => 'sometimes|nullable|string',
                'family_planning_date_of_acknowledgement'          => 'sometimes|nullable|date|before_or_equal:today',
                'add_family_planning_consent_signature_image'      => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_family_planning_consent_signature_data'       => 'sometimes|nullable|string',
                'family_planning_date_of_acknowledgement_consent'  => 'sometimes|nullable|date|before_or_equal:today',
                'current_user_type'                                => 'sometimes|nullable|string',
            ], [
                'client_id.string'                                     => 'The client ID must be a string.',
                'philhealth_no.regex'                                   => 'The PhilHealth number format is invalid. Must be in format: XX-XXXXXXXXX-X',
                'spouse_lname.string'                                   => 'The spouse last name must be a string.',
                'spouse_fname.string'                                   => 'The spouse first name must be a string.',
                'spouse_MI.string'                                      => 'The spouse middle initial must be a string.',
                'spouse_MI.max'                                         => 'The spouse middle initial may not be greater than :max characters.',
                'spouse_date_of_birth.date'                             => 'The spouse date of birth must be a valid date.',
                'spouse_date_of_birth.before_or_equal'                  => 'The spouse date of birth must be today or earlier.',
                'spouse_age.numeric'                                    => 'The spouse age must be a number.',
                'spouse_age.max'                                        => 'The spouse age may not be greater than :max.',
                'number_of_living_children.numeric'                     => 'The number of living children must be a number.',
                'number_of_living_children.max'                         => 'The number of living children may not be greater than :max.',
                'average_montly_income.numeric'                         => 'The average monthly income must be a number.',
                'family_planning_type_of_patient.string'                => 'The family planning type of patient must be a string.',
                'new_acceptor_reason_for_FP.string'                     => 'The new acceptor reason for FP must be a string.',
                'current_user_reason_for_FP.string'                     => 'The current user reason for FP must be a string.',
                'add_family_planning_signature_image.image'             => 'The signature must be an image.',
                'add_family_planning_signature_image.mimes'             => 'The signature must be a file of type: jpg, jpeg, png.',
                'add_family_planning_signature_image.max'               => 'The signature may not be greater than :max kilobytes.',
                'family_planning_date_of_acknowledgement.date'          => 'The date of acknowledgement must be a valid date.',
                'add_family_planning_consent_signature_image.image'     => 'The consent signature must be an image.',
                'add_family_planning_consent_signature_image.mimes'     => 'The consent signature must be a file of type: jpg, jpeg, png.',
                'add_family_planning_consent_signature_image.max'       => 'The consent signature may not be greater than :max kilobytes.',
                'family_planning_date_of_acknowledgement_consent.date'  => 'The date of acknowledgement consent must be a valid date.',
                'family_planning_date_of_acknowledgement.before_or_equal' => 'The date of acknowledgement must not be a future date.',
                'family_planning_date_of_acknowledgement_consent.before_or_equal' => 'The date of acknowledgement consent must not be a future date.',
                'new_acceptor_reason_text.max'   => 'The new acceptor reason detail may not exceed :max characters.',
                'current_user_reason_text.max'   => 'The current user reason detail may not exceed :max characters.',
                'current_method_reason_text.max' => 'The side effects detail may not exceed :max characters.',
            ]);

            $medicalHistoryData = $request->validate([
                'medical_history_severe_headaches_migraine'       => 'sometimes|nullable|string',
                'medical_history_history_of_stroke'               => 'sometimes|nullable|string',
                'medical_history_non_traumatic_hemtoma'           => 'sometimes|nullable|string',
                'medical_history_history_of_breast_cancer'        => 'sometimes|nullable|string',
                'medical_history_severe_chest_pain'               => 'sometimes|nullable|string',
                'medical_history_cough'                           => 'sometimes|nullable|string',
                'medical_history_jaundice'                        => 'sometimes|nullable|string',
                'medical_history_unexplained_vaginal_bleeding'    => 'sometimes|nullable|string',
                'medical_history_abnormal_vaginal_discharge'      => 'sometimes|nullable|string',
                'medical_history_abnormal_phenobarbital'          => 'sometimes|nullable|string',
                'medical_history_smoker'                          => 'sometimes|nullable|string',
                'medical_history_with_dissability'                => 'sometimes|nullable|string',
                'if_with_dissability_specification'               => 'sometimes|nullable|string',
            ], [
                'medical_history_severe_headaches_migraine.string'    => 'The severe headaches/migraine field must be a string.',
                'medical_history_history_of_stroke.string'            => 'The history of stroke field must be a string.',
                'medical_history_non_traumatic_hemtoma.string'        => 'The non-traumatic hematoma field must be a string.',
                'medical_history_history_of_breast_cancer.string'     => 'The history of breast cancer field must be a string.',
                'medical_history_severe_chest_pain.string'            => 'The severe chest pain field must be a string.',
                'medical_history_unexplained_vaginal_bleeding.string' => 'The unexplained vaginal bleeding field must be a string.',
                'medical_history_abnormal_vaginal_discharge.string'   => 'The abnormal vaginal discharge field must be a string.',
                'medical_history_abnormal_phenobarbital.string'       => 'The abnormal phenobarbital field must be a string.',
                'medical_history_with_dissability.string'             => 'The with disability field must be a string.',
                'if_with_dissability_specification.string'            => 'The disability specification field must be a string.',
            ]);

            $obstericalHistoryData = $request->validate([
                'family_planning_G'                                          => 'sometimes|nullable|numeric|max:20',
                'family_planning_P'                                          => 'sometimes|nullable|numeric|max:20',
                'family_planning_full_term'                                  => 'sometimes|nullable|numeric|max:20',
                'family_planning_abortion'                                   => 'sometimes|nullable|numeric|max:20',
                'family_planning_premature'                                  => 'sometimes|nullable|numeric|max:20',
                'family_planning_living_children'                            => 'sometimes|nullable|numeric|max:20',
                'family_planning_date_of_last_delivery'                      => 'sometimes|nullable|date|before_or_equal:today',
                'family_planning_type_of_last_delivery'                      => 'sometimes|nullable|string',
                'family_planning_date_of_last_delivery_menstrual_period'     => 'sometimes|nullable|date|before_or_equal:today',
                'family_planning_date_of_previous_delivery_menstrual_period' => 'sometimes|nullable|date|before_or_equal:today',
                'family_planning_type_of_menstrual'                          => 'sometimes|nullable|string',
                'family_planning_Dysmenorrhea'                               => 'sometimes|nullable|string',
                'family_planning_hydatidiform_mole'                          => 'sometimes|nullable|string',
                'family_planning_ectopic_pregnancy'                          => 'sometimes|nullable|string',
            ], [
                'family_planning_G.numeric'                                             => 'The G (gravida) must be a number.',
                'family_planning_G.max'                                                 => 'The G (gravida) may not be greater than :max.',
                'family_planning_P.numeric'                                             => 'The P (para) must be a number.',
                'family_planning_P.max'                                                 => 'The P (para) may not be greater than :max.',
                'family_planning_full_term.numeric'                                     => 'The full term must be a number.',
                'family_planning_full_term.max'                                         => 'The full term may not be greater than :max.',
                'family_planning_abortion.numeric'                                      => 'The abortion count must be a number.',
                'family_planning_abortion.max'                                          => 'The abortion count may not be greater than :max.',
                'family_planning_premature.numeric'                                     => 'The premature count must be a number.',
                'family_planning_premature.max'                                         => 'The premature count may not be greater than :max.',
                'family_planning_living_children.numeric'                               => 'The living children count must be a number.',
                'family_planning_living_children.max'                                   => 'The living children count may not be greater than :max.',
                'family_planning_date_of_last_delivery.date'                            => 'Please enter a valid date of last delivery.',
                'family_planning_date_of_last_delivery.before_or_equal'                 => 'The date of last delivery cannot be a future date.',
                'family_planning_date_of_last_delivery_menstrual_period.date'           => 'Please enter a valid last menstrual period date.',
                'family_planning_date_of_last_delivery_menstrual_period.before_or_equal' => 'The last menstrual period date cannot be a future date.',
                'family_planning_date_of_previous_delivery_menstrual_period.date'           => 'Please enter a valid previous menstrual period date.',
                'family_planning_date_of_previous_delivery_menstrual_period.before_or_equal' => 'The previous menstrual period date cannot be a future date.',
                'family_planning_type_of_menstrual.string'                              => 'The type of menstrual must be a valid text value.',
            ]);

            $riskData = $request->validate([
                'infection_abnormal_discharge_from_genital_area' => 'sometimes|nullable|string',
                'origin_of_abnormal_discharge'                   => 'sometimes|nullable|string',
                'scores_or_ulcer'                                => 'sometimes|nullable|string',
                'pain_or_burning_sensation'                      => 'sometimes|nullable|string',
                'history_of_sexually_transmitted_infection'      => 'sometimes|nullable|string',
                'sexually_transmitted_disease'                   => 'sometimes|nullable|string',
                'history_of_domestic_violence_of_VAW'            => 'sometimes|nullable|string',
                'unpleasant_relationship_with_partner'           => 'sometimes|nullable|string',
                'partner_does_not_approve'                       => 'sometimes|nullable|string',
                'referred_to'                                    => 'sometimes|nullable|string',
                'reffered_to_others'                             => 'sometimes|nullable|string',
            ], [
                'infection_abnormal_discharge_from_genital_area.string' => 'The abnormal discharge from genital area field must be a string.',
                'origin_of_abnormal_discharge.string'                   => 'The origin of abnormal discharge field must be a string.',
                'scores_or_ulcer.string'                                => 'The sores or ulcer field must be a string.',
                'pain_or_burning_sensation.string'                      => 'The pain or burning sensation field must be a string.',
                'history_of_sexually_transmitted_infection.string'      => 'The history of sexually transmitted infection field must be a string.',
                'sexually_transmitted_disease.string'                   => 'The sexually transmitted disease field must be a string.',
                'history_of_domestic_violence_of_VAW.string'            => 'The history of domestic violence of VAW field must be a string.',
                'unpleasant_relationship_with_partner.string'           => 'The unpleasant relationship with partner field must be a string.',
                'partner_does_not_approve.string'                       => 'The partner does not approve field must be a string.',
                'reffered_to_others.string'                             => 'The referred to others field must be a string.',
            ]);

            $physicalExaminationData = $request->validate([
                'physical_examination_skin_type'           => 'sometimes|nullable|string',
                'physical_examination_conjuctiva_type'     => 'sometimes|nullable|string',
                'physical_examination_breast_type'         => 'sometimes|nullable|string',
                'physical_examination_abdomen_type'        => 'sometimes|nullable|string',
                'physical_examination_extremites_type'     => 'sometimes|nullable|string',
                'physical_examination_extremites_UID_type' => 'sometimes|nullable|string',
                'cervical_abnormalities_type'              => 'sometimes|nullable|string',
                'cervical_consistency_type'                => 'sometimes|nullable|string',
                'uterine_position_type'                    => 'sometimes|nullable|string',
                'uterine_depth_text'                       => 'sometimes|nullable|numeric',
                'physical_examination_neck_type'           => 'sometimes|nullable|string',
            ], [
                'physical_examination_skin_type.string'           => 'The skin type field must be a string.',
                'physical_examination_conjuctiva_type.string'     => 'The conjunctiva type field must be a string.',
                'physical_examination_breast_type.string'         => 'The breast type field must be a string.',
                'physical_examination_abdomen_type.string'        => 'The abdomen type field must be a string.',
                'physical_examination_extremites_type.string'     => 'The extremities type field must be a string.',
                'physical_examination_extremites_UID_type.string' => 'The extremities IUD type field must be a string.',
                'cervical_abnormalities_type.string'              => 'The cervical abnormalities type field must be a string.',
                'cervical_consistency_type.string'                => 'The cervical consistency type field must be a string.',
                'uterine_position_type.string'                    => 'The uterine position type field must be a string.',
                'uterine_depth_text.numeric'                      => 'The uterine depth must be a number.',
                'physical_examination_neck_type.string'           => 'The neck type field must be a string.',
            ]);

            $sideBdata = $request->validate([
                'side_b_date_of_visit'                           => 'required|date|before_or_equal:today',
                'side_b_medical_findings'                        => 'sometimes|nullable|string',
                'side_b_method_accepted'                         => 'sometimes|nullable|array',
                'side_b_method_accepted.*'                       => 'string',
                'add_side_b_name_n_signature_image'              => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_side_b_name_n_signature_data'               => 'sometimes|nullable|string',
                'side_b_date_of_follow_up_visit'                 => [
                    'required',
                    'date',
                    'before_or_equal:' . now()->addYear()->toDateString(),
                ],
                'baby_Less_than_six_months_question'             => 'sometimes|nullable|string',
                'sexual_intercouse_or_mesntrual_period_question' => 'sometimes|nullable|string',
                'baby_last_4_weeks_question'                     => 'sometimes|nullable|string',
                'menstrual_period_in_seven_days_question'        => 'sometimes|nullable|string',
                'miscarriage_or_abortion_question'               => 'sometimes|nullable|string',
                'contraceptive_question'                         => 'sometimes|nullable|string',
            ], [
                'side_b_date_of_visit.required'                         => 'The date of visit is required.',
                'side_b_date_of_visit.date'                             => 'Please enter a valid date of visit.',
                'side_b_date_of_visit.before_or_equal'                  => 'The date of visit cannot be a future date.',
                'side_b_medical_findings.string'                        => 'The medical findings must be a valid text value.',
                'side_b_method_accepted.array'                          => 'The method accepted must be a list of selected methods.',
                'add_side_b_name_n_signature_image.image'               => 'The signature must be an image file.',
                'add_side_b_name_n_signature_image.mimes'               => 'The signature must be a jpg, jpeg, or png file.',
                'add_side_b_name_n_signature_image.max'                 => 'The signature image may not exceed :max kilobytes.',
                'side_b_date_of_follow_up_visit.required'               => 'The date of follow up visit is required.',
                'side_b_date_of_follow_up_visit.date'                   => 'Please enter a valid follow up visit date.',
                'side_b_date_of_follow_up_visit.before_or_equal'        => 'The follow up visit date cannot be more than 1 year in the future.',
                'baby_Less_than_six_months_question.string'             => 'The baby less than six months field must be a valid text value.',
                'sexual_intercouse_or_mesntrual_period_question.string' => 'The sexual intercourse or menstrual period field must be a valid text value.',
                'baby_last_4_weeks_question.string'                     => 'The baby last 4 weeks field must be a valid text value.',
                'menstrual_period_in_seven_days_question.string'        => 'The menstrual period in seven days field must be a valid text value.',
                'miscarriage_or_abortion_question.string'               => 'The miscarriage or abortion field must be a valid text value.',
            ]);

            // ============================================================================
            // HANDLE EXISTING PATIENT RECORD
            // ============================================================================
            if ($request->filled('patient_id')) {

                $familPlanningPatient = patients::with('address')->findOrFail($patientData['patient_id']);

                if (empty($familPlanningPatient->sex) || strtolower($familPlanningPatient->sex) !== 'female') {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors'  => ['patient_id' => ['Only female patients can be registered for a famiy planning case.']]
                    ], 422);
                }

                $existingCase = medical_record_cases::where('patient_id', $familPlanningPatient->id)
                    ->where('type_of_case', $patientData['type_of_patient'])
                    ->where('status', 'Active')
                    ->first();

                if ($existingCase) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors'  => ['type_of_patient' => ['This patient already has an active Family Planning case.']]
                    ], 422);
                }
                

                $middle     = substr($patientData['middle_initial'] ?? '', 0, 1);
                $middle     = $middle ? strtoupper($middle) . '.' : null;
                $middleName = $patientData['middle_initial'] ? ucwords(strtolower($patientData['middle_initial'])) : '';
                $fullName   = ucwords(trim(implode(' ', array_filter([
                    strtolower($patientData['first_name']),
                    $middle,
                    strtolower($patientData['last_name']),
                    $patientData['suffix'] ?? null,
                ]))));

                $familPlanningPatient->update([
                    'first_name'           => ucwords(strtolower($patientData['first_name'])),
                    'middle_initial'       => $middleName,
                    'last_name'            => ucwords(strtolower($patientData['last_name'])),
                    'full_name'            => $fullName,
                    'suffix'               => $patientData['suffix'] ?? '',
                    'contact_number'       => $patientData['contact_number'],
                    'nationality'          => $patientData['nationality'] ?? $familPlanningPatient->nationality,
                    'date_of_birth'        => $patientData['date_of_birth'],
                    'age'                  => isset($patientData['date_of_birth']) ? \Carbon\Carbon::parse($patientData['date_of_birth'])->age : $familPlanningPatient->age,
                    'place_of_birth'       => $patientData['place_of_birth'] ?? $familPlanningPatient->place_of_birth,
                    'civil_status'         => $patientData['civil_status'] ?? $familPlanningPatient->civil_status,
                    'date_of_registration' => $patientData['date_of_registration'],
                ]);

                $blk_n_street = explode(',', $patientData['street']);
                $patientAddress = $familPlanningPatient->address;

                if (!$patientAddress) {
                    return response()->json([
                        'message' => 'Patient address not found.',
                        'errors'  => ['patient_id' => ['The selected patient does not have an address record.']]
                    ], 422);
                }

                $patientAddress->update([
                    'house_number' => $blk_n_street[0],
                    'street'       => $blk_n_street[1] ?? null,
                    'purok'        => $patientData['brgy'],
                ]);

                $patientAddress->refresh();
                $fullAddress = collect([
                    $patientAddress->house_number,
                    $patientAddress->street,
                    $patientAddress->purok,
                    $patientAddress->barangay ?? null,
                    $patientAddress->city ?? null,
                    $patientAddress->province ?? null,
                ])->filter()->join(', ');

                $familyPlanningPatientRecordId = $familPlanningPatient->id;
                $message = 'Family Planning case added to existing patient successfully.';
            } else {
                // ============================================================================
                // CREATE NEW PATIENT RECORD
                // ============================================================================

                $middle     = substr($patientData['middle_initial'] ?? '', 0, 1);
                $middle     = $middle ? strtoupper($middle) . '.' : null;
                $middleName = $patientData['middle_initial'] ? ucwords(strtolower($patientData['middle_initial'])) : '';
                $parts      = [
                    strtolower($patientData['first_name']),
                    $middle,
                    strtolower($patientData['last_name']),
                    $patientData['suffix'] ?? null,
                ];
                $blk_n_street = explode(',', $patientData['street']);
                $fullName     = ucwords(trim(implode(' ', array_filter($parts))));

                // Validate user account matching (only if patient account linked, not guardian mode)
                if ($patientData['user_account'] && !$isGuardianMode) {
                    $errors = [];

                    try {
                        $user = User::with('user_address')->findOrFail((int)$patientData['user_account']);

                        if ($user->email != $patientData['email']) {
                            $errors['email'] = ["Patient Account email doesn't match the email input value."];
                        }

                        if (isset($blk_n_street[0]) && $blk_n_street[0] != $user->user_address->house_number) {
                            $errors['street'] = ["House number doesn't match the patient account records."];
                        }

                        if (isset($blk_n_street[1]) && !empty(trim($blk_n_street[1]))) {
                            if (trim($blk_n_street[1]) != $user->user_address->street) {
                                if (!isset($errors['street'])) $errors['street'] = [];
                                $errors['street'][] = "Street doesn't match the patient account records.";
                            }
                        }

                        if ($patientData['brgy'] != $user->user_address->purok) {
                            $errors['brgy'] = ["Barangay doesn't match the patient account records."];
                        }

                        if (!empty($errors)) {
                            return response()->json([
                                'message' => 'The given data does not match our records.',
                                'errors'  => $errors
                            ], 422);
                        }
                    } catch (ModelNotFoundException $e) {
                        return response()->json([
                            'message' => 'Patient account not found.',
                            'errors'  => ['user_account' => ['The selected patient account does not exist.']]
                        ], 404);
                    }
                }

                // Create patient record
                $familPlanningPatient = patients::create([
                    'user_id'              => null,
                    'guardian_user_id'     => $isGuardianMode ? $guardianAccountId : null, // NEW
                    'first_name'           => ucwords(strtolower($patientData['first_name'])),
                    'middle_initial'       => $middleName,
                    'last_name'            => ucwords(strtolower($patientData['last_name'])),
                    'full_name'            => $fullName,
                    'age'                  => isset($patientData['date_of_birth']) ? \Carbon\Carbon::parse($patientData['date_of_birth'])->age : null,
                    'sex'                  => 'Female',
                    'civil_status'         => $patientData['civil_status'] ?? null,
                    'contact_number'       => $patientData['contact_number'] ?? null,
                    'date_of_birth'        => $patientData['date_of_birth'] ?? null,
                    'profile_image'        => 'images/default_profile.png',
                    'nationality'          => $patientData['nationality'] ?? null,
                    'date_of_registration' => $patientData['date_of_registration'] ?? null,
                    'place_of_birth'       => $patientData['place_of_birth'] ?? null,
                    'suffix'               => $patientData['suffix'] ?? '',
                    'status'               => 'Active',
                ]);

                // ----------------------------------------------------------------
                // ACCOUNT HANDLING: Guardian mode vs Patient account vs New account
                // ----------------------------------------------------------------
                if ($isGuardianMode) {
                    // GUARDIAN MODE: no user account created for the patient
                    // guardian_user_id already set above on the patient record
                    // Notifications will go to the guardian's account

                } elseif ($patientData['user_account']) {
                    // EXISTING USER ACCOUNT: link and update
                    try {
                        $user = User::with('user_address')->findOrFail((int)$patientData['user_account']);

                        $user->update([
                            'patient_record_id' => $familPlanningPatient->id,
                            'first_name'        => ucwords(strtolower($patientData['first_name'])),
                            'middle_initial'    => $middleName,
                            'last_name'         => ucwords(strtolower($patientData['last_name'])),
                            'full_name'         => $fullName,
                            'email'             => $patientData['email'],
                            'contact_number'    => $patientData['contact_number'] ?? null,
                            'date_of_birth'     => $patientData['date_of_birth'] ?? null,
                            'suffix'            => $patientData['suffix'] ?? null,
                            'patient_type'      => $patientData['type_of_patient'],
                            'role'              => 'patient',
                            'status'            => 'active',
                        ]);

                        $familPlanningPatient->update(['user_id' => $user->id]);

                        if ($user->user_address) {
                            $user->user_address->update([
                                'patient_id'   => $familPlanningPatient->id,
                                'house_number' => $blk_n_street[0],
                                'street'       => $blk_n_street[1] ?? null,
                                'purok'        => $patientData['brgy'],
                            ]);
                        } else {
                            $user->user_address()->create([
                                'patient_id'   => $familPlanningPatient->id,
                                'house_number' => $blk_n_street[0],
                                'street'       => $blk_n_street[1] ?? null,
                                'purok'        => $patientData['brgy'],
                            ]);
                        }
                    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                        return response()->json([
                            'message' => 'Patient account not found.',
                            'errors'  => ['user_account' => ['The selected patient account does not exist.']]
                        ], 404);
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'An error occurred while updating patient information.',
                            'errors'  => ['server' => ['Please try again or contact support.']]
                        ], 500);
                    }
                } else {
                    // NEW ACCOUNT: create fresh user account and send credentials
                    $temporaryPassword = $this->generateSecurePassword(8);
                    try {
                        $user = User::create([
                            'patient_record_id' => $familPlanningPatient->id,
                            'first_name'        => ucwords(strtolower($patientData['first_name'])),
                            'middle_initial'    => $middleName,
                            'last_name'         => ucwords(strtolower($patientData['last_name'])),
                            'full_name'         => $fullName,
                            'email'             => $patientData['email'],
                            'contact_number'    => $patientData['contact_number'] ?? null,
                            'date_of_birth'     => $patientData['date_of_birth'] ?? null,
                            'suffix'            => $patientData['suffix'] ?? null,
                            'patient_type'      => $patientData['type_of_patient'],
                            'password'          => Hash::make($temporaryPassword),
                            'role'              => 'patient',
                            'status'            => 'active',
                        ]);

                        $familPlanningPatient->update(['user_id' => $user->id]);

                        Mail::to($user->email)->send(new PatientAccountCreated($user, $temporaryPassword));

                        $user->user_address()->create([
                            'patient_id'   => $familPlanningPatient->id,
                            'house_number' => $blk_n_street[0],
                            'street'       => $blk_n_street[1] ?? null,
                            'purok'        => $patientData['brgy'],
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'An error occurred while creating patient account.',
                            'errors'  => ['server' => ['Please try again or contact support.']]
                        ], 500);
                    }
                }

                $familyPlanningPatientRecordId = $familPlanningPatient->id;

                $patientAddress = patient_addresses::create([
                    'patient_id'   => $familyPlanningPatientRecordId,
                    'house_number' => $blk_n_street[0] ?? $patientData['blk_n_street'],
                    'street'       => $blk_n_street[1] ?? null,
                    'purok'        => $patientData['brgy'],
                    'postal_code'  => '4109',
                    'latitude'     => null,
                    'longitude'    => null,
                ]);

                $patientAddress->refresh();

                $fullAddress = collect([
                    $patientAddress->house_number,
                    $patientAddress->street,
                    $patientAddress->purok,
                    $patientAddress->barangay ?? null,
                    $patientAddress->city ?? null,
                    $patientAddress->province ?? null,
                ])->filter()->join(', ');

                $message = 'Family Planning patient information added successfully.';
            }

            // ============================================================================
            // CREATE MEDICAL CASE RECORD (Common for both new and existing patients)
            // ============================================================================

            $medicalCase = medical_record_cases::create([
                'patient_id'   => $familyPlanningPatientRecordId,
                'type_of_case' => $patientData['type_of_patient'],
                'status'       => 'Active',
                'date_of_registration' => $patientData['date_of_registration'],
            ]);

            $medicalCaseId = $medicalCase->id;

            family_planning_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id'       => $handledBy,
                'patient_name'           => $familPlanningPatient->full_name,
                'occupation'             => $medicalData['family_plan_occupation'] ?? null,
                'religion'               => $medicalData['religion'] ?? null,
                'philhealth_no'          => $medicalData['philhealth_no'] ?? null,
                'blood_pressure'         => $medicalData['blood_pressure'] ?? null,
                'temperature'            => $medicalData['temperature'] ?? null,
                'pulse_rate'             => $medicalData['pulse_rate'] ?? null,
                'respiratory_rate'       => $medicalData['respiratory_rate'] ?? null,
                'height'                 => $medicalData['height'] ?? null,
                'weight'                 => $medicalData['weight'] ?? null,
            ]);

            $methods         = $caseData['previously_used_method'] ?? null;
            $previoulyMethod = null;
            if ($methods) {
                $previoulyMethod = implode(",", $caseData['previously_used_method'] ?? []);
            }

            $signaturePath        = null;
            $signatureConsentPath = null;

            if ($request->hasFile('add_family_planning_signature_image')) {
                $signaturePath = $this->compressAndSaveSignature($request->file('add_family_planning_signature_image'));
            } elseif ($request->filled('add_family_planning_signature_data')) {
                $signaturePath = $this->saveCanvasSignature($request->add_family_planning_signature_data);
            }

            if ($request->hasFile('add_family_planning_consent_signature_image')) {
                $signatureConsentPath = $this->compressAndSaveSignature($request->file('add_family_planning_consent_signature_image'));
            } elseif ($request->filled('add_family_planning_consent_signature_data')) {
                $signatureConsentPath = $this->saveCanvasSignature($request->add_family_planning_consent_signature_data);
            }

            $allowedMethods = [
                'Implant',
                'Injectable',
                'LAM',
                'IUD',
                'COC',
                'SDM',
                'BTL',
                'POP',
                'BBT',
                'NSV',
                'Condom',
                'BOM/CMM/STM',
            ];
            $allowedMethodsMap = array_combine(
                array_map('strtolower', $allowedMethods),
                $allowedMethods
            );

            $normalizedChoosenMethod = null;
            if (!empty($caseData['choosen_method'])) {
                $submitted = array_map('trim', explode(',', $caseData['choosen_method']));
                $normalized = array_map(fn($m) => $allowedMethodsMap[strtolower($m)] ?? $m, $submitted);
                $normalizedChoosenMethod = implode(', ', $normalized);
            }

            $newAcceptorReasonRadio = $caseData['new_acceptor_reason_for_FP'] ?? null;
            $newAcceptorReasonText  = trim($caseData['new_acceptor_reason_text'] ?? '');
            $newAcceptorReasonFinal = ($newAcceptorReasonRadio === 'others' && $newAcceptorReasonText !== '')
                ? 'others: ' . $newAcceptorReasonText
                : $newAcceptorReasonRadio;

            $currentUserReasonRadio = $caseData['current_user_reason_for_FP'] ?? null;
            $currentUserReasonText  = trim($caseData['current_user_reason_text'] ?? '');
            $currentUserReasonFinal = ($currentUserReasonRadio === 'others' && $currentUserReasonText !== '')
                ? 'others: ' . $currentUserReasonText
                : $currentUserReasonRadio;

            $currentMethodReasonRadio = $caseData['current_method_reason'] ?? null;
            $currentMethodReasonText  = trim($caseData['current_method_reason_text'] ?? '');
            $currentMethodReasonFinal = ($currentMethodReasonRadio === 'side effects' && $currentMethodReasonText !== '')
                ? 'side effects: ' . $currentMethodReasonText
                : $currentMethodReasonRadio;

            $caseRecord = family_planning_case_records::create([
                'medical_record_case_id'                  => $medicalCaseId,
                'health_worker_id'                        => $handledBy,
                'client_id'                               => $caseData['client_id'] ?? null,
                'philhealth_no'                           => $caseData['philhealth_no'] ?? null,
                'NHTS'                                    => $caseData['NHTS'] ?? null,
                'client_name'                             => $familPlanningPatient->full_name,
                'client_first_name'                       => $familPlanningPatient->first_name,
                'client_last_name'                        => $familPlanningPatient->last_name,
                'client_middle_name'                      => $familPlanningPatient->middle_initial,
                'client_date_of_birth'                    => $familPlanningPatient->date_of_birth ?? null,
                'client_age'                              => $familPlanningPatient->age ?? null,
                'occupation'                              => $medicalData['family_plan_occupation'] ?? null,
                'client_address'                          => $fullAddress,
                'client_contact_number'                   => $familPlanningPatient->contact_number ?? null,
                'client_civil_status'                     => $familPlanningPatient->civil_status ?? null,
                'client_religion'                         => $medicalData['religion'] ?? null,
                'client_suffix'                           => $familPlanningPatient->suffix ?? '',
                'spouse_lname'                            => $caseData['spouse_lname'] ?? null,
                'spouse_fname'                            => $caseData['spouse_fname'] ?? null,
                'spouse_MI'                               => $caseData['spouse_MI'] ?? null,
                'spouse_date_of_birth'                    => $caseData['spouse_date_of_birth'] ?? null,
                'spouse_age'                              => $caseData['spouse_age'] ?? null,
                'spouse_occupation'                       => $caseData['spouse_occupation'] ?? null,
                'spouse_suffix'                           => $caseData['spouse_suffix'] ?? '',
                'number_of_living_children'               => $caseData['number_of_living_children'] ?? null,
                'plan_to_have_more_children'              => $caseData['plan_to_have_more_children'] ?? null,
                'average_montly_income'                   => $caseData['average_montly_income'] ?? null,
                'type_of_patient'                         => $caseData['family_planning_type_of_patient'] ?? null,
                'new_acceptor_reason_for_FP' => $newAcceptorReasonFinal,
                'current_user_reason_for_FP' => $currentUserReasonFinal,
                'current_method_reason'      => $currentMethodReasonFinal,
                'previously_used_method'                  => $previoulyMethod ?? null,
                'choosen_method' => $normalizedChoosenMethod,
                'signature_image'                         => $signaturePath ?? null,
                'date_of_acknowledgement'                 => $caseData['family_planning_date_of_acknowledgement'] ?? null,
                'acknowledgement_consent_signature_image' => $signatureConsentPath ?? null,
                'date_of_acknowledgement_consent'         => $caseData['family_planning_date_of_acknowledgement_consent'] ?? null,
                'current_user_type'                       => $caseData['current_user_type'] ?? null,
                'status'                                  => 'Active',
            ]);

            $caseId = $caseRecord->id;

            family_planning_medical_histories::create([
                'case_id'                           => $caseId,
                'severe_headaches_migraine'         => $medicalHistoryData['medical_history_severe_headaches_migraine'] ?? null,
                'history_of_stroke'                 => $medicalHistoryData['medical_history_history_of_stroke'] ?? null,
                'non_traumatic_hemtoma'             => $medicalHistoryData['medical_history_non_traumatic_hemtoma'] ?? null,
                'history_of_breast_cancer'          => $medicalHistoryData['medical_history_history_of_breast_cancer'] ?? null,
                'severe_chest_pain'                 => $medicalHistoryData['medical_history_severe_chest_pain'] ?? null,
                'cough'                             => $medicalHistoryData['medical_history_cough'] ?? null,
                'jaundice'                          => $medicalHistoryData['medical_history_jaundice'] ?? null,
                'unexplained_vaginal_bleeding'      => $medicalHistoryData['medical_history_unexplained_vaginal_bleeding'] ?? null,
                'abnormal_vaginal_discharge'        => $medicalHistoryData['medical_history_abnormal_vaginal_discharge'] ?? null,
                'abnormal_phenobarbital'            => $medicalHistoryData['medical_history_abnormal_phenobarbital'] ?? null,
                'smoker'                            => $medicalHistoryData['medical_history_smoker'] ?? null,
                'with_dissability'                  => $medicalHistoryData['medical_history_with_dissability'] ?? null,
                'if_with_dissability_specification' => $medicalHistoryData['if_with_dissability_specification'] ?? null,
            ]);

            family_planning_obsterical_histories::create([
                'case_id'                                            => $caseId,
                'G'                                                  => $obstericalHistoryData['family_planning_G'] ?? null,
                'P'                                                  => $obstericalHistoryData['family_planning_P'] ?? null,
                'full_term'                                          => $obstericalHistoryData['family_planning_full_term'] ?? null,
                'abortion'                                           => $obstericalHistoryData['family_planning_abortion'] ?? null,
                'premature'                                          => $obstericalHistoryData['family_planning_premature'] ?? null,
                'living_children'                                    => $obstericalHistoryData['family_planning_living_children'] ?? null,
                'date_of_last_delivery'                              => $obstericalHistoryData['family_planning_date_of_last_delivery'] ?? null,
                'type_of_last_delivery'                              => $obstericalHistoryData['family_planning_type_of_last_delivery'] ?? null,
                'date_of_last_delivery_menstrual_period'             => $obstericalHistoryData['family_planning_date_of_last_delivery_menstrual_period'] ?? null,
                'date_of_previous_delivery_menstrual_period'         => $obstericalHistoryData['family_planning_date_of_previous_delivery_menstrual_period'] ?? null,
                'type_of_menstrual'                                  => $obstericalHistoryData['family_planning_type_of_menstrual'] ?? null,
                'Dysmenorrhea'                                       => $obstericalHistoryData['family_planning_Dysmenorrhea'] ?? null,
                'hydatidiform_mole'                                  => $obstericalHistoryData['family_planning_hydatidiform_mole'] ?? null,
                'ectopic_pregnancy'                                  => $obstericalHistoryData['family_planning_ectopic_pregnancy'] ?? null,
            ]);

            risk_for_sexually_transmitted_infections::create([
                'case_id'                                        => $caseId,
                'infection_abnormal_discharge_from_genital_area' => $riskData['infection_abnormal_discharge_from_genital_area'] ?? null,
                'origin_of_abnormal_discharge'                   => $riskData['origin_of_abnormal_discharge'] ?? null,
                'scores_or_ulcer'                                => $riskData['scores_or_ulcer'] ?? null,
                'pain_or_burning_sensation'                      => $riskData['pain_or_burning_sensation'] ?? null,
                'history_of_sexually_transmitted_infection'      => $riskData['history_of_sexually_transmitted_infection'] ?? null,
                'sexually_transmitted_disease'                   => $riskData['sexually_transmitted_disease'] ?? null,
                'history_of_domestic_violence_of_VAW'            => $riskData['history_of_domestic_violence_of_VAW'] ?? null,
                'unpleasant_relationship_with_partner'           => $riskData['unpleasant_relationship_with_partner'] ?? null,
                'partner_does_not_approve'                       => $riskData['partner_does_not_approve'] ?? null,
                'referred_to'                                    => $riskData['referred_to'] ?? null,
                'reffered_to_others'                             => $riskData['reffered_to_others'] ?? null,
            ]);

            family_planning_physical_examinations::create([
                'case_id'                     => $caseId,
                'blood_pressure'              => $medicalData['blood_pressure'] ?? null,
                'pulse_rate'                  => $medicalData['pulse_rate'] ?? null,
                'height'                      => $medicalData['height'] ?? null,
                'weight'                      => $medicalData['weight'] ?? null,
                'skin_type'                   => $physicalExaminationData['physical_examination_skin_type'] ?? null,
                'conjuctiva_type'             => $physicalExaminationData['physical_examination_conjuctiva_type'] ?? null,
                'breast_type'                 => $physicalExaminationData['physical_examination_breast_type'] ?? null,
                'abdomen_type'                => $physicalExaminationData['physical_examination_abdomen_type'] ?? null,
                'extremites_type'             => $physicalExaminationData['physical_examination_extremites_type'] ?? null,
                'extremites_UID_type'         => $physicalExaminationData['physical_examination_extremites_UID_type'] ?? null,
                'cervical_abnormalities_type' => $physicalExaminationData['cervical_abnormalities_type'] ?? null,
                'cervical_consistency_type'   => $physicalExaminationData['cervical_consistency_type'] ?? null,
                'uterine_position_type'       => $physicalExaminationData['uterine_position_type'] ?? null,
                'uterine_depth_text'          => $physicalExaminationData['uterine_depth_text'] ?? null,
                'neck_type'                   => $physicalExaminationData['physical_examination_neck_type'] ?? null,
            ]);

            $sideBsignaturePath = null;
            if ($request->hasFile('add_side_b_name_n_signature_image')) {
                $sideBsignaturePath = $this->compressAndSaveSignature($request->file('add_side_b_name_n_signature_image'));
            } elseif ($request->filled('add_side_b_name_n_signature_data')) {
                $sideBsignaturePath = $this->saveCanvasSignature($request->add_side_b_name_n_signature_data);
            }

            family_planning_side_b_records::create([
                'medical_record_case_id'                         => $medicalCaseId,
                'health_worker_id'                               => $handledBy,
                'date_of_visit'                                  => $sideBdata['side_b_date_of_visit'] ?? null,
                'medical_findings'                               => $sideBdata['side_b_medical_findings'] ?? null,
                'method_accepted'                                => !empty($sideBdata['side_b_method_accepted']) ? implode(', ', $sideBdata['side_b_method_accepted']) : null,
                'signature_of_the_provider'                      => $sideBsignaturePath ?? null,
                'date_of_follow_up_visit'                        => $sideBdata['side_b_date_of_follow_up_visit'] ?? null,
                'baby_Less_than_six_months_question'             => $sideBdata['baby_Less_than_six_months_question'] ?? null,
                'sexual_intercouse_or_mesntrual_period_question' => $sideBdata['sexual_intercouse_or_mesntrual_period_question'] ?? null,
                'baby_last_4_weeks_question'                     => $sideBdata['baby_last_4_weeks_question'] ?? null,
                'menstrual_period_in_seven_days_question'        => $sideBdata['menstrual_period_in_seven_days_question'] ?? null,
                'miscarriage_or_abortion_question'               => $sideBdata['miscarriage_or_abortion_question'] ?? null,
                'contraceptive_question'                         => $sideBdata['contraceptive_question'] ?? null,
                'status'                                         => 'Active',
            ]);

            // WRA masterlist
            $method_of_FP = [
                'modern'      => ['Implant', 'IUD', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
            ];

            $modern_methods      = [];
            $traditional_methods = [];

            if ($methods) {
                foreach ($caseData['previously_used_method'] as $method) {
                    if (in_array($method, $method_of_FP['modern'])) {
                        $modern_methods[] = $method;
                    } elseif (in_array($method, $method_of_FP['traditional'])) {
                        $traditional_methods[] = $method;
                    }
                }
            }

            // FIX Bug 1: use ternary so empty arrays produce null instead of ""
            $converted_modern_methods      = !empty($modern_methods)      ? implode(',', $modern_methods)      : null;
            $converted_traditional_methods = !empty($traditional_methods) ? implode(',', $traditional_methods) : null;

            $method_accepted = [];
            if (!empty($normalizedChoosenMethod)) {
                $method_accepted = array_map('trim', explode(',', $normalizedChoosenMethod));
            }

            $accept_modern_FP = [];
            foreach ($method_accepted as $method) {
                if (in_array($method, $method_of_FP['modern'])) {
                    $accept_modern_FP[] = $method;
                }
            }

            // FIX Bug 1 (same): empty array → null
            $converted_accepted_modern_FP = !empty($accept_modern_FP) ? implode(',', $accept_modern_FP) : null;

            if ($familPlanningPatient->age >= 10) {
                wra_masterlists::create([
                    'medical_record_case_id'           => $medicalCaseId,
                    'health_worker_id'                 => $handledBy,
                    'address_id'                       => $patientAddress->id,
                    'patient_id'                       => $familyPlanningPatientRecordId,
                    'brgy_name'                        => $patientAddress->purok,
                    'house_hold_number'                => null,
                    'name_of_wra'                      => $familPlanningPatient->full_name,
                    'address'                          => $fullAddress,
                    'age'                              => $familPlanningPatient->age ?? null,
                    'date_of_birth'                    => $familPlanningPatient->date_of_birth ?? null,
                    'SE_status'                        => ($caseData['NHTS'] ?? null) === 'yes'
                        ? 'NHTS'
                        : (($caseData['NHTS'] ?? null) !== null ? 'Yes' : 'No'),

                    // FIX Bug 2: wrap all three values in a single array argument
                    'plan_to_have_more_children_yes'   => ($caseData['plan_to_have_more_children'] ?? null) === 'Yes'
                        ? collect([
                            $newAcceptorReasonFinal,
                            $currentUserReasonFinal,
                            $currentMethodReasonFinal,
                        ])->first(fn($value) => !empty($value))
                        : null,

                    'plan_to_have_more_children_no'    => ($caseData['plan_to_have_more_children'] ?? null) === 'No' ? 'limiting' : null,
                    'current_FP_methods'               => ($caseData['family_planning_type_of_patient'] ?? null) === 'current user' ? $previoulyMethod : null,
                    'modern_FP'                        => $converted_modern_methods,
                    'traditional_FP'                   => $converted_traditional_methods,
                    'currently_using_any_FP_method_no' => empty($caseData['previously_used_method']) ? 'yes' : null,
                    'shift_to_modern_method'           => null,
                    'wra_with_MFP_unmet_need'          => 'no',

                    // FIX Bug 3: !empty() correctly returns false for both null and ""
                    'wra_accept_any_modern_FP_method'  => !empty($converted_accepted_modern_FP) ? 'yes' : 'no',
                    'selected_modern_FP_method'        => $converted_accepted_modern_FP,
                    'date_when_FP_method_accepted'     => !empty($converted_accepted_modern_FP)
                        ? ($caseData['family_planning_date_of_acknowledgement'] ?? null)
                        : null,
                ]);
            }

            return response()->json(['message' => $message], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Family Planning Patient Creation Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => [$e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile()]]
            ], 500);
        }
    }

    public function editPatientDetails(Request $request, $id)
    {
        try {

            $familyPlanningRecord = medical_record_cases::with(['patient', 'family_planning_case_record', 'family_planning_medical_record'])
            ->where('type_of_case','family-planning')
            ->where('status','Active')
            ->findOrFail($id);
            $familyPlanningMedicalRecord = family_planning_medical_records::where("medical_record_case_id", $familyPlanningRecord->id)->first();
            $familyPlanningCaseRecord = family_planning_case_records::where("medical_record_case_id", $familyPlanningRecord->id)->where("status", "!=", 'Archived')->first();
            $address = patient_addresses::where('patient_id', $familyPlanningRecord->patient->id)->firstOrFail();


            $data = $request->validate([
                'first_name' => [
                    'required',
                    'string',
                    Rule::unique('patients')
                        ->ignore($familyPlanningRecord->patient->id)
                        ->where(function ($query) use ($request) {
                            return $query->where('first_name', $request->first_name)
                                ->where('last_name', $request->last_name);
                        }),
                ],
                'last_name'            => 'required|string',
                'middle_initial'       => 'sometimes|nullable|string',
                'date_of_birth'        => 'required|date|before_or_equal:today',
                'place_of_birth'       => 'sometimes|nullable|string',
                'age'                  => 'sometimes|nullable|numeric|max:100',
                'sex'                  => 'sometimes|nullable|string',
                'contact_number'       => 'required|digits_between:7,12',
                'nationality'          => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date|before_or_equal:today',
                'handled_by'           => 'required',
                'civil_status'         => 'sometimes|nullable|string',
                'occupation'           => 'sometimes|nullable|string',
                'religion'             => 'sometimes|nullable|string',
                'street'               => 'required',
                'brgy'                 => 'required',
                'blood_pressure' => [
                    'sometimes',
                    'nullable',
                    'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                ],
                'temperature'      => 'nullable|numeric|between:30,45',
                'pulse_rate'       => 'nullable|string|max:20',
                'respiratory_rate' => 'nullable|integer|min:5|max:60',
                'height'           => 'nullable|numeric|between:1,250',
                'weight'           => 'nullable|numeric|between:1,250',
                'client_id'        => 'sometimes|nullable|numeric',
                'philhealth_no' => [
                    'sometimes',
                    'nullable',
                    'regex:/^\d{2}-\d{9}-\d{1}$/'
                ],
                'NHTS'   => 'sometimes|nullable|string',
                'suffix' => 'sometimes|nullable|string',
            ], [
                'first_name.required'                => 'The first name is required.',
                'first_name.string'                  => 'The first name must be a valid text value.',
                'first_name.unique'                  => 'This patient already exists.',
                'last_name.required'                 => 'The last name is required.',
                'last_name.string'                   => 'The last name must be a valid text value.',
                'date_of_birth.required'             => 'The date of birth is required.',
                'date_of_birth.date'                 => 'Please enter a valid date of birth.',
                'date_of_birth.before_or_equal'      => 'The date of birth cannot be a future date.',
                'contact_number.required'            => 'The contact number is required.',
                'contact_number.digits_between'      => 'The contact number must be between :min and :max digits.',
                'date_of_registration.required'      => 'The date of registration is required.',
                'date_of_registration.date'          => 'Please enter a valid date of registration.',
                'date_of_registration.before_or_equal' => 'The date of registration cannot be a future date.',
                'handled_by.required'                => 'Please select the health worker who handled this record.',
                'street.required'                    => 'The street address is required.',
                'brgy.required'                      => 'The barangay is required.',
                'blood_pressure.regex'               => 'Please enter a valid blood pressure format (e.g., 120/80).',
                'temperature.numeric'                => 'The temperature must be a valid number.',
                'temperature.between'                => 'The temperature must be between 30°C and 45°C.',
                'pulse_rate.string'                  => 'The pulse rate must be a valid text value.',
                'pulse_rate.max'                     => 'The pulse rate may not exceed :max characters.',
                'respiratory_rate.integer'           => 'The respiratory rate must be a whole number.',
                'respiratory_rate.min'               => 'The respiratory rate must be at least :min breaths per minute.',
                'respiratory_rate.max'               => 'The respiratory rate may not exceed :max breaths per minute.',
                'height.numeric'                     => 'The height must be a valid number.',
                'height.between'                     => 'The height must be between 1 and 250 cm.',
                'weight.numeric'                     => 'The weight must be a valid number.',
                'weight.between'                     => 'The weight must be between 1 and 250 kg.',
                'philhealth_no.regex'                => 'Please enter a valid PhilHealth number format (e.g., 12-123456789-0).',
                'age.max'                            => 'The age may not be greater than :max years.',
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
            $data['sex'] = 'Female';
            $sex = isset($data['sex']) ? $data['sex'] : 'Female';
            // update the patient data first
            $familyPlanningRecord->patient->update([
                'first_name' => ucwords(strtolower($data['first_name'])) ?? ucwords(strtolower($familyPlanningRecord->patient->first_name)),
                'middle_initial' =>  $middleName,
                'last_name' => ucwords(strtolower($data['last_name'])) ?? ucwords(strtolower($familyPlanningRecord->patient->last_name)),
                'full_name' => $fullName ?? $familyPlanningRecord->patient->full_name,
                'age' => $data['age'] ?? $familyPlanningRecord->patient->age,
                'sex' => 'Female',
                'civil_status' => $data['civil_status'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'date_of_registration' => $data['date_of_registration'] ?? $familyPlanningRecord->patient->date_of_registration,
                'place_of_birth' => $data['place_of_birth'] ?? null,
                'suffix' => $data['suffix'] ?? ''
            ]);

            $familyPlanningRecord ->update([
                'date_of_registration' => $data['date_of_registration'] ?? $familyPlanningRecord->patient->date_of_registration
            ]);

            $patientUpdateService = new PatientUpdateService();
            if ($familyPlanningRecord->patient) {
                $patientUpdateService->updatePatientDetails($data, $familyPlanningRecord->patient->id);
            }
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
                    'client_first_name' => $familyPlanningRecord->patient->first_name,
                    'client_middle_name' => $familyPlanningRecord->patient->middle_initial ?? '',
                    'client_last_name' => $familyPlanningRecord->patient->last_name,
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
                'side_A_add_client_fname'          => 'required|string',
                'side_A_add_client_MI'             => 'sometimes|nullable|string',
                'side_A_add_client_lname'          => 'required|string',
                'side_A_add_client_date_of_birth'  => 'required|date|before_or_equal:today',
                'side_A_add_client_age'            => 'sometimes|nullable|numeric|max:100',
                'side_A_add_occupation'            => 'sometimes|nullable|string',
                'side_A_add_client_civil_status'   => 'sometimes|nullable|string',
                'side_A_add_client_religion'       => 'sometimes|nullable|string',
                'side_A_add_client_contact_number' => 'required|digits_between:7,12',
                'add_street'                       => 'required',
                'add_brgy'                         => 'required',
                'side_A_add_client_suffix'         => 'sometimes|nullable|string',
            ], [
                'side_A_add_client_fname.required'                => 'The client first name field is required.',
                'side_A_add_client_fname.string'                  => 'The client first name must be a string.',
                'side_A_add_client_MI.string'                     => 'The client middle initial must be a string.',
                'side_A_add_client_MI.max'                        => 'The client middle initial may not be greater than :max characters.',
                'side_A_add_client_lname.required'                => 'The client last name field is required.',
                'side_A_add_client_lname.string'                  => 'The client last name must be a string.',
                'side_A_add_client_date_of_birth.required'        => 'The client date of birth field is required.',
                'side_A_add_client_date_of_birth.date'            => 'The client date of birth must be a valid date.',
                'side_A_add_client_date_of_birth.before_or_equal' => 'The client date of birth must be today or earlier.',
                'side_A_add_client_age.numeric'                   => 'The client age must be a number.',
                'side_A_add_client_age.max'                       => 'The client age may not be greater than :max.',
                'add_street.required'                             => 'The street field is required.',
                'add_brgy.required'                               => 'The barangay field is required.',
            ]);

            $caseData = $request->validate([
                'side_A_add_client_id'                                       => 'sometimes|nullable|string',
                'side_A_add_philhealth_no'                                   => ['sometimes', 'nullable', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
                'side_A_add_NHTS'                                            => 'sometimes|nullable|string',
                'side_A_add_spouse_lname'                                    => 'sometimes|nullable|string',
                'side_A_add_spouse_fname'                                    => 'sometimes|nullable|string',
                'side_A_add_spouse_MI'                                       => 'sometimes|nullable|string|max:2',
                'side_A_add_spouse_date_of_birth'                            => 'sometimes|nullable|date|before_or_equal:today',
                'side_A_add_spouse_age'                                      => 'sometimes|nullable|numeric|max:100',
                'side_A_add_spouse_occupation'                               => 'sometimes|nullable|string',
                'side_A_add_spouse_suffix'                                   => 'sometimes|nullable|string',
                'side_A_add_number_of_living_children'                       => 'sometimes|nullable|numeric|max:50',
                'side_A_add_plan_to_have_more_children'                      => 'sometimes|nullable|string',
                'side_A_add_average_montly_income'                           => 'sometimes|nullable|numeric',
                'side_A_add_type_of_patient'                                 => 'sometimes|nullable|string',

                // Radio values
                'side_A_add_new_acceptor_reason_for_FP'                      => 'sometimes|nullable|string',
                'side_A_add_current_user_reason_for_FP'                      => 'sometimes|nullable|string',
                'side_A_add_current_method_reason'                           => 'sometimes|nullable|string',

                // ---------------------------------------------------------------
                // FIX: the three free-text companions that were missing before
                // ---------------------------------------------------------------
                'side_A_add_new_acceptor_reason_text'                        => 'sometimes|nullable|string|max:255',
                'side_A_add_current_user_reason_text'                        => 'sometimes|nullable|string|max:255',
                'side_A_add_side_effects_text_value'                         => 'sometimes|nullable|string|max:255',

                'side_A_add_previously_used_method'                          => 'sometimes|nullable|array',
                'side_A_add_choosen_method'                                  => [
                    'sometimes',
                    'nullable',
                    'string',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) return;

                        $allowedMethods = [
                            'Implant',
                            'Injectable',
                            'LAM',
                            'IUD',
                            'COC',
                            'SDM',
                            'BTL',
                            'POP',
                            'BBT',
                            'NSV',
                            'Condom',
                            'BOM/CMM/STM',
                        ];

                        $submitted    = array_map('trim', explode(',', $value));
                        $allowedLower = array_map('strtolower', $allowedMethods);

                        foreach ($submitted as $method) {
                            if (!in_array(strtolower($method), $allowedLower)) {
                                $fail("Invalid method \"{$method}\". Allowed methods are: " . implode(', ', $allowedMethods) . '.');
                                return;
                            }
                        }
                    },
                ],
                'side_A_add_family_planning_acknowledgement_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'side_A_add_family_planning_acknowledgement_signature_data'  => 'sometimes|nullable|string',
                'side_A_add_family_planning_date_of_acknowledgement'         => 'sometimes|nullable|date|before_or_equal:today',
                'side_A_add_family_planning_consent_signature_image'         => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'side_A_add_family_planning_consent_signature_data'          => 'sometimes|nullable|string',
                'side_A_add_family_planning_date_of_acknowledgement_consent' => 'sometimes|nullable|date|before_or_equal:today',
                'side_A_add_current_user_type'                               => 'sometimes|nullable|string',
                'side_A_add_health_worker_id'                                => 'required',
            ], [
                'side_A_add_client_id.string'                                                => 'The client ID must be a valid text value.',
                'side_A_add_philhealth_no.regex'                                             => 'Please enter a valid PhilHealth number format (e.g., 12-123456789-0).',
                'side_A_add_spouse_lname.string'                                             => 'The spouse last name must be a valid text value.',
                'side_A_add_spouse_fname.string'                                             => 'The spouse first name must be a valid text value.',
                'side_A_add_spouse_MI.string'                                                => 'The spouse middle initial must be a valid text value.',
                'side_A_add_spouse_MI.max'                                                   => 'The spouse middle initial may not exceed :max characters.',
                'side_A_add_spouse_date_of_birth.date'                                       => 'Please enter a valid spouse date of birth.',
                'side_A_add_spouse_date_of_birth.before_or_equal'                            => 'The spouse date of birth cannot be a future date.',
                'side_A_add_spouse_age.numeric'                                              => 'The spouse age must be a number.',
                'side_A_add_spouse_age.max'                                                  => 'The spouse age may not be greater than :max years.',
                'side_A_add_number_of_living_children.numeric'                               => 'The number of living children must be a number.',
                'side_A_add_number_of_living_children.max'                                   => 'The number of living children may not be greater than :max.',
                'side_A_add_average_montly_income.numeric'                                   => 'The average monthly income must be a valid number.',
                'side_A_add_new_acceptor_reason_text.max'                                    => 'The new acceptor reason detail may not exceed :max characters.',
                'side_A_add_current_user_reason_text.max'                                    => 'The current user reason detail may not exceed :max characters.',
                'side_A_add_side_effects_text_value.max'                                     => 'The side effects detail may not exceed :max characters.',
                'side_A_add_family_planning_acknowledgement_signature_image.image'           => 'The acknowledgement signature must be an image file.',
                'side_A_add_family_planning_acknowledgement_signature_image.mimes'           => 'The acknowledgement signature must be a jpg, jpeg, or png file.',
                'side_A_add_family_planning_acknowledgement_signature_image.max'             => 'The acknowledgement signature may not exceed :max kilobytes.',
                'side_A_add_family_planning_date_of_acknowledgement.date'                    => 'Please enter a valid acknowledgement date.',
                'side_A_add_family_planning_date_of_acknowledgement.before_or_equal'         => 'The acknowledgement date cannot be a future date.',
                'side_A_add_family_planning_consent_signature_image.image'                   => 'The consent signature must be an image file.',
                'side_A_add_family_planning_consent_signature_image.mimes'                   => 'The consent signature must be a jpg, jpeg, or png file.',
                'side_A_add_family_planning_consent_signature_image.max'                     => 'The consent signature may not exceed :max kilobytes.',
                'side_A_add_family_planning_date_of_acknowledgement_consent.date'            => 'Please enter a valid consent date.',
                'side_A_add_family_planning_date_of_acknowledgement_consent.before_or_equal' => 'The consent date cannot be a future date.',
                'side_A_add_health_worker_id.required'                                       => 'Please select the health worker assigned to this record.',
            ]);

            $medicalHistoryData = $request->validate([
                'side_A_add_severe_headaches_migraine'         => 'sometimes|nullable|string',
                'side_A_add_history_of_stroke'                 => 'sometimes|nullable|string',
                'side_A_add_non_traumatic_hemtoma'             => 'sometimes|nullable|string',
                'side_A_add_history_of_breast_cancer'          => 'sometimes|nullable|string',
                'side_A_add_severe_chest_pain'                 => 'sometimes|nullable|string',
                'side_A_add_cough'                             => 'sometimes|nullable|string',
                'side_A_add_jaundice'                          => 'sometimes|nullable|string',
                'side_A_add_unexplained_vaginal_bleeding'      => 'sometimes|nullable|string',
                'side_A_add_abnormal_vaginal_discharge'        => 'sometimes|nullable|string',
                'side_A_add_abnormal_phenobarbital'            => 'sometimes|nullable|string',
                'side_A_add_smoker'                            => 'sometimes|nullable|string',
                'side_A_add_with_dissability'                  => 'sometimes|nullable|string',
                'side_A_add_if_with_dissability_specification' => 'sometimes|nullable|string',
            ], [
                'side_A_add_severe_headaches_migraine.string'         => 'The severe headaches/migraine field must be a string.',
                'side_A_add_history_of_stroke.string'                 => 'The history of stroke field must be a string.',
                'side_A_add_non_traumatic_hemtoma.string'             => 'The non-traumatic hematoma field must be a string.',
                'side_A_add_history_of_breast_cancer.string'          => 'The history of breast cancer field must be a string.',
                'side_A_add_severe_chest_pain.string'                 => 'The severe chest pain field must be a string.',
                'side_A_add_unexplained_vaginal_bleeding.string'      => 'The unexplained vaginal bleeding field must be a string.',
                'side_A_add_abnormal_vaginal_discharge.string'        => 'The abnormal vaginal discharge field must be a string.',
                'side_A_add_abnormal_phenobarbital.string'            => 'The abnormal phenobarbital field must be a string.',
                'side_A_add_with_dissability.string'                  => 'The with disability field must be a string.',
                'side_A_add_if_with_dissability_specification.string' => 'The disability specification field must be a string.',
            ]);

            $obstericalHistoryData = $request->validate([
                'side_A_add_G'                                          => 'sometimes|nullable|numeric|max:20',
                'side_A_add_P'                                          => 'sometimes|nullable|numeric|max:20',
                'side_A_add_full_term'                                  => 'sometimes|nullable|numeric|max:20',
                'side_A_add_abortion'                                   => 'sometimes|nullable|numeric|max:20',
                'side_A_add_premature'                                  => 'sometimes|nullable|numeric|max:20',
                'side_A_add_living_children'                            => 'sometimes|nullable|numeric|max:20',
                'side_A_add_date_of_last_delivery'                      => 'sometimes|nullable|date',
                'side_A_add_type_of_last_delivery'                      => 'sometimes|nullable|string',
                'side_A_add_date_of_last_delivery_menstrual_period'     => 'sometimes|nullable|date',
                'side_A_add_date_of_previous_delivery_menstrual_period' => 'sometimes|nullable|date',
                'side_A_add_type_of_menstrual'                          => 'sometimes|nullable|string',
                'side_A_add_Dysmenorrhea'                               => 'sometimes|nullable|string',
                'side_A_add_hydatidiform_mole'                          => 'sometimes|nullable|string',
                'side_A_add_ectopic_pregnancy'                          => 'sometimes|nullable|string',
            ], [
                'side_A_add_G.numeric'                               => 'The G (gravida) must be a number.',
                'side_A_add_G.max'                                   => 'The G (gravida) may not be greater than :max.',
                'side_A_add_P.numeric'                               => 'The P (para) must be a number.',
                'side_A_add_P.max'                                   => 'The P (para) may not be greater than :max.',
                'side_A_add_full_term.numeric'                       => 'The full term must be a number.',
                'side_A_add_full_term.max'                           => 'The full term may not be greater than :max.',
                'side_A_add_abortion.numeric'                        => 'The abortion must be a number.',
                'side_A_add_abortion.max'                            => 'The abortion may not be greater than :max.',
                'side_A_add_premature.numeric'                       => 'The premature must be a number.',
                'side_A_add_premature.max'                           => 'The premature may not be greater than :max.',
                'side_A_add_living_children.numeric'                 => 'The living children must be a number.',
                'side_A_add_living_children.max'                     => 'The living children may not be greater than :max.',
                'side_A_add_date_of_last_delivery.date'              => 'The date of last delivery must be a valid date.',
                'side_A_add_date_of_last_delivery_menstrual_period.date'            => 'The date of last delivery menstrual period must be a valid date.',
                'side_A_add_date_of_previous_delivery_menstrual_period.date'        => 'The date of previous delivery menstrual period must be a valid date.',
            ]);

            $riskData = $request->validate([
                'side_A_add_infection_abnormal_discharge_from_genital_area' => 'sometimes|nullable|string',
                'side_A_add_origin_of_abnormal_discharge'                   => 'sometimes|nullable|string',
                'side_A_add_scores_or_ulcer'                                => 'sometimes|nullable|string',
                'side_A_add_pain_or_burning_sensation'                      => 'sometimes|nullable|string',
                'side_A_add_history_of_sexually_transmitted_infection'      => 'sometimes|nullable|string',
                'side_A_add_sexually_transmitted_disease'                   => 'sometimes|nullable|string',
                'side_A_add_history_of_domestic_violence_of_VAW'            => 'sometimes|nullable|string',
                'side_A_add_unpleasant_relationship_with_partner'           => 'sometimes|nullable|string',
                'side_A_add_partner_does_not_approve'                       => 'sometimes|nullable|string',
                'side_A_add_referred_to'                                    => 'sometimes|nullable|string',
                'side_A_add_reffered_to_others'                             => 'sometimes|nullable|string',
            ], [
                'side_A_add_infection_abnormal_discharge_from_genital_area.string' => 'The abnormal discharge from genital area field must be a string.',
                'side_A_add_origin_of_abnormal_discharge.string'                   => 'The origin of abnormal discharge field must be a string.',
                'side_A_add_scores_or_ulcer.string'                                => 'The sores or ulcer field must be a string.',
                'side_A_add_pain_or_burning_sensation.string'                      => 'The pain or burning sensation field must be a string.',
                'side_A_add_history_of_sexually_transmitted_infection.string'      => 'The history of sexually transmitted infection field must be a string.',
                'side_A_add_sexually_transmitted_disease.string'                   => 'The sexually transmitted disease field must be a string.',
                'side_A_add_history_of_domestic_violence_of_VAW.string'            => 'The history of domestic violence of VAW field must be a string.',
                'side_A_add_unpleasant_relationship_with_partner.string'           => 'The unpleasant relationship with partner field must be a string.',
                'side_A_add_partner_does_not_approve.string'                       => 'The partner does not approve field must be a string.',
                'side_A_add_reffered_to_others.string'                             => 'The referred to others field must be a string.',
            ]);

            $physicalExaminationData = $request->validate([
                'side_A_add_blood_pressure'              => ['sometimes', 'nullable', 'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'],
                'side_A_add_pulse_rate'                  => 'nullable|string|max:20',
                'side_A_add_height'                      => 'nullable|numeric|between:1,250',
                'side_A_add_weight'                      => 'nullable|numeric|between:1,250',
                'side_A_add_skin_type'                   => 'sometimes|nullable|string',
                'side_A_add_conjuctiva_type'             => 'sometimes|nullable|string',
                'side_A_add_breast_type'                 => 'sometimes|nullable|string',
                'side_A_add_abdomen_type'                => 'sometimes|nullable|string',
                'side_A_add_extremites_type'             => 'sometimes|nullable|string',
                'side_A_add_extremites_UID_type'         => 'sometimes|nullable|string',
                'side_A_add_cervical_abnormalities_type' => 'sometimes|nullable|string',
                'side_A_add_cervical_consistency_type'   => 'sometimes|nullable|string',
                'side_A_add_uterine_position_type'       => 'sometimes|nullable|string',
                'side_A_add_uterine_depth_text'          => 'sometimes|nullable|numeric',
                'side_A_add_neck_type'                   => 'sometimes|nullable|string',
            ], [
                'side_A_add_blood_pressure.regex'               => 'The blood pressure format is invalid.',
                'side_A_add_pulse_rate.string'                  => 'The pulse rate must be a string.',
                'side_A_add_pulse_rate.max'                     => 'The pulse rate may not be greater than :max characters.',
                'side_A_add_height.numeric'                     => 'The height must be a number.',
                'side_A_add_height.between'                     => 'The height must be between :min and :max cm.',
                'side_A_add_weight.numeric'                     => 'The weight must be a number.',
                'side_A_add_weight.between'                     => 'The weight must be between :min and :max kg.',
                'side_A_add_skin_type.string'                   => 'The skin type field must be a string.',
                'side_A_add_conjuctiva_type.string'             => 'The conjunctiva type field must be a string.',
                'side_A_add_breast_type.string'                 => 'The breast type field must be a string.',
                'side_A_add_abdomen_type.string'                => 'The abdomen type field must be a string.',
                'side_A_add_extremites_type.string'             => 'The extremities type field must be a string.',
                'side_A_add_extremites_UID_type.string'         => 'The extremities UID type field must be a string.',
                'side_A_add_cervical_abnormalities_type.string' => 'The cervical abnormalities type field must be a string.',
                'side_A_add_cervical_consistency_type.string'   => 'The cervical consistency type field must be a string.',
                'side_A_add_uterine_position_type.string'       => 'The uterine position type field must be a string.',
                'side_A_add_uterine_depth_text.numeric'         => 'The uterine depth must be a number.',
                'side_A_add_neck_type.string'                   => 'The neck type field must be a string.',
            ]);

            // ============================================================================
            // BUILD NAME COMPONENTS
            // ============================================================================
            $fpFirstName     = ucwords(strtolower($patientData['side_A_add_client_fname'] ?? ''));
            $fpLastName      = ucwords(strtolower($patientData['side_A_add_client_lname'] ?? ''));
            $fpMiddleName    = ucwords(strtolower($patientData['side_A_add_client_MI'] ?? ''));
            $fpMiddleInitial = $fpMiddleName ? strtoupper(substr($fpMiddleName, 0, 1)) . '.' : null;
            $fpFullName      = trim($fpFirstName . ' ' . ($fpMiddleInitial ? $fpMiddleInitial . ' ' : '') . $fpLastName);

            // ============================================================================
            // NORMALIZE CHOOSEN METHOD
            // ============================================================================
            $allowedMethods = [
                'Implant',
                'Injectable',
                'LAM',
                'IUD',
                'COC',
                'SDM',
                'BTL',
                'POP',
                'BBT',
                'NSV',
                'Condom',
                'BOM/CMM/STM',
            ];
            $allowedMethodsMap = array_combine(
                array_map('strtolower', $allowedMethods),
                $allowedMethods
            );

            $normalizedChoosenMethod = null;
            if (!empty($caseData['side_A_add_choosen_method'])) {
                $submitted               = array_map('trim', explode(',', $caseData['side_A_add_choosen_method']));
                $normalized              = array_map(fn($m) => $allowedMethodsMap[strtolower($m)] ?? $m, $submitted);
                $normalizedChoosenMethod = implode(', ', $normalized);
            }

            // ============================================================================
            // PREVIOUSLY USED METHOD
            // ============================================================================
            $previoulyMethod = !empty($caseData['side_A_add_previously_used_method'])
                ? implode(',', $caseData['side_A_add_previously_used_method'])
                : null;

            // ============================================================================
            // FIX: BUILD THE THREE REASON FIELDS — merge text companion into radio value
            // ============================================================================

            // New acceptor: if radio = "others", append the free-text; otherwise use the radio value as-is
            $newAcceptorReasonRadio = $caseData['side_A_add_new_acceptor_reason_for_FP'] ?? null;
            $newAcceptorReasonText  = trim($caseData['side_A_add_new_acceptor_reason_text'] ?? '');
            $newAcceptorReasonFinal = ($newAcceptorReasonRadio === 'others' && $newAcceptorReasonText !== '')
                ? 'others: ' . $newAcceptorReasonText
                : $newAcceptorReasonRadio;

            // Current user: same pattern
            $currentUserReasonRadio = $caseData['side_A_add_current_user_reason_for_FP'] ?? null;
            $currentUserReasonText  = trim($caseData['side_A_add_current_user_reason_text'] ?? '');
            $currentUserReasonFinal = ($currentUserReasonRadio === 'others' && $currentUserReasonText !== '')
                ? 'others: ' . $currentUserReasonText
                : $currentUserReasonRadio;

            // Current method: if radio = "side effects", append the free-text description
            $currentMethodReasonRadio = $caseData['side_A_add_current_method_reason'] ?? null;
            $sideEffectsText          = trim($caseData['side_A_add_side_effects_text_value'] ?? '');
            $currentMethodReasonFinal = ($currentMethodReasonRadio === 'side effects' && $sideEffectsText !== '')
                ? 'side effects: ' . $sideEffectsText
                : $currentMethodReasonRadio;

            // ============================================================================
            // SIGNATURES
            // ============================================================================
            $signaturePath        = null;
            $signatureConsentPath = null;

            if ($request->hasFile('side_A_add_family_planning_acknowledgement_signature_image')) {
                $signaturePath = $this->compressAndSaveSignature($request->file('side_A_add_family_planning_acknowledgement_signature_image'));
            } elseif ($request->filled('side_A_add_family_planning_acknowledgement_signature_data')) {
                $signaturePath = $this->saveCanvasSignature($request->side_A_add_family_planning_acknowledgement_signature_data);
            }

            if ($request->hasFile('side_A_add_family_planning_consent_signature_image')) {
                $signatureConsentPath = $this->compressAndSaveSignature($request->file('side_A_add_family_planning_consent_signature_image'));
            } elseif ($request->filled('side_A_add_family_planning_consent_signature_data')) {
                $signatureConsentPath = $this->saveCanvasSignature($request->side_A_add_family_planning_consent_signature_data);
            }

            // ============================================================================
            // CREATE CASE RECORD
            // ============================================================================
            $familyPlanningCaseRecord = family_planning_case_records::create([
                'medical_record_case_id'                  => $id,
                'health_worker_id'                        => $caseData['side_A_add_health_worker_id'],
                'client_id'                               => $caseData['side_A_add_client_id'] ?? null,
                'philhealth_no'                           => $caseData['side_A_add_philhealth_no'] ?? null,
                'NHTS'                                    => $caseData['side_A_add_NHTS'] ?? null,
                'client_name'                             => $fpFullName,
                'client_first_name'                       => $fpFirstName,
                'client_middle_name'                      => $fpMiddleName,
                'client_last_name'                        => $fpLastName,
                'client_date_of_birth'                    => $patientData['side_A_add_client_date_of_birth'] ?? null,
                'client_age'                              => $patientData['side_A_add_client_age'] ?? null,
                'client_suffix'                           => $patientData['side_A_add_client_suffix'] ?? '',
                'occupation'                              => $patientData['side_A_add_occupation'] ?? null,
                'client_contact_number'                   => $patientData['side_A_add_client_contact_number'] ?? null,
                'client_civil_status'                     => $patientData['side_A_add_client_civil_status'] ?? null,
                'client_religion'                         => $patientData['side_A_add_client_religion'] ?? null,
                'spouse_lname'                            => $caseData['side_A_add_spouse_lname'] ?? null,
                'spouse_fname'                            => $caseData['side_A_add_spouse_fname'] ?? null,
                'spouse_MI'                               => $caseData['side_A_add_spouse_MI'] ?? null,
                'spouse_date_of_birth'                    => $caseData['side_A_add_spouse_date_of_birth'] ?? null,
                'spouse_age'                              => $caseData['side_A_add_spouse_age'] ?? null,
                'spouse_occupation'                       => $caseData['side_A_add_spouse_occupation'] ?? null,
                'spouse_suffix'                           => $caseData['side_A_add_spouse_suffix'] ?? '',
                'number_of_living_children'               => $caseData['side_A_add_number_of_living_children'] ?? null,
                'plan_to_have_more_children'              => $caseData['side_A_add_plan_to_have_more_children'] ?? null,
                'average_montly_income'                   => $caseData['side_A_add_average_montly_income'] ?? null,
                'type_of_patient'                         => $caseData['side_A_add_type_of_patient'] ?? null,

                // FIX: use the merged final values instead of the raw radio values
                'new_acceptor_reason_for_FP'              => $newAcceptorReasonFinal,
                'current_user_reason_for_FP'              => $currentUserReasonFinal,
                'current_method_reason'                   => $currentMethodReasonFinal,

                'previously_used_method'                  => $previoulyMethod,
                'choosen_method'                          => $normalizedChoosenMethod,
                'signature_image'                         => $signaturePath,
                'date_of_acknowledgement'                 => $caseData['side_A_add_family_planning_date_of_acknowledgement'] ?? null,
                'acknowledgement_consent_signature_image' => $signatureConsentPath,
                'date_of_acknowledgement_consent'         => $caseData['side_A_add_family_planning_date_of_acknowledgement_consent'] ?? null,
                'current_user_type'                       => $caseData['side_A_add_current_user_type'] ?? null,
                'status'                                  => 'Active',
            ]);

            // ============================================================================
            // MEDICAL HISTORY
            // ============================================================================
            family_planning_medical_histories::create([
                'case_id'                           => $familyPlanningCaseRecord->id,
                'severe_headaches_migraine'         => $medicalHistoryData['side_A_add_severe_headaches_migraine'] ?? null,
                'history_of_stroke'                 => $medicalHistoryData['side_A_add_history_of_stroke'] ?? null,
                'non_traumatic_hemtoma'             => $medicalHistoryData['side_A_add_non_traumatic_hemtoma'] ?? null,
                'history_of_breast_cancer'          => $medicalHistoryData['side_A_add_history_of_breast_cancer'] ?? null,
                'severe_chest_pain'                 => $medicalHistoryData['side_A_add_severe_chest_pain'] ?? null,
                'cough'                             => $medicalHistoryData['side_A_add_cough'] ?? null,
                'jaundice'                          => $medicalHistoryData['side_A_add_jaundice'] ?? null,
                'unexplained_vaginal_bleeding'      => $medicalHistoryData['side_A_add_unexplained_vaginal_bleeding'] ?? null,
                'abnormal_vaginal_discharge'        => $medicalHistoryData['side_A_add_abnormal_vaginal_discharge'] ?? null,
                'abnormal_phenobarbital'            => $medicalHistoryData['side_A_add_abnormal_phenobarbital'] ?? null,
                'smoker'                            => $medicalHistoryData['side_A_add_smoker'] ?? null,
                'with_dissability'                  => $medicalHistoryData['side_A_add_with_dissability'] ?? null,
                'if_with_dissability_specification' => $medicalHistoryData['side_A_add_if_with_dissability_specification'] ?? null,
            ]);

            // ============================================================================
            // OBSTETRICAL HISTORY
            // ============================================================================
            family_planning_obsterical_histories::create([
                'case_id'                                    => $familyPlanningCaseRecord->id,
                'G'                                          => $obstericalHistoryData['side_A_add_G'] ?? null,
                'P'                                          => $obstericalHistoryData['side_A_add_P'] ?? null,
                'full_term'                                  => $obstericalHistoryData['side_A_add_full_term'] ?? null,
                'abortion'                                   => $obstericalHistoryData['side_A_add_abortion'] ?? null,
                'premature'                                  => $obstericalHistoryData['side_A_add_premature'] ?? null,
                'living_children'                            => $obstericalHistoryData['side_A_add_living_children'] ?? null,
                'date_of_last_delivery'                      => $obstericalHistoryData['side_A_add_date_of_last_delivery'] ?? null,
                'type_of_last_delivery'                      => $obstericalHistoryData['side_A_add_type_of_last_delivery'] ?? null,
                'date_of_last_delivery_menstrual_period'     => $obstericalHistoryData['side_A_add_date_of_last_delivery_menstrual_period'] ?? null,
                'date_of_previous_delivery_menstrual_period' => $obstericalHistoryData['side_A_add_date_of_previous_delivery_menstrual_period'] ?? null,
                'type_of_menstrual'                          => $obstericalHistoryData['side_A_add_type_of_menstrual'] ?? null,
                'Dysmenorrhea'                               => $obstericalHistoryData['side_A_add_Dysmenorrhea'] ?? null,
                'hydatidiform_mole'                          => $obstericalHistoryData['side_A_add_hydatidiform_mole'] ?? null,
                'ectopic_pregnancy'                          => $obstericalHistoryData['side_A_add_ectopic_pregnancy'] ?? null,
            ]);

            // ============================================================================
            // RISK FOR STI & VAW
            // ============================================================================
            risk_for_sexually_transmitted_infections::create([
                'case_id'                                        => $familyPlanningCaseRecord->id,
                'infection_abnormal_discharge_from_genital_area' => $riskData['side_A_add_infection_abnormal_discharge_from_genital_area'] ?? null,
                'origin_of_abnormal_discharge'                   => $riskData['side_A_add_origin_of_abnormal_discharge'] ?? null,
                'scores_or_ulcer'                                => $riskData['side_A_add_scores_or_ulcer'] ?? null,
                'pain_or_burning_sensation'                      => $riskData['side_A_add_pain_or_burning_sensation'] ?? null,
                'history_of_sexually_transmitted_infection'      => $riskData['side_A_add_history_of_sexually_transmitted_infection'] ?? null,
                'sexually_transmitted_disease'                   => $riskData['side_A_add_sexually_transmitted_disease'] ?? null,
                'history_of_domestic_violence_of_VAW'            => $riskData['side_A_add_history_of_domestic_violence_of_VAW'] ?? null,
                'unpleasant_relationship_with_partner'           => $riskData['side_A_add_unpleasant_relationship_with_partner'] ?? null,
                'partner_does_not_approve'                       => $riskData['side_A_add_partner_does_not_approve'] ?? null,
                'referred_to'                                    => $riskData['side_A_add_referred_to'] ?? null,
                'reffered_to_others'                             => $riskData['side_A_add_reffered_to_others'] ?? null,
            ]);

            // ============================================================================
            // PHYSICAL EXAMINATION
            // ============================================================================
            family_planning_physical_examinations::create([
                'case_id'                     => $familyPlanningCaseRecord->id,
                'blood_pressure'              => $physicalExaminationData['side_A_add_blood_pressure'] ?? null,
                'pulse_rate'                  => $physicalExaminationData['side_A_add_pulse_rate'] ?? null,
                'height'                      => $physicalExaminationData['side_A_add_height'] ?? null,
                'weight'                      => $physicalExaminationData['side_A_add_weight'] ?? null,
                'skin_type'                   => $physicalExaminationData['side_A_add_skin_type'] ?? null,
                'conjuctiva_type'             => $physicalExaminationData['side_A_add_conjuctiva_type'] ?? null,
                'breast_type'                 => $physicalExaminationData['side_A_add_breast_type'] ?? null,
                'abdomen_type'                => $physicalExaminationData['side_A_add_abdomen_type'] ?? null,
                'extremites_type'             => $physicalExaminationData['side_A_add_extremites_type'] ?? null,
                'extremites_UID_type'         => $physicalExaminationData['side_A_add_extremites_UID_type'] ?? null,
                'cervical_abnormalities_type' => $physicalExaminationData['side_A_add_cervical_abnormalities_type'] ?? null,
                'cervical_consistency_type'   => $physicalExaminationData['side_A_add_cervical_consistency_type'] ?? null,
                'uterine_position_type'       => $physicalExaminationData['side_A_add_uterine_position_type'] ?? null,
                'uterine_depth_text'          => $physicalExaminationData['side_A_add_uterine_depth_text'] ?? null,
                'neck_type'                   => $physicalExaminationData['side_A_add_neck_type'] ?? null,
            ]);

            // ============================================================================
            // WRA MASTERLIST UPDATE
            // ============================================================================
            $method_of_FP = [
                'modern'      => ['Implant', 'IUD', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
            ];

            $modern_methods      = [];
            $traditional_methods = [];
            $previouslyMethods   = $caseData['side_A_add_previously_used_method'] ?? [];

            foreach ($previouslyMethods as $method) {
                if (in_array($method, $method_of_FP['modern'])) {
                    $modern_methods[] = $method;
                } elseif (in_array($method, $method_of_FP['traditional'])) {
                    $traditional_methods[] = $method;
                }
            }

            $converted_modern_methods      = !empty($modern_methods) ? implode(',', $modern_methods) : null;
            $converted_traditional_methods = !empty($traditional_methods) ? implode(',', $traditional_methods) : null;

            $method_accepted = [];
            if (!empty($normalizedChoosenMethod)) {
                $method_accepted = array_map('trim', explode(',', $normalizedChoosenMethod));
            }

            $accept_modern_FP = [];
            foreach ($method_accepted as $method) {
                if (in_array($method, $method_of_FP['modern'])) {
                    $accept_modern_FP[] = $method;
                }
            }

            $converted_accepted_modern_FP = !empty($accept_modern_FP) ? implode(',', $accept_modern_FP) : null;

            // Update patient address
            $medical_case_record = medical_record_cases::with('patient')->where('id', $id)->first();
            $address             = patient_addresses::where('patient_id', $medical_case_record->patient->id)->firstOrFail();
            $blk_n_street        = explode(',', $patientData['add_street']);

            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street'       => $blk_n_street[1] ?? $address->street,
                'purok'        => $patientData['add_brgy'] ?? $address->purok,
            ]);

            $address->refresh();
            $newAddress = collect([
                $address->house_number,
                $address->street,
                $address->purok,
                $address->barangay ?? null,
                $address->city ?? null,
                $address->province ?? null,
            ])->filter()->join(', ');

            $clientAge = !empty($patientData['side_A_add_client_age'])
                ? (int) $patientData['side_A_add_client_age']
                : (int) ($medical_case_record->patient->age ?? 0);

            if ($clientAge >= 10) {
                wra_masterlists::updateOrCreate(
                    ['medical_record_case_id' => $id],
                    [
                        'health_worker_id'                 => $caseData['side_A_add_health_worker_id'],
                        'address_id'                       => $address->id,
                        'patient_id'                       => $medical_case_record->patient->id,
                        'brgy_name'                        => $address->purok,
                        'name_of_wra'                      => $medical_case_record->patient->full_name,
                        'address'                          => $newAddress,
                        'age'                              => $clientAge,
                        'date_of_birth'                    => $patientData['side_A_add_client_date_of_birth'] ?? null,
                        'SE_status'                        => ($caseData['side_A_add_NHTS'] ?? null) === 'yes'
                            ? 'NHTS'
                            : (($caseData['side_A_add_NHTS'] ?? null) !== null ? 'Yes' : 'No'),
                        'plan_to_have_more_children_yes'   => ($caseData['side_A_add_plan_to_have_more_children'] ?? null) === 'Yes'
                            ? collect([
                                $newAcceptorReasonFinal,
                                $currentUserReasonFinal,
                                $currentMethodReasonFinal,
                            ])->first(fn($value) => !empty($value))
                            : null,
                        'plan_to_have_more_children_no'    => ($caseData['side_A_add_plan_to_have_more_children'] ?? null) === 'No' ? 'limiting' : null,
                        'current_FP_methods'               => ($caseData['side_A_add_type_of_patient'] ?? null) === 'current user' ? $previoulyMethod : null,
                        'modern_FP'                        => $converted_modern_methods,
                        'traditional_FP'                   => $converted_traditional_methods,
                        'currently_using_any_FP_method_no' => empty($caseData['side_A_add_previously_used_method']) ? 'yes' : null,
                        'wra_with_MFP_unmet_need'          => 'no',
                        'wra_accept_any_modern_FP_method'  => !empty($converted_accepted_modern_FP) ? 'yes' : 'no',
                        'selected_modern_FP_method'        => $converted_accepted_modern_FP,
                        'date_when_FP_method_accepted'     => !empty($converted_accepted_modern_FP)
                            ? ($caseData['side_A_add_family_planning_date_of_acknowledgement'] ?? null)
                            : null,
                    ]
                );
            }

            return response()->json(['message' => 'Family Planning Client Assessment Record - Side A information is added Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => [$e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile()]],
            ], 500);
        }
    }

    // ------------------------------------------------------------------------------------------------------------------



    public function updateCaseInfo(Request $request, $id)
    {
        try {
            $familyPlanCaseInfo = family_planning_case_records::with(['medical_history', 'obsterical_history', 'risk_for_sexually_transmitted_infection', 'physical_examinations'])->findOrFail($id);
            $medical_case_record = medical_record_cases::with(['patient', 'family_planning_medical_record'])->findOrFail($familyPlanCaseInfo->medical_record_case_id);

            $patientData = $request->validate(
                [
                    'edit_client_fname'          => 'required|string',
                    'edit_client_MI'             => 'sometimes|nullable|string',
                    'edit_client_lname'          => 'required|string',
                    'edit_client_date_of_birth'  => 'required|date|before_or_equal:today',
                    'edit_client_age'            => 'required|numeric|max:100',
                    'edit_occupation'            => 'sometimes|nullable|string',
                    'edit_client_civil_status'   => 'sometimes|nullable|string',
                    'edit_client_religion'       => 'sometimes|nullable|string',
                    'edit_street'                => 'required',
                    'edit_brgy'                  => 'required',
                    'edit_client_contact_number' => 'required|digits_between:7,12',
                    'edit_client_suffix'         => 'sometimes|nullable|string',
                ],
                [],
                [
                    'edit_client_fname'         => 'first name',
                    'edit_client_MI'            => 'middle initial',
                    'edit_client_lname'         => 'last name',
                    'edit_client_date_of_birth' => 'date of birth',
                    'edit_client_age'           => 'age',
                    'edit_occupation'           => 'occupation',
                    'edit_client_civil_status'  => 'civil status',
                    'edit_client_religion'      => 'religion',
                ]
            );

            $caseData = $request->validate(
                [
                    'edit_client_id'                                       => 'sometimes|nullable|string',
                    'edit_philhealth_no'                                   => ['sometimes', 'nullable', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
                    'edit_NHTS'                                            => 'sometimes|nullable|string',
                    'edit_spouse_lname'                                    => 'sometimes|nullable|string',
                    'edit_spouse_fname'                                    => 'sometimes|nullable|string',
                    'edit_spouse_MI'                                       => 'sometimes|nullable|string',
                    'edit_spouse_date_of_birth'                            => 'sometimes|nullable|date|before_or_equal:today',
                    'edit_spouse_age'                                      => 'sometimes|nullable|numeric|max:100',
                    'edit_spouse_occupation'                               => 'sometimes|nullable|string',
                    'edit_spouse_suffix'                                   => 'sometimes|nullable|string',
                    'edit_number_of_living_children'                       => 'sometimes|nullable|numeric|max:50',
                    'edit_plan_to_have_more_children'                      => 'sometimes|nullable|string',
                    'edit_average_montly_income'                           => 'sometimes|nullable|numeric',
                    'edit_type_of_patient'                                 => 'sometimes|nullable|string',
                    // new acceptor reason radio + text box (separate names)
                    'edit_new_acceptor_reason_for_FP'                      => 'sometimes|nullable|string',
                    'edit_new_acceptor_reason_text'                        => 'sometimes|nullable|string',
                    // current user reason radio + text box (separate names)
                    'edit_current_user_reason_for_FP'                      => 'sometimes|nullable|string',
                    'edit_current_user_reason_for_FP_other'                => 'sometimes|nullable|string',
                    // current method reason radio (text box now has its OWN name below)
                    'edit_current_method_reason'                           => 'sometimes|nullable|string',
                    // FIXED: separate field — no longer shares name with the radio group
                    'edit_side_effects_text_value'                         => 'sometimes|nullable|string',
                    'edit_previously_used_method'                          => 'sometimes|nullable|array',
                    'edit_choosen_method'                                  => [
                        'sometimes',
                        'nullable',
                        'string',
                        function ($attribute, $value, $fail) {
                            if (empty($value)) return;
                            $allowedMethods = [
                                'Implant', 'Injectable', 'LAM', 'IUD', 'COC',
                                'SDM', 'BTL', 'POP', 'BBT', 'NSV', 'Condom', 'BOM/CMM/STM',
                            ];
                            $submitted    = array_map('trim', explode(',', $value));
                            $allowedLower = array_map('strtolower', $allowedMethods);
                            foreach ($submitted as $method) {
                                if (!in_array(strtolower($method), $allowedLower)) {
                                    $fail("Invalid method \"{$method}\". Allowed methods are: " . implode(', ', $allowedMethods) . '.');
                                    return;
                                }
                            }
                        },
                    ],
                    'edit_family_planning_acknowledgement_signature_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                    'edit_family_planning_acknowledgement_signature_data'  => 'sometimes|nullable|string',
                    'edit_date_of_acknowledgement'                         => 'sometimes|nullable|date|before_or_equal:today',
                    'edit_family_planning_consent_signature_image'         => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                    'edit_family_planning_consent_signature_data'          => 'sometimes|nullable|string',
                    'edit_date_of_acknowledgement_consent'                 => 'sometimes|nullable|date|before_or_equal:today',
                    'edit_current_user_type'                               => 'sometimes|nullable|string',
                ],
                [
                    'edit_philhealth_no.regex'                                   => 'Please enter a valid PhilHealth number format (e.g., 12-123456789-0).',
                    'edit_spouse_date_of_birth.date'                             => 'Please enter a valid spouse date of birth.',
                    'edit_spouse_date_of_birth.before_or_equal'                  => 'The spouse date of birth cannot be a future date.',
                    'edit_spouse_age.numeric'                                    => 'The spouse age must be a number.',
                    'edit_spouse_age.max'                                        => 'The spouse age may not be greater than :max years.',
                    'edit_number_of_living_children.numeric'                     => 'The number of living children must be a number.',
                    'edit_number_of_living_children.max'                         => 'The number of living children may not be greater than :max.',
                    'edit_average_montly_income.numeric'                         => 'The average monthly income must be a valid number.',
                    'edit_family_planning_acknowledgement_signature_image.image' => 'The acknowledgement signature must be an image file.',
                    'edit_family_planning_acknowledgement_signature_image.mimes' => 'The acknowledgement signature must be a jpg, jpeg, or png file.',
                    'edit_family_planning_acknowledgement_signature_image.max'   => 'The acknowledgement signature may not exceed :max kilobytes.',
                    'edit_date_of_acknowledgement.date'                          => 'Please enter a valid acknowledgement date.',
                    'edit_date_of_acknowledgement.before_or_equal'               => 'The acknowledgement date cannot be a future date.',
                    'edit_family_planning_consent_signature_image.image'         => 'The consent signature must be an image file.',
                    'edit_family_planning_consent_signature_image.mimes'         => 'The consent signature must be a jpg, jpeg, or png file.',
                    'edit_family_planning_consent_signature_image.max'           => 'The consent signature may not exceed :max kilobytes.',
                    'edit_date_of_acknowledgement_consent.date'                  => 'Please enter a valid consent date.',
                    'edit_date_of_acknowledgement_consent.before_or_equal'       => 'The consent date cannot be a future date.',
                ],
                [
                    'edit_client_id'                                       => 'client ID',
                    'edit_philhealth_no'                                   => 'PhilHealth number',
                    'edit_NHTS'                                            => 'NHTS status',
                    'edit_spouse_lname'                                    => 'spouse last name',
                    'edit_spouse_fname'                                    => 'spouse first name',
                    'edit_spouse_MI'                                       => 'spouse middle initial',
                    'edit_spouse_date_of_birth'                            => 'spouse date of birth',
                    'edit_spouse_age'                                       => 'spouse age',
                    'edit_spouse_occupation'                               => 'spouse occupation',
                    'edit_number_of_living_children'                       => 'number of living children',
                    'edit_plan_to_have_more_children'                      => 'plan to have more children',
                    'edit_average_montly_income'                           => 'average monthly income',
                    'edit_type_of_patient'                                 => 'type of family planning patient',
                    'edit_new_acceptor_reason_for_FP'                      => 'reason for new acceptor of family planning',
                    'edit_current_user_reason_for_FP'                      => 'reason for current user of family planning',
                    'edit_current_method_reason'                           => 'reason for current method',
                    'edit_previously_used_method'                          => 'previously used method',
                    'edit_choosen_method'                                  => 'chosen method',
                    'edit_family_planning_acknowledgement_signature_image' => 'acknowledgement signature',
                    'edit_date_of_acknowledgement'                         => 'date of acknowledgement',
                    'edit_family_planning_consent_signature_image'         => 'consent signature',
                    'edit_date_of_acknowledgement_consent'                 => 'date of acknowledgement consent',
                    'edit_current_user_type'                               => 'current user type',
                ]
            );

            // medical history
            $medicalHistoryData = $request->validate([
                'edit_severe_headaches_migraine'         => 'sometimes|nullable|string',
                'edit_history_of_stroke'                 => 'sometimes|nullable|string',
                'edit_non_traumatic_hemtoma'             => 'sometimes|nullable|string',
                'edit_history_of_breast_cancer'          => 'sometimes|nullable|string',
                'edit_severe_chest_pain'                 => 'sometimes|nullable|string',
                'edit_cough'                             => 'sometimes|nullable|string',
                'edit_jaundice'                          => 'sometimes|nullable|string',
                'edit_unexplained_vaginal_bleeding'      => 'sometimes|nullable|string',
                'edit_abnormal_vaginal_discharge'        => 'sometimes|nullable|string',
                'edit_abnormal_phenobarbital'            => 'sometimes|nullable|string',
                'edit_smoker'                            => 'sometimes|nullable|string',
                'edit_with_dissability'                  => 'sometimes|nullable|string',
                'edit_if_with_dissability_specification' => 'sometimes|nullable|string',
            ], [
                'edit_severe_headaches_migraine.string'         => 'The severe headaches/migraine field must be a string.',
                'edit_history_of_stroke.string'                 => 'The history of stroke field must be a string.',
                'edit_non_traumatic_hemtoma.string'             => 'The non-traumatic hematoma field must be a string.',
                'edit_history_of_breast_cancer.string'          => 'The history of breast cancer field must be a string.',
                'edit_severe_chest_pain.string'                 => 'The severe chest pain field must be a string.',
                'edit_unexplained_vaginal_bleeding.string'      => 'The unexplained vaginal bleeding field must be a string.',
                'edit_abnormal_vaginal_discharge.string'        => 'The abnormal vaginal discharge field must be a string.',
                'edit_abnormal_phenobarbital.string'            => 'The abnormal phenobarbital field must be a string.',
                'edit_with_dissability.string'                  => 'The with disability field must be a string.',
                'edit_if_with_dissability_specification.string' => 'The disability specification field must be a string.',
            ]);

            // Obstetrical history
            $obstericalHistoryData = $request->validate([
                'edit_G'                                          => 'sometimes|nullable|numeric|max:20',
                'edit_P'                                          => 'sometimes|nullable|numeric|max:20',
                'edit_full_term'                                  => 'sometimes|nullable|numeric|max:20',
                'edit_abortion'                                   => 'sometimes|nullable|numeric|max:20',
                'edit_premature'                                  => 'sometimes|nullable|numeric|max:20',
                'edit_living_children'                            => 'sometimes|nullable|numeric|max:20',
                'edit_date_of_last_delivery'                      => 'sometimes|nullable|date',
                'edit_type_of_last_delivery'                      => 'sometimes|nullable|string',
                'edit_date_of_last_delivery_menstrual_period'     => 'sometimes|nullable|date',
                'edit_date_of_previous_delivery_menstrual_period' => 'sometimes|nullable|date',
                'edit_type_of_menstrual'                          => 'sometimes|nullable|string',
                'edit_Dysmenorrhea'                               => 'sometimes|nullable|string',
                'edit_hydatidiform_mole'                          => 'sometimes|nullable|string',
                'edit_ectopic_pregnancy'                          => 'sometimes|nullable|string',
            ], [
                'edit_G.numeric'                                       => 'The G (gravida) must be a number.',
                'edit_G.max'                                           => 'The G (gravida) may not be greater than :max.',
                'edit_P.numeric'                                       => 'The P (para) must be a number.',
                'edit_P.max'                                           => 'The P (para) may not be greater than :max.',
                'edit_full_term.numeric'                               => 'The full term must be a number.',
                'edit_full_term.max'                                   => 'The full term may not be greater than :max.',
                'edit_abortion.numeric'                                => 'The abortion must be a number.',
                'edit_abortion.max'                                    => 'The abortion may not be greater than :max.',
                'edit_premature.numeric'                               => 'The premature must be a number.',
                'edit_premature.max'                                   => 'The premature may not be greater than :max.',
                'edit_living_children.numeric'                         => 'The living children must be a number.',
                'edit_living_children.max'                             => 'The living children may not be greater than :max.',
                'edit_date_of_last_delivery.date'                      => 'The date of last delivery must be a valid date.',
                'edit_date_of_last_delivery_menstrual_period.date'     => 'The date of last delivery menstrual period must be a valid date.',
                'edit_date_of_previous_delivery_menstrual_period.date' => 'The date of previous delivery menstrual period must be a valid date.',
            ]);

            // Risk for STI & VAW
            $riskData = $request->validate([
                'edit_infection_abnormal_discharge_from_genital_area' => 'sometimes|nullable|string',
                'edit_origin_of_abnormal_discharge'                   => 'sometimes|nullable|string',
                'edit_scores_or_ulcer'                                => 'sometimes|nullable|string',
                'edit_pain_or_burning_sensation'                      => 'sometimes|nullable|string',
                'edit_history_of_sexually_transmitted_infection'      => 'sometimes|nullable|string',
                'edit_sexually_transmitted_disease'                   => 'sometimes|nullable|string',
                'edit_history_of_domestic_violence_of_VAW'            => 'sometimes|nullable|string',
                'edit_unpleasant_relationship_with_partner'           => 'sometimes|nullable|string',
                'edit_partner_does_not_approve'                       => 'sometimes|nullable|string',
                'edit_referred_to'                                    => 'sometimes|nullable|string',
                'edit_reffered_to_others'                             => 'sometimes|nullable|string',
            ], [
                'edit_infection_abnormal_discharge_from_genital_area.string' => 'The abnormal discharge from genital area field must be a string.',
                'edit_origin_of_abnormal_discharge.string'                   => 'The origin of abnormal discharge field must be a string.',
                'edit_scores_or_ulcer.string'                                => 'The sores or ulcer field must be a string.',
                'edit_pain_or_burning_sensation.string'                      => 'The pain or burning sensation field must be a string.',
                'edit_history_of_sexually_transmitted_infection.string'      => 'The history of sexually transmitted infection field must be a string.',
                'edit_sexually_transmitted_disease.string'                   => 'The sexually transmitted disease field must be a string.',
                'edit_history_of_domestic_violence_of_VAW.string'            => 'The history of domestic violence of VAW field must be a string.',
                'edit_unpleasant_relationship_with_partner.string'           => 'The unpleasant relationship with partner field must be a string.',
                'edit_partner_does_not_approve.string'                       => 'The partner does not approve field must be a string.',
                'edit_reffered_to_others.string'                             => 'The referred to others field must be a string.',
            ]);

            // Physical examination
            $physicalExaminationData = $request->validate([
                'edit_blood_pressure'              => ['sometimes', 'nullable', 'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'],
                'edit_pulse_rate'                  => 'nullable|string|max:20',
                'edit_height'                      => 'nullable|numeric|between:1,250',
                'edit_weight'                      => 'nullable|numeric|between:1,250',
                'edit_skin_type'                   => 'sometimes|nullable|string',
                'edit_conjuctiva_type'             => 'sometimes|nullable|string',
                'edit_breast_type'                 => 'sometimes|nullable|string',
                'edit_abdomen_type'                => 'sometimes|nullable|string',
                'edit_extremites_type'             => 'sometimes|nullable|string',
                'edit_extremites_UID_type'         => 'sometimes|nullable|string',
                'edit_cervical_abnormalities_type' => 'sometimes|nullable|string',
                'edit_cervical_consistency_type'   => 'sometimes|nullable|string',
                'edit_uterine_position_type'       => 'sometimes|nullable|string',
                'edit_uterine_depth_text'          => 'sometimes|nullable|numeric',
                'edit_neck_type'                   => 'sometimes|nullable|string',
            ], [
                'edit_blood_pressure.regex'               => 'The blood pressure format is invalid.',
                'edit_pulse_rate.string'                  => 'The pulse rate must be a string.',
                'edit_pulse_rate.max'                     => 'The pulse rate may not be greater than :max characters.',
                'edit_height.numeric'                     => 'The height must be a number.',
                'edit_height.between'                     => 'The height must be between :min and :max cm.',
                'edit_weight.numeric'                     => 'The weight must be a number.',
                'edit_weight.between'                     => 'The weight must be between :min and :max kg.',
                'edit_skin_type.string'                   => 'The skin type field must be a string.',
                'edit_conjuctiva_type.string'             => 'The conjunctiva type field must be a string.',
                'edit_breast_type.string'                 => 'The breast type field must be a string.',
                'edit_abdomen_type.string'                => 'The abdomen type field must be a string.',
                'edit_extremites_type.string'             => 'The extremities type field must be a string.',
                'edit_extremites_UID_type.string'         => 'The extremities IUD type field must be a string.',
                'edit_cervical_abnormalities_type.string' => 'The cervical abnormalities type field must be a string.',
                'edit_cervical_consistency_type.string'   => 'The cervical consistency type field must be a string.',
                'edit_uterine_position_type.string'       => 'The uterine position type field must be a string.',
                'edit_uterine_depth_text.numeric'         => 'The uterine depth must be a number.',
                'edit_neck_type.string'                   => 'The neck type field must be a string.',
            ]);

            // Build full name
            $middle   = substr($patientData['edit_client_MI'] ?? '', 0, 1);
            $middle   = $middle ? strtoupper($middle) . '.' : null;
            $parts    = [
                strtolower($patientData['edit_client_fname']),
                $middle,
                strtolower($patientData['edit_client_lname']),
                $patientData['edit_client_suffix'] ?? null,
            ];
            $fullName = ucwords(trim(implode(' ', array_filter($parts))));

            // Update patient
            $medical_case_record->patient->update([
                'first_name'     => ucwords(strtolower($patientData['edit_client_fname'])),
                'middle_initial' => ucwords(strtolower($patientData['edit_client_MI'] ?? '')),
                'last_name'      => ucwords(strtolower($patientData['edit_client_lname'])),
                'full_name'      => $fullName,
                'age'            => $patientData['edit_client_age'],
                'contact_number' => $patientData['edit_client_contact_number'] ?? null,
                'date_of_birth'  => $patientData['edit_client_date_of_birth'],
                'civil_status'   => $patientData['edit_client_civil_status'] ?? null,
                'suffix'         => $patientData['edit_client_suffix'] ?? '',
            ]);

            $address      = patient_addresses::where('patient_id', $medical_case_record->patient->id)->firstOrFail();
            $blk_n_street = explode(',', $patientData['edit_street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street'       => $blk_n_street[1] ?? $address->street,
                'purok'        => $patientData['edit_brgy'] ?? $address->purok,
            ]);

            $address->refresh();
            $newAddress = $address->house_number . ", " . $address->street . "," . $address->purok . "," . $address->barangay . "," . $address->city . "," . $address->province;

            $medical_case_record->family_planning_medical_record->update([
                'patient_name'  => trim($patientData['edit_client_fname'] . ' ' . ($patientData['edit_client_MI'] ?? '') . ' ' . $patientData['edit_client_lname']),
                'occupation'    => $patientData['edit_occupation'] ?? null,
                'blood_pressure'=> $physicalExaminationData['edit_blood_pressure'] ?? null,
                'pulse_rate'    => $physicalExaminationData['edit_pulse_rate'] ?? null,
                'height'        => $physicalExaminationData['edit_height'] ?? null,
                'weight'        => $physicalExaminationData['edit_weight'] ?? null,
                'religion'      => $patientData['edit_client_religion'] ?? null,
            ]);

            $medical_case_record->patient->refresh();

            $previoulyMethod = null;
            if (isset($caseData['edit_previously_used_method']) && !empty($caseData['edit_previously_used_method'])) {
                $previoulyMethod = implode(",", $caseData['edit_previously_used_method']);
            }

            // ---------------------------------------------------------------
            // FIX 1: new_acceptor_reason_for_FP
            // Only use the text box value when the radio is explicitly "others"
            // ---------------------------------------------------------------
            $newAcceptorRadio = $caseData['edit_new_acceptor_reason_for_FP'] ?? null;
            $newAcceptorText  = trim($caseData['edit_new_acceptor_reason_text'] ?? '');

            if ($newAcceptorRadio === 'others' && !empty($newAcceptorText)) {
                $resolvedNewAcceptorReason = $newAcceptorText;
            } elseif ($newAcceptorRadio === 'others') {
                $resolvedNewAcceptorReason = null;
            } else {
                // "spacing" or "limiting" — use radio value as-is
                $resolvedNewAcceptorReason = $newAcceptorRadio ?? $familyPlanCaseInfo->new_acceptor_reason_for_FP;
            }

            // ---------------------------------------------------------------
            // FIX 2: current_user_reason_for_FP
            // Only use the text box value when the radio is explicitly "others"
            // ---------------------------------------------------------------
            $radioReason = $caseData['edit_current_user_reason_for_FP'] ?? null;
            $otherText   = trim($caseData['edit_current_user_reason_for_FP_other'] ?? '');

            if ($radioReason === 'others' && !empty($otherText)) {
                $currentReason = $otherText;
            } elseif ($radioReason === 'others') {
                $currentReason = null;
            } else {
                // "spacing" or "limiting" — use radio value as-is
                $currentReason = $radioReason ?? $familyPlanCaseInfo->current_user_reason_for_FP;
            }

            // ---------------------------------------------------------------
            // FIX 3: current_method_reason
            // Text input now has its own name (edit_side_effects_text_value) so
            // it no longer silently overwrites the radio on every submit.
            // ---------------------------------------------------------------
            $methodReasonRadio = $caseData['edit_current_method_reason'] ?? null;
            $sideEffectsText   = trim($caseData['edit_side_effects_text_value'] ?? '');

            if ($methodReasonRadio === 'side effects' && !empty($sideEffectsText)) {
                $resolvedMethodReason = $sideEffectsText;
            } else {
                $resolvedMethodReason = $methodReasonRadio; // "medical condition" or null
            }

            // Signatures
            $signaturePath        = $familyPlanCaseInfo->signature_image;
            $consentSignaturePath = $familyPlanCaseInfo->acknowledgement_consent_signature_image;

            if ($request->filled('edit_family_planning_acknowledgement_signature_data')) {
                if ($familyPlanCaseInfo->signature_image) {
                    Storage::disk('public')->delete($familyPlanCaseInfo->signature_image);
                }
                $signaturePath = $this->saveCanvasSignature($request->edit_family_planning_acknowledgement_signature_data);
            } elseif ($request->hasFile('edit_family_planning_acknowledgement_signature_image')) {
                if ($familyPlanCaseInfo->signature_image) {
                    Storage::disk('public')->delete($familyPlanCaseInfo->signature_image);
                }
                $signaturePath = $this->compressAndSaveSignature($request->file('edit_family_planning_acknowledgement_signature_image'));
            }

            if ($request->filled('edit_family_planning_consent_signature_data')) {
                if ($familyPlanCaseInfo->acknowledgement_consent_signature_image) {
                    Storage::disk('public')->delete($familyPlanCaseInfo->acknowledgement_consent_signature_image);
                }
                $consentSignaturePath = $this->saveCanvasSignature($request->edit_family_planning_consent_signature_data);
            } elseif ($request->hasFile('edit_family_planning_consent_signature_image')) {
                if ($familyPlanCaseInfo->acknowledgement_consent_signature_image) {
                    Storage::disk('public')->delete($familyPlanCaseInfo->acknowledgement_consent_signature_image);
                }
                $consentSignaturePath = $this->compressAndSaveSignature($request->file('edit_family_planning_consent_signature_image'));
            }

            $allowedMethods    = ['Implant', 'Injectable', 'LAM', 'IUD', 'COC', 'SDM', 'BTL', 'POP', 'BBT', 'NSV', 'Condom', 'BOM/CMM/STM'];
            $allowedMethodsMap = array_combine(array_map('strtolower', $allowedMethods), $allowedMethods);

            $normalizedChoosenMethod = null;
            if (!empty($caseData['edit_choosen_method'])) {
                $submitted               = array_map('trim', explode(',', $caseData['edit_choosen_method']));
                $normalized              = array_map(fn($m) => $allowedMethodsMap[strtolower($m)] ?? $m, $submitted);
                $normalizedChoosenMethod = implode(', ', $normalized);
            }

            // Update case record
            $familyPlanCaseInfo->update([
                'client_id'                               => $caseData['edit_client_id'] ?? null,
                'philhealth_no'                           => $caseData['edit_philhealth_no'] ?? null,
                'NHTS'                                    => $caseData['edit_NHTS'] ?? null,
                'client_name'                             => $medical_case_record->patient->full_name,
                'client_first_name'                       => ucwords(strtolower($patientData['edit_client_fname'])),
                'client_middle_name'                      => ucwords(strtolower($patientData['edit_client_MI'] ?? '')),
                'client_last_name'                        => ucwords(strtolower($patientData['edit_client_lname'])),
                'client_address'                          => $newAddress,
                'client_date_of_birth'                    => $patientData['edit_client_date_of_birth'],
                'client_age'                              => $patientData['edit_client_age'],
                'occupation'                              => $patientData['edit_occupation'] ?? null,
                'client_contact_number'                   => $patientData['edit_client_contact_number'],
                'client_civil_status'                     => $patientData['edit_client_civil_status'] ?? null,
                'client_religion'                         => $patientData['edit_client_religion'] ?? null,
                'client_suffix'                           => $patientData['edit_client_suffix'] ?? '',
                'spouse_lname'                            => $caseData['edit_spouse_lname'] ?? null,
                'spouse_fname'                            => $caseData['edit_spouse_fname'] ?? null,
                'spouse_MI'                               => $caseData['edit_spouse_MI'] ?? null,
                'spouse_suffix'                           => $caseData['edit_spouse_suffix'] ?? '',
                'spouse_date_of_birth'                    => $caseData['edit_spouse_date_of_birth'] ?? null,
                'spouse_age'                              => $caseData['edit_spouse_age'] ?? null,
                'spouse_occupation'                       => $caseData['edit_spouse_occupation'] ?? null,
                'number_of_living_children'               => $caseData['edit_number_of_living_children'] ?? null,
                'plan_to_have_more_children'              => $caseData['edit_plan_to_have_more_children'] ?? null,
                'average_montly_income'                   => $caseData['edit_average_montly_income'] ?? null,
                'type_of_patient'                         => $caseData['edit_type_of_patient'] ?? null,
                'new_acceptor_reason_for_FP'              => $resolvedNewAcceptorReason,   // FIX 1
                'current_user_reason_for_FP'              => $currentReason,               // FIX 2
                'current_method_reason'                   => $resolvedMethodReason,         // FIX 3
                'previously_used_method'                  => $previoulyMethod ?? $familyPlanCaseInfo->previously_used_method ?? null,
                'choosen_method'                          => $normalizedChoosenMethod ?? null,
                'signature_image'                         => $signaturePath,
                'date_of_acknowledgement'                 => $caseData['edit_date_of_acknowledgement'] ?? null,
                'acknowledgement_consent_signature_image' => $consentSignaturePath,
                'date_of_acknowledgement_consent'         => $caseData['edit_date_of_acknowledgement_consent'] ?? null,
                'current_user_type'                       => $caseData['edit_current_user_type'] ?? $familyPlanCaseInfo->current_user_type,
                'status'                                  => 'Active',
            ]);

            $familyPlanCaseInfo->medical_history->update([
                'severe_headaches_migraine'         => $medicalHistoryData['edit_severe_headaches_migraine'] ?? null,
                'history_of_stroke'                 => $medicalHistoryData['edit_history_of_stroke'] ?? null,
                'non_traumatic_hemtoma'             => $medicalHistoryData['edit_non_traumatic_hemtoma'] ?? null,
                'history_of_breast_cancer'          => $medicalHistoryData['edit_history_of_breast_cancer'] ?? null,
                'severe_chest_pain'                 => $medicalHistoryData['edit_severe_chest_pain'] ?? null,
                'cough'                             => $medicalHistoryData['edit_cough'] ?? null,
                'jaundice'                          => $medicalHistoryData['edit_jaundice'] ?? null,
                'unexplained_vaginal_bleeding'      => $medicalHistoryData['edit_unexplained_vaginal_bleeding'] ?? null,
                'abnormal_vaginal_discharge'        => $medicalHistoryData['edit_abnormal_vaginal_discharge'] ?? null,
                'abnormal_phenobarbital'            => $medicalHistoryData['edit_abnormal_phenobarbital'] ?? null,
                'smoker'                            => $medicalHistoryData['edit_smoker'] ?? null,
                'with_dissability'                  => $medicalHistoryData['edit_with_dissability'] ?? null,
                'if_with_dissability_specification' => $medicalHistoryData['edit_if_with_dissability_specification'] ?? null,
            ]);

            $familyPlanCaseInfo->obsterical_history->update([
                'G'                                          => $obstericalHistoryData['edit_G'] ?? null,
                'P'                                          => $obstericalHistoryData['edit_P'] ?? null,
                'full_term'                                  => $obstericalHistoryData['edit_full_term'] ?? null,
                'abortion'                                   => $obstericalHistoryData['edit_abortion'] ?? null,
                'premature'                                  => $obstericalHistoryData['edit_premature'] ?? null,
                'living_children'                            => $obstericalHistoryData['edit_living_children'] ?? null,
                'date_of_last_delivery'                      => $obstericalHistoryData['edit_date_of_last_delivery'] ?? null,
                'type_of_last_delivery'                      => $obstericalHistoryData['edit_type_of_last_delivery'] ?? null,
                'date_of_last_delivery_menstrual_period'     => $obstericalHistoryData['edit_date_of_last_delivery_menstrual_period'] ?? null,
                'date_of_previous_delivery_menstrual_period' => $obstericalHistoryData['edit_date_of_previous_delivery_menstrual_period'] ?? null,
                'type_of_menstrual'                          => $obstericalHistoryData['edit_type_of_menstrual'] ?? null,
                'Dysmenorrhea'                               => $obstericalHistoryData['edit_Dysmenorrhea'] ?? null,
                'hydatidiform_mole'                          => $obstericalHistoryData['edit_hydatidiform_mole'] ?? null,
                'ectopic_pregnancy'                          => $obstericalHistoryData['edit_ectopic_pregnancy'] ?? null,
            ]);

            $familyPlanCaseInfo->risk_for_sexually_transmitted_infection->update([
                'infection_abnormal_discharge_from_genital_area' => $riskData['edit_infection_abnormal_discharge_from_genital_area'] ?? null,
                'origin_of_abnormal_discharge'                   => $riskData['edit_origin_of_abnormal_discharge'] ?? null,
                'scores_or_ulcer'                                => $riskData['edit_scores_or_ulcer'] ?? null,
                'pain_or_burning_sensation'                      => $riskData['edit_pain_or_burning_sensation'] ?? null,
                'history_of_sexually_transmitted_infection'      => $riskData['edit_history_of_sexually_transmitted_infection'] ?? null,
                'sexually_transmitted_disease'                   => $riskData['edit_sexually_transmitted_disease'] ?? null,
                'history_of_domestic_violence_of_VAW'            => $riskData['edit_history_of_domestic_violence_of_VAW'] ?? null,
                'unpleasant_relationship_with_partner'           => $riskData['edit_unpleasant_relationship_with_partner'] ?? null,
                'partner_does_not_approve'                       => $riskData['edit_partner_does_not_approve'] ?? null,
                'referred_to'                                    => $riskData['edit_referred_to'] ?? null,
                'reffered_to_others'                             => $riskData['edit_reffered_to_others'] ?? null,
            ]);

            $familyPlanCaseInfo->physical_examinations->update([
                'blood_pressure'              => $physicalExaminationData['edit_blood_pressure'] ?? null,
                'pulse_rate'                  => $physicalExaminationData['edit_pulse_rate'] ?? null,
                'height'                      => $physicalExaminationData['edit_height'] ?? null,
                'weight'                      => $physicalExaminationData['edit_weight'] ?? null,
                'skin_type'                   => $physicalExaminationData['edit_skin_type'] ?? null,
                'conjuctiva_type'             => $physicalExaminationData['edit_conjuctiva_type'] ?? null,
                'breast_type'                 => $physicalExaminationData['edit_breast_type'] ?? null,
                'abdomen_type'                => $physicalExaminationData['edit_abdomen_type'] ?? null,
                'extremites_type'             => $physicalExaminationData['edit_extremites_type'] ?? null,
                'extremites_UID_type'         => $physicalExaminationData['edit_extremites_UID_type'] ?? null,
                'cervical_abnormalities_type' => $physicalExaminationData['edit_cervical_abnormalities_type'] ?? null,
                'cervical_consistency_type'   => $physicalExaminationData['edit_cervical_consistency_type'] ?? null,
                'uterine_position_type'       => $physicalExaminationData['edit_uterine_position_type'] ?? null,
                'uterine_depth_text'          => $physicalExaminationData['edit_uterine_depth_text'] ?? null,
                'neck_type'                   => $physicalExaminationData['edit_neck_type'] ?? null,
            ]);

            // WRA masterlist update
            $wra_masterlist_record = wra_masterlists::where('patient_id', $medical_case_record->patient_id)->first();

            $method_of_FP = [
                'modern'      => ['Implant', 'IUD', 'BTL', 'NSV', 'Injectable', 'COC', 'POP', 'Condom'],
                'traditional' => ['LAM', 'SDM', 'BBT', 'BOM/CMM/STM'],
            ];

            $modern_methods      = [];
            $traditional_methods = [];
            $previouslyMethods   = $caseData['edit_previously_used_method'] ?? null;

            if ($previouslyMethods) {
                foreach ($caseData['edit_previously_used_method'] as $method) {
                    if (in_array($method, $method_of_FP['modern'])) {
                        $modern_methods[] = $method;
                    } elseif (in_array($method, $method_of_FP['traditional'])) {
                        $traditional_methods[] = $method;
                    }
                }
            }

            $converted_modern_methods      = implode(",", $modern_methods);
            $converted_traditional_methods = implode(",", $traditional_methods);

            $method_accepted = [];
            if (!empty($normalizedChoosenMethod)) {
                $method_accepted = array_map('trim', explode(',', $normalizedChoosenMethod));
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
                    'brgy_name'                        => $address->purok,
                    'name_of_wra'                      => $medical_case_record->patient->full_name,
                    'address'                          => $newAddress,
                    'age'                              => $patientData['edit_client_age'] ?? null,
                    'date_of_birth'                    => $patientData['edit_client_date_of_birth'] ?? $wra_masterlist_record->date_of_birth,
                    'SE_status'                        => ($caseData['edit_NHTS'] ?? null) === 'yes'
                        ? 'NHTS'
                        : (($caseData['edit_NHTS'] ?? null) !== null ? 'Yes' : 'No'),
                    'plan_to_have_more_children_yes'   => ($caseData['edit_plan_to_have_more_children'] ?? null) === 'Yes'
                        ? collect([
                            $resolvedNewAcceptorReason,
                            $currentReason,
                            $resolvedMethodReason,
                        ])->first(fn($value) => !empty($value))
                        : null,
                    'plan_to_have_more_children_no'    => ($caseData['edit_plan_to_have_more_children'] ?? null) === 'No' ? 'limiting' : null,
                    'current_FP_methods'               => ($caseData['edit_type_of_patient'] ?? null) === 'current user'
                        ? $previoulyMethod
                        : $wra_masterlist_record->current_FP_methods,
                    'modern_FP'                        => $converted_modern_methods ?? null,
                    'traditional_FP'                   => $converted_traditional_methods ?? null,
                    'currently_using_any_FP_method_no' => empty($caseData['edit_previously_used_method']) ? 'yes' : null,
                    'wra_accept_any_modern_FP_method'  => $converted_accepted_modern_FP != null ? 'yes' : 'no',
                    'selected_modern_FP_method'        => $converted_accepted_modern_FP ?? null,
                    'date_when_FP_method_accepted'     => !empty($converted_accepted_modern_FP)
                        ? ($caseData['edit_date_of_acknowledgement'] ?? $wra_masterlist_record->date_when_FP_method_accepted)
                        : $wra_masterlist_record->date_when_FP_method_accepted,
                ]);
            }

            $sideBrecord = family_planning_side_b_records::where('medical_record_case_id', $medical_case_record->id)->first();

            if ($sideBrecord) {
                $sideBrecord->update([
                    'method_accepted' => $normalizedChoosenMethod ?? $sideBrecord->method_accepted,
                    'date_of_visit'   => $caseData['edit_date_of_acknowledgement'] ?? $sideBrecord->date_of_visit,
                ]);
            }

            return response()->json(['message' => 'Family Planning Patient Case information is updated Successfully'], 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }
    }


    public function addSideBrecord(Request $request)
    {
        try {
            $data = $request->validate([
                'side_b_medical_record_case_id'                          => 'required',
                'side_b_health_worker_id'                                => 'required',
                'side_b_date_of_visit'                                   => 'required|date|before_or_equal:today',
                'side_b_medical_findings'                                => 'sometimes|nullable|string',
                'side_b_method_accepted'                                 => 'required|array|min:1',
                'side_b_method_accepted.*'                               => 'string',
                'add_side_b_signature_image'                             => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'add_side_b_signature_data'                              => 'sometimes|nullable|string',
                'side_b_date_of_follow_up_visit'                         => [
                    'required',
                    'date',
                    'before_or_equal:' . now()->addYears()->toDateString(),
                ],
                'baby_Less_than_six_months_question'                     => 'sometimes|nullable|string',
                'sexual_intercouse_or_mesntrual_period_question'         => 'sometimes|nullable|string',
                'baby_last_4_weeks_question'                             => 'sometimes|nullable|string',
                'menstrual_period_in_seven_days_question'                => 'sometimes|nullable|string',
                'miscarriage_or_abortion_question'                       => 'sometimes|nullable|string',
                'contraceptive_question'                                 => 'sometimes|nullable|string',
                'is_final'                                               => 'required|in:0,1',
            ], [
                'side_b_medical_record_case_id.required'                 => 'The medical record case is required.',
                'side_b_health_worker_id.required'                       => 'Please select the health worker assigned to this record.',
                'side_b_date_of_visit.required'                          => 'The date of visit is required.',
                'side_b_date_of_visit.date'                              => 'Please enter a valid date of visit.',
                'side_b_date_of_visit.before_or_equal'                   => 'The date of visit cannot be a future date.',
                'side_b_method_accepted.required'                        => 'Please select at least one method accepted.',
                'side_b_method_accepted.array'                           => 'The method accepted must be a list of selected methods.',
                'side_b_method_accepted.min'                             => 'Please select at least one method accepted.',
                'add_side_b_signature_image.image'                       => 'The signature must be an image file.',
                'add_side_b_signature_image.mimes'                       => 'The signature must be a jpg, jpeg, or png file.',
                'add_side_b_signature_image.max'                         => 'The signature may not exceed :max kilobytes.',
                'side_b_date_of_follow_up_visit.required'                => 'The follow up visit date is required.',
                'side_b_date_of_follow_up_visit.date'                    => 'Please enter a valid follow up visit date.',
                'side_b_date_of_follow_up_visit.before_or_equal'         => 'The Follow-up visit date cannot be more than 1 year in the future.',
                'is_final.required'                                      => 'The final record field is required.',
                'is_final.in'                                            => 'The final record field must be 0 or 1.',
            ]);

            $caseId = $data['side_b_medical_record_case_id'];

            if ($request->filled('side_b_date_of_follow_up_visit')) {
                $duplicateDate = family_planning_side_b_records::where('medical_record_case_id', $caseId)
                    ->where('status', 'Active')
                    ->where('date_of_follow_up_visit', $data['side_b_date_of_follow_up_visit'])
                    ->exists();

                if ($duplicateDate) {
                    return response()->json([
                        'errors' => ['side_b_date_of_follow_up_visit' => ['A record with this follow-up visit date already exists for this case.']]
                    ], 422);
                }
            }

            $alreadyFinal = family_planning_side_b_records::where('medical_record_case_id', $caseId)
                ->where('is_final', true)
                ->where('status', 'Active')
                ->exists();

            if ($alreadyFinal) {
                return response()->json([
                    'errors' => [
                        'is_final' => ['This case has already been closed. No new records can be added.'],
                    ],
                ], 422);
            }

            $sideBsignaturePath = null;

            if ($request->hasFile('add_side_b_signature_image')) {
                $sideBsignaturePath = $this->compressAndSaveSignature($request->file('add_side_b_signature_image'));
            } elseif ($request->filled('add_side_b_signature_data')) {
                $sideBsignaturePath = $this->saveCanvasSignature($request->add_side_b_signature_data);
            }

            family_planning_side_b_records::create([
                'medical_record_case_id'                            => $data['side_b_medical_record_case_id'],
                'health_worker_id'                                  => $data['side_b_health_worker_id'],
                'date_of_visit'                                     => $data['side_b_date_of_visit'] ?? null,
                'medical_findings'                                  => $data['side_b_medical_findings'] ?? null,
                'method_accepted'                                   => !empty($data['side_b_method_accepted']) ? implode(', ', $data['side_b_method_accepted']) : null,
                'signature_of_the_provider'                         => $sideBsignaturePath ?? null,
                'date_of_follow_up_visit'                           => $data['side_b_date_of_follow_up_visit'] ?? null,
                'baby_Less_than_six_months_question'                => $data['baby_Less_than_six_months_question'] ?? null,
                'sexual_intercouse_or_mesntrual_period_question'    => $data['sexual_intercouse_or_mesntrual_period_question'] ?? null,
                'baby_last_4_weeks_question'                        => $data['baby_last_4_weeks_question'] ?? null,
                'menstrual_period_in_seven_days_question'           => $data['menstrual_period_in_seven_days_question'] ?? null,
                'miscarriage_or_abortion_question'                  => $data['miscarriage_or_abortion_question'] ?? null,
                'contraceptive_question'                            => $data['contraceptive_question'] ?? null,
                'is_final'                                          => (bool) $data['is_final'],
                'status'                                            => 'Active',
            ]);

            return response()->json(['message' => 'Family Planning Assessment Record Successfully Added'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function sideBrecords($id)
    {
        try {
            $sideBrecord = family_planning_side_b_records::findorFail($id);
            // case_is_final: true if ANY active record in this case has is_final = true
            $caseIsFinal = family_planning_side_b_records::where('medical_record_case_id', $sideBrecord->medical_record_case_id)
                ->where('is_final', true)
                ->where('status', 'Active')
                ->exists();

            return response()->json([
                'sideBrecord'          => $sideBrecord,
                'case_is_final'        => $caseIsFinal,
                'this_record_is_final' => (bool) $sideBrecord->is_final,
            ], 200);
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
                'edit_side_b_medical_record_case_id'                          => 'required',
                'edit_side_b_health_worker_id'                                => 'required',
                'edit_side_b_date_of_visit'                                   => 'required|date|before_or_equal:today',
                'edit_side_b_medical_findings'                                => 'sometimes|nullable|string',
                'edit_side_b_method_accepted'                                 => 'required|array|min:1',
                'edit_side_b_method_accepted.*'                               => 'string',
                'edit_side_b_signature_image'                                 => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:512',
                'edit_side_b_signature_data'                                  => 'sometimes|nullable|string',
                'edit_side_b_date_of_follow_up_visit'                         => [
                    'required',
                    'date',
                    'before_or_equal:' . now()->addYears()->toDateString(),
                ],
                'edit_baby_Less_than_six_months_question'                     => 'sometimes|nullable|string',
                'edit_sexual_intercouse_or_mesntrual_period_question'         => 'sometimes|nullable|string',
                'edit_baby_last_4_weeks_question'                             => 'sometimes|nullable|string',
                'edit_menstrual_period_in_seven_days_question'                => 'sometimes|nullable|string',
                'edit_miscarriage_or_abortion_question'                       => 'sometimes|nullable|string',
                'edit_contraceptive_question'                                 => 'sometimes|nullable|string',
                'is_final'                                                    => 'required|in:0,1',
            ], [
                'edit_side_b_medical_record_case_id.required'                 => 'The medical record case is required.',
                'edit_side_b_health_worker_id.required'                       => 'Please select the health worker assigned to this record.',
                'edit_side_b_date_of_visit.required'                          => 'The date of visit is required.',
                'edit_side_b_date_of_visit.date'                              => 'Please enter a valid date of visit.',
                'edit_side_b_date_of_visit.before_or_equal'                   => 'The date of visit cannot be a future date.',
                'edit_side_b_method_accepted.required'                        => 'Please select at least one method accepted.',
                'edit_side_b_method_accepted.array'                           => 'The method accepted must be a list of selected methods.',
                'edit_side_b_method_accepted.min'                             => 'Please select at least one method accepted.',
                'edit_side_b_signature_image.image'                           => 'The signature must be an image file.',
                'edit_side_b_signature_image.mimes'                           => 'The signature must be a jpg, jpeg, or png file.',
                'edit_side_b_signature_image.max'                             => 'The signature may not exceed :max kilobytes.',
                'edit_side_b_date_of_follow_up_visit.date'                    => 'Please enter a valid follow up visit date.',
                'edit_side_b_date_of_follow_up_visit.before_or_equal'         => 'The follow up visit date cannot be more than 1 year in the future.',
                'is_final.required'                                           => 'The final record field is required.',
                'is_final.in'                                                 => 'The final record field must be 0 or 1.',
            ]);

            // Guard: only the latest active record may be marked as final
            if ((bool) $data['is_final']) {
                $latestRecord = family_planning_side_b_records::where('medical_record_case_id', $sideBrecord->medical_record_case_id)
                    ->where('status', 'Active')
                    ->orderByDesc('created_at')
                    ->first();

                if ($latestRecord && $latestRecord->id !== $sideBrecord->id) {
                    return response()->json([
                        'errors' => [
                            'is_final' => ['Only the most recent record can be marked as final.'],
                        ],
                    ], 422);
                }
            }

            $duplicateDate = family_planning_side_b_records::where('medical_record_case_id', $sideBrecord->medical_record_case_id)
                ->where('status', 'Active')
                ->where('date_of_follow_up_visit', $data['edit_side_b_date_of_follow_up_visit'])
                ->where('id', '!=', $sideBrecord->id)
                ->exists();

            if ($duplicateDate) {
                return response()->json([
                    'errors' => ['edit_side_b_date_of_follow_up_visit' => ['A record with this follow-up visit date already exists for this case.']]
                ], 422);
            }

            $signaturePath = $sideBrecord->signature_of_the_provider;

            if ($request->filled('edit_side_b_signature_data')) {
                if ($sideBrecord->signature_of_the_provider) {
                    Storage::disk('public')->delete($sideBrecord->signature_of_the_provider);
                }
                $signaturePath = $this->saveCanvasSignature($request->edit_side_b_signature_data);
            } elseif ($request->hasFile('edit_side_b_signature_image')) {
                if ($sideBrecord->signature_of_the_provider) {
                    Storage::disk('public')->delete($sideBrecord->signature_of_the_provider);
                }
                $signaturePath = $this->compressAndSaveSignature($request->file('edit_side_b_signature_image'));
            }

            $sideBrecord->update([
                'medical_record_case_id'                            => $data['edit_side_b_medical_record_case_id'],
                'health_worker_id'                                  => $data['edit_side_b_health_worker_id'],
                'date_of_visit'                                     => $data['edit_side_b_date_of_visit'] ?? $sideBrecord->date_of_visit,
                'medical_findings'                                  => $data['edit_side_b_medical_findings'] ?? null,
                'method_accepted'                                   => !empty($data['edit_side_b_method_accepted']) ? implode(', ', $data['edit_side_b_method_accepted']) : null,
                'signature_of_the_provider'                         => $signaturePath,
                'date_of_follow_up_visit'                           => $data['edit_side_b_date_of_follow_up_visit'] ?? null,
                'baby_Less_than_six_months_question'                => $data['edit_baby_Less_than_six_months_question'] ?? null,
                'sexual_intercouse_or_mesntrual_period_question'    => $data['edit_sexual_intercouse_or_mesntrual_period_question'] ?? null,
                'baby_last_4_weeks_question'                        => $data['edit_baby_last_4_weeks_question'] ?? null,
                'menstrual_period_in_seven_days_question'           => $data['edit_menstrual_period_in_seven_days_question'] ?? null,
                'miscarriage_or_abortion_question'                  => $data['edit_miscarriage_or_abortion_question'] ?? null,
                'contraceptive_question'                            => $data['edit_contraceptive_question'] ?? null,
                'is_final'                                          => (bool) $data['is_final'],
                'status'                                            => 'Active',
            ]);

            return response()->json(['message' => 'Family Planning Assessment Record Successfully Updated'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
