<?php

namespace App\Http\Controllers;

use App\Mail\PatientAccountCreated;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\senior_citizen_case_records;
use App\Models\senior_citizen_maintenance_meds;
use App\Models\senior_citizen_medical_records;
use App\Models\User;
use App\Services\PatientUpdateService;
use App\Services\WraMasterlistService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SeniorCitizenController extends Controller
{
    // generate random password
    public function generateSecurePassword($length = 8)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

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
                'date_of_birth'        => [
                    'required',
                    'date',
                    'before_or_equal:' . now()->subYears(60)->format('Y-m-d'),
                    'before_or_equal:today',
                ],
                'place_of_birth'       => 'sometimes|nullable|string',
                'age'                  => 'sometimes|nullable|numeric|min:60',
                'sex'                  => 'required|string',
                'contact_number'       => 'required|digits_between:7,12',
                'nationality'          => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date|before_or_equal:today', // ← fixed
                'handled_by'           => 'nullable|exists:users,id',
                'handled_by_backup'    => 'nullable|exists:users,id',
                'street'               => 'required',
                'brgy'                 => 'required',
                'civil_status'         => 'sometimes|nullable|string',
                'suffix'               => 'sometimes|nullable|string',
                'guardian_account_id'  => 'nullable|exists:users,id',
                'email' => array_filter([
                    !$request->filled('guardian_account_id') && !$request->filled('patient_id')
                        ? 'required_without:patient_id'
                        : 'nullable',
                    'email',
                    !$request->user_account && !$request->patient_id && !$request->filled('guardian_account_id')
                        ? Rule::unique('users', 'email')
                        : null,
                ]),
                'user_account'         => 'sometimes|nullable|numeric',
            ], [
                'patient_id.exists'                     => 'The selected patient record does not exist.',
                'type_of_patient.required'              => 'The type of patient field is required.',
                'first_name.required_without'           => 'The first name field is required.',
                'first_name.string'                     => 'The first name must be a string.',
                'first_name.unique'                     => 'This patient already exists.',
                'last_name.required_without'            => 'The last name field is required.',
                'last_name.string'                      => 'The last name must be a string.',
                'date_of_birth.required_without'        => 'The date of birth field is required.',
                'date_of_birth.date'                    => 'The date of birth must be a valid date.',
                'date_of_birth.before_or_equal'         => 'You must be at least 60 years old.',
                'age.numeric'                           => 'The age must be a number.',
                'age.min'                               => 'The age must be at least :min.',
                'contact_number.required_without'       => 'The contact number field is required.',
                'contact_number.digits_between'         => 'The contact number must be between :min and :max digits.',
                'date_of_registration.required_without' => 'The date of registration field is required.',
                'date_of_registration.date'             => 'The date of registration must be a valid date.',
                'date_of_registration.before_or_equal'  => 'The date of registration must be today or earlier.', // ← added
                'handled_by.exists'                     => 'The selected health worker does not exist.',
                'handled_by_backup.exists'              => 'The selected health worker does not exist.',
                'guardian_account_id.exists'            => 'The selected guardian account does not exist.',
                'user_account.numeric'                  => 'The user account must be a number.',
            ]);

            $handledBy = $patientData['handled_by'] ?? $patientData['handled_by_backup'] ?? null;

            if (!$handledBy) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => ['handled_by' => ['The health worker field is required.']]
                ], 422);
            }

            $guardianAccountId = $patientData['guardian_account_id'] ?? null;
            $isGuardianMode    = !empty($guardianAccountId);

            $patientMedicalRecord = $request->validate([
                'occupation'       => 'sometimes|nullable|string',
                'religion'         => 'sometimes|nullable|string',
                'SSS'              => 'sometimes|nullable|string',
                'blood_pressure'   => ['sometimes', 'nullable', 'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'],
                'temperature'      => 'nullable|numeric|between:30,45',
                'pulse_rate'       => 'nullable|string|max:20',
                'respiratory_rate' => 'nullable|integer|min:5|max:60',
                'height'           => 'nullable|numeric|between:1,250',
                'weight'           => 'nullable|numeric|between:1,250',
            ], [
                'blood_pressure.regex'     => 'The blood pressure format is invalid.',
                'pulse_rate.string'        => 'The pulse rate must be a string.',
                'pulse_rate.max'           => 'The pulse rate may not be greater than :max characters.',
                'respiratory_rate.integer' => 'The respiratory rate must be an integer.',
                'respiratory_rate.min'     => 'The respiratory rate must be at least :min.',
                'respiratory_rate.max'     => 'The respiratory rate may not be greater than :max.',
            ]);

            $patientCase = $request->validate([
                'existing_medical_condition'      => 'sometimes|nullable|string',
                'alergies'                        => 'sometimes|nullable|string',
                'prescribe_by_nurse'              => 'sometimes|nullable|string',
                'medication_maintenance_remarks'  => 'sometimes|nullable|string',
                'senior_citizen_date_of_comeback' => [
                    'required',
                    'date',
                    'before_or_equal:' . now()->addYears(5)->toDateString(),
                ], // ← fixed
            ], [
                'existing_medical_condition.string'        => 'The existing medical condition must be a string.',
                'prescribe_by_nurse.string'                => 'The prescribe by nurse field must be a string.',
                'medication_maintenance_remarks.string'    => 'The medication maintenance remarks must be a string.',
                'senior_citizen_date_of_comeback.required' => 'The date of comeback field is required.',
                'senior_citizen_date_of_comeback.date'     => 'The date of comeback must be a valid date.',
                'senior_citizen_date_of_comeback.before_or_equal' => 'The date of comeback must be today or a future date.', // ← added
            ]);

            $maintenanceMedicationData = $request->validate([
                'medicines'            => 'sometimes|nullable|array',
                'dosage_n_frequencies' => 'sometimes|nullable|array',
                'maintenance_quantity' => 'sometimes|nullable|array',
                'start_date'           => 'sometimes|nullable|array',
                'end_date'             => 'sometimes|nullable|array',
            ]);

            if ($request->filled('patient_id')) {

                $seniorCitizenPatient = patients::with('address')->findOrFail($patientData['patient_id']);

                $existingCase = medical_record_cases::where('patient_id', $seniorCitizenPatient->id)
                    ->where('type_of_case', $patientData['type_of_patient'])
                    ->where('status', 'Active')
                    ->first();

                if ($existingCase) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors'  => ['type_of_patient' => ['This patient already has an active Senior Citizen case.']]
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

                $seniorCitizenPatient->update([
                    'first_name'           => ucwords(strtolower($patientData['first_name'])),
                    'middle_initial'       => $middleName,
                    'last_name'            => ucwords(strtolower($patientData['last_name'])),
                    'full_name'            => $fullName,
                    'suffix'               => $patientData['suffix'] ?? '',
                    'contact_number'       => $patientData['contact_number'],
                    'nationality'          => $patientData['nationality'] ?? $seniorCitizenPatient->nationality,
                    'date_of_birth'        => $patientData['date_of_birth'],
                    'age'                  => isset($patientData['date_of_birth']) ? \Carbon\Carbon::parse($patientData['date_of_birth'])->age : $seniorCitizenPatient->age,
                    'place_of_birth'       => $patientData['place_of_birth'] ?? $seniorCitizenPatient->place_of_birth,
                    'civil_status'         => $patientData['civil_status'] ?? $seniorCitizenPatient->civil_status,
                    'sex'                  => isset($patientData['sex']) ? ucfirst(strtolower($patientData['sex'])) : $seniorCitizenPatient->sex,
                    'date_of_registration' => $patientData['date_of_registration'],
                ]);

                $blk_n_street         = explode(',', $patientData['street']);
                $seniorCitizenAddress = $seniorCitizenPatient->address;

                if ($seniorCitizenAddress) {
                    $seniorCitizenAddress->update([
                        'house_number' => $blk_n_street[0],
                        'street'       => $blk_n_street[1] ?? null,
                        'purok'        => $patientData['brgy'],
                    ]);
                }

                $seniorCitizenPatientId = $seniorCitizenPatient->id;
                $message = 'Senior Citizen case added to existing patient successfully.';
            } else {
                $middle     = substr($patientData['middle_initial'] ?? '', 0, 1);
                $middle     = $middle ? strtoupper($middle) . '.' : null;
                $middleName = $patientData['middle_initial'] ? ucwords($patientData['middle_initial']) : '';
                $parts      = [
                    strtolower($patientData['first_name']),
                    $middle,
                    strtolower($patientData['last_name']),
                    $patientData['suffix'] ?? null,
                ];
                $blk_n_street = explode(',', $patientData['street']);
                $fullName     = ucwords(trim(implode(' ', array_filter($parts))));

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

                $sex = isset($patientData['sex']) ? ucfirst(strtolower($patientData['sex'])) : null;

                $seniorCitizenPatient = patients::create([
                    'user_id'              => null,
                    'guardian_user_id'     => $isGuardianMode ? $guardianAccountId : null,
                    'first_name'           => ucwords(strtolower($patientData['first_name'])),
                    'middle_initial'       => $middleName,
                    'last_name'            => ucwords(strtolower($patientData['last_name'])),
                    'full_name'            => $fullName,
                    'age'                  => isset($patientData['date_of_birth']) ? \Carbon\Carbon::parse($patientData['date_of_birth'])->age : null,
                    'sex'                  => $sex,
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

                if ($isGuardianMode) {
                    // guardian_user_id already set above
                } elseif ($patientData['user_account']) {
                    try {
                        $user = User::with('user_address')->findOrFail((int)$patientData['user_account']);

                        $user->update([
                            'patient_record_id' => $seniorCitizenPatient->id,
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

                        $seniorCitizenPatient->update(['user_id' => $user->id]);

                        if ($user->user_address) {
                            $user->user_address->update([
                                'patient_id'   => $seniorCitizenPatient->id,
                                'house_number' => $blk_n_street[0],
                                'street'       => $blk_n_street[1] ?? null,
                                'purok'        => $patientData['brgy'],
                            ]);
                        } else {
                            $user->user_address()->create([
                                'patient_id'   => $seniorCitizenPatient->id,
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
                    $temporaryPassword = $this->generateSecurePassword(8);
                    try {
                        $user = User::create([
                            'patient_record_id' => $seniorCitizenPatient->id,
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

                        $seniorCitizenPatient->update(['user_id' => $user->id]);

                        Mail::to($user->email)->send(new PatientAccountCreated($user, $temporaryPassword));

                        $user->user_address()->create([
                            'patient_id'   => $seniorCitizenPatient->id,
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

                $seniorCitizenPatientId = $seniorCitizenPatient->id;

                patient_addresses::create([
                    'patient_id'   => $seniorCitizenPatientId,
                    'house_number' => $blk_n_street[0] ?? $patientData['blk_n_street'],
                    'street'       => $blk_n_street[1] ?? null,
                    'purok'        => $patientData['brgy'],
                    'postal_code'  => '4109',
                    'latitude'     => null,
                    'longitude'    => null,
                ]);

                $message = 'Senior Citizen patient information added successfully.';
            }

            $medicalCase = medical_record_cases::create([
                'patient_id'           => $seniorCitizenPatientId,
                'type_of_case'         => $patientData['type_of_patient'],
                'status'               => 'Active',
                'date_of_registration' => $patientData['date_of_registration'],
            ]);

            $medicalCaseId = $medicalCase->id;

            senior_citizen_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id'       => $handledBy,
                'patient_name'           => $seniorCitizenPatient->full_name,
                'occupation'             => $patientMedicalRecord['occupation'] ?? null,
                'religion'               => $patientMedicalRecord['religion'] ?? null,
                'SSS'                    => $patientMedicalRecord['SSS'] ?? null,
                'blood_pressure'         => $patientMedicalRecord['blood_pressure'] ?? null,
                'temperature'            => $patientMedicalRecord['temperature'] ?? null,
                'pulse_rate'             => $patientMedicalRecord['pulse_rate'] ?? null,
                'respiratory_rate'       => $patientMedicalRecord['respiratory_rate'] ?? null,
                'height'                 => $patientMedicalRecord['height'] ?? null,
                'weight'                 => $patientMedicalRecord['weight'] ?? null,
                'type_of_record'         => 'Medical Record',
            ]);

            $seniorCitizenCase = senior_citizen_case_records::create([
                'medical_record_case_id'     => $medicalCaseId,
                'health_worker_id'           => $handledBy,
                'patient_name'               => $seniorCitizenPatient->full_name,
                'existing_medical_condition' => $patientCase['existing_medical_condition'],
                'alergies'                   => $patientCase['alergies'],
                'prescribe_by_nurse'         => $patientCase['prescribe_by_nurse'],
                'remarks'                    => $patientCase['medication_maintenance_remarks'],
                'type_of_record'             => 'Case Record',
                'date_of_comeback'           => $patientCase['senior_citizen_date_of_comeback'],
                'status'                     => 'Active',
            ]);

            $caseId = $seniorCitizenCase->id;

            if (!empty($maintenanceMedicationData['medicines'])) {
                foreach ($maintenanceMedicationData['medicines'] as $index => $value) {
                    senior_citizen_maintenance_meds::create([
                        'senior_citizen_case_id' => $caseId,
                        'maintenance_medication' => $value,
                        'dosage_n_frequency'     => $maintenanceMedicationData['dosage_n_frequencies'][$index],
                        'quantity'               => $maintenanceMedicationData['maintenance_quantity'][$index],
                        'start_date'             => $maintenanceMedicationData['start_date'][$index],
                        'end_date'               => $maintenanceMedicationData['end_date'][$index],
                    ]);
                }
            }

            $seniorCitizenAddress = $seniorCitizenPatient->fresh('address')->address;

            $fullAddress = collect([
                $seniorCitizenAddress->house_number,
                $seniorCitizenAddress->street,
                $seniorCitizenAddress->purok,
                $seniorCitizenAddress->barangay ?? null,
                $seniorCitizenAddress->city ?? null,
                $seniorCitizenAddress->province ?? null,
            ])->filter()->join(', ');

            $WraMasterlistService = new WraMasterlistService();

            $WraMasterlistService->createIfNotExists([
                'patient'                => $seniorCitizenPatient,
                'patient_address'        => $seniorCitizenAddress,
                'full_address'           => $fullAddress,
                'health_worker_id'       => $handledBy,
                'medical_record_case_id' => $medicalCaseId,
                'wra_with_MFP_unmet_need' => 'yes',
            ]);

            return response()->json(['message' => $message], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Senior Citizen Patient Creation Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']]
            ], 500);
        }
    }

    public function updateDetails(Request $request, $id)
    {
        try {
            $seniorCitizenRecord = medical_record_cases::with(['patient', 'senior_citizen_medical_record', 'senior_citizen_case_record'])
                ->where('type_of_case', 'senior-citizen')
                ->where('status', 'Active')
                ->findOrFail($id);
            $seniorCitizenCase = senior_citizen_case_records::where('medical_record_case_id', $id)->get();
            $address = patient_addresses::where('patient_id', $seniorCitizenRecord->patient->id)->firstorFail();

            $data = $request->validate([
                'first_name' => [
                    'required',
                    'string',
                    Rule::unique('patients')
                        ->ignore($seniorCitizenRecord->patient->id)
                        ->where(function ($query) use ($request) {
                            return $query->where('first_name', $request->first_name)
                                ->where('last_name', $request->last_name);
                        }),
                ],
                'last_name'            => 'required|nullable|string',
                'middle_initial'       => 'sometimes|nullable|string',
                'date_of_birth'        => [
                    'required',
                    'date',
                    'before_or_equal:' . now()->subYears(60)->format('Y-m-d'),
                    'before_or_equal:today',
                ],
                'place_of_birth'       => 'sometimes|nullable|string',
                'age'                  => 'required|numeric',
                'sex'                  => 'sometimes|nullable|string',
                'contact_number'       => 'required|digits_between:7,12',
                'nationality'          => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date|before_or_equal:today', // ← fixed
                'handled_by'           => 'required',
                'civil_status'         => 'sometimes|nullable|string',
                'occupation'           => 'sometimes|nullable|string',
                'SSS'                  => 'sometimes|nullable|string',
                'religion'             => 'sometimes|nullable|string',
                'street'               => 'required',
                'brgy'                 => 'required',
                'blood_pressure'       => [
                    'sometimes',
                    'nullable',
                    'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'
                ],
                'temperature'          => 'nullable|numeric|between:30,45',
                'pulse_rate'           => 'nullable|string|max:20',
                'respiratory_rate'     => 'nullable|integer|min:5|max:60',
                'height'               => 'nullable|numeric|between:1,250',
                'weight'               => 'nullable|numeric|between:1,250',
                'suffix'               => 'nullable|sometimes|string',
            ], [
                'first_name.required'                   => 'The first name field is required.',
                'first_name.string'                     => 'The first name must be a string.',
                'first_name.unique'                     => 'This patient already exists.',
                'last_name.required'                    => 'The last name field is required.',
                'last_name.string'                      => 'The last name must be a string.',
                'date_of_birth.required'                => 'The date of birth field is required.',
                'date_of_birth.date'                    => 'The date of birth must be a valid date.',
                'date_of_birth.before_or_equal'         => 'You must be at least 60 years old.',
                'age.required'                          => 'The age field is required.',
                'age.numeric'                           => 'The age must be a number.',
                'contact_number.required'               => 'The contact number field is required.',
                'contact_number.digits_between'         => 'The contact number must be between :min and :max digits.',
                'date_of_registration.required'         => 'The date of registration field is required.',
                'date_of_registration.date'             => 'The date of registration must be a valid date.',
                'date_of_registration.before_or_equal'  => 'The date of registration must be today or earlier.', // ← added
                'handled_by.required'                   => 'The handled by field is required.',
                'blood_pressure.regex'                  => 'The blood pressure format is invalid.',
                'pulse_rate.string'                     => 'The pulse rate must be a string.',
                'pulse_rate.max'                        => 'The pulse rate may not be greater than :max characters.',
                'respiratory_rate.integer'              => 'The respiratory rate must be an integer.',
                'respiratory_rate.min'                  => 'The respiratory rate must be at least :min.',
                'respiratory_rate.max'                  => 'The respiratory rate may not be greater than :max.',
            ]);

            $middle     = substr($data['middle_initial'] ?? '', 0, 1);
            $middle     = $middle ? strtoupper($middle) . '.' : null;
            $middleName = $data['middle_initial'] ? ucwords(strtolower($data['middle_initial'])) : '';
            $parts      = [
                strtolower($data['first_name']),
                $middle,
                strtolower($data['last_name']),
                $data['suffix'] ?? null,
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));
            $sex      = $data['sex'] ?? '';

            $seniorCitizenRecord->patient->update([
                'first_name'           => ucwords(strtolower($data['first_name'])) ?? ucwords($seniorCitizenRecord->patient->first_name),
                'middle_initial'       => $middleName,
                'last_name'            => ucwords(strtolower($data['last_name'])) ?? ucwords($seniorCitizenRecord->patient->last_name),
                'full_name'            => $fullName ?? ucwords($seniorCitizenRecord->patient->full_name),
                'age'                  => $data['age'] ?? $seniorCitizenRecord->patient->age,
                'sex'                  => $sex ? ucfirst(strtolower($sex)) : null,
                'civil_status'         => $data['civil_status'] ?? '',
                'contact_number'       => $data['contact_number'] ?? '',
                'date_of_birth'        => $data['date_of_birth'] ?? $seniorCitizenRecord->patient->date_of_birth,
                'nationality'          => $data['nationality'] ?? '',
                'date_of_registration' => $data['date_of_registration'] ?? $seniorCitizenRecord->patient->date_of_registration,
                'place_of_birth'       => $data['place_of_birth'] ?? '',
                'suffix'               => $data['suffix'] ?? '',
            ]);

            $seniorCitizenRecord->update([
                'date_of_registration' => $data['date_of_registration'],
            ]);

            $patientUpdateService = new PatientUpdateService();
            if ($seniorCitizenRecord->patient) {
                $patientUpdateService->updatePatientDetails($data, $seniorCitizenRecord->patient->id);
            }

            $blk_n_street = explode(',', $data['street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street'       => $blk_n_street[1] ?? $address->street,
                'purok'        => $data['brgy'] ?? $address->purok,
            ]);

            $seniorCitizenRecord->patient->refresh();

            $seniorCitizenRecord->senior_citizen_medical_record->update([
                'health_worker_id' => $data['handled_by'] ?? $seniorCitizenRecord->senior_citizen_medical_record->health_worker_id,
                'patient_name'     => $seniorCitizenRecord->patient->full_name,
                'occupation'       => $data['occupation'] ?? null,
                'religion'         => $data['religion'] ?? null,
                'SSS'              => $data['SSS'] ?? null,
                'blood_pressure'   => $data['blood_pressure'] ?? null,
                'temperature'      => $data['temperature'] ?? null,
                'pulse_rate'       => $data['pulse_rate'] ?? null,
                'respiratory_rate' => $data['respiratory_rate'] ?? null,
                'height'           => $data['height'] ?? null,
                'weight'           => $data['weight'] ?? null,
            ]);

            foreach ($seniorCitizenCase as $record) {
                $record->update([
                    'patient_name' => $seniorCitizenRecord->patient->full_name ?? $record->patient_name,
                ]);
            }

            return response()->json(['message' => 'Updating Patient information Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function viewCaseDetails($id)
    {
        try {
            $caseRecord = senior_citizen_case_records::with('senior_citizen_maintenance_med')->findOrFail($id);

            // True if ANY record in this case is marked final
            $case_is_final = senior_citizen_case_records::where('medical_record_case_id', $caseRecord->medical_record_case_id)
                ->where('is_final', true)
                ->exists();

            return response()->json([
                'seniorCaseRecord'     => $caseRecord,
                'patient_name'         => $caseRecord->patient_name,
                'case_is_final'        => $case_is_final,
                'this_record_is_final' => (bool) $caseRecord->is_final,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateCase(Request $request, $id)
    {
        try {
            $seniorCitizenCase = senior_citizen_case_records::findOrFail($id);

            // --- FINAL TOGGLE GUARD ---
            // If is_final is being turned on, this record MUST be the last one.
            $isFinal = $request->input('is_final', 0);

            if ($isFinal) {
                $isLastRecord = !senior_citizen_case_records::where('medical_record_case_id', $seniorCitizenCase->medical_record_case_id)
                    ->where('id', '>', $id)
                    ->exists();

                if (!$isLastRecord) {
                    return response()->json([
                        'errors' => [
                            'is_final' => ['Only the most recent record can be marked as the final record.']
                        ]
                    ], 422);
                }
            }

            $data = $request->validate([
                'edit_existing_medical_condition'     => 'sometimes|nullable|string',
                'edit_alergies'                       => 'sometimes|nullable|string',
                'edit_prescribe_by_nurse'             => 'required|string',
                'edit_medication_maintenance_remarks' => 'sometimes|nullable|string',
                'edit_date_of_comeback'               =>  [
                    'required',
                    'date',
                    'before_or_equal:' . now()->addYears(5)->toDateString(),
                ],
            ], [
                'edit_prescribe_by_nurse.string'    => 'The prescribe by nurse field is required.',
                'edit_date_of_comeback.required'    => 'The date of comeback field is required.',
                'edit_date_of_comeback.date'        => 'The date of comeback field must be a valid date.',
                'edit_date_of_comeback.before_or_equal' => 'The date of comeback must be past or only 5 years future date.',
            ]);

            $seniorCitizenCase->update([
                'existing_medical_condition' => $data['edit_existing_medical_condition'] ?? '',
                'alergies'                   => $data['edit_alergies'] ?? '',
                'prescribe_by_nurse'         => $data['edit_prescribe_by_nurse'] ? ucwords($data['edit_prescribe_by_nurse']) : '',
                'remarks'                    => $data['edit_medication_maintenance_remarks'] ?? '',
                'date_of_comeback'           => $data['edit_date_of_comeback'],
                'is_final'                   => (bool) $isFinal,  // <-- save it
            ]);

            // Re-create maintenance meds
            senior_citizen_maintenance_meds::where('senior_citizen_case_id', $id)->delete();

            $maintenanceMedicationData = $request->validate([
                'medicines'            => 'sometimes|nullable|array',
                'dosage_n_frequencies' => 'sometimes|nullable|array',
                'maintenance_quantity' => 'sometimes|nullable|array',
                'start_date'           => 'sometimes|nullable|array',
                'end_date'             => 'sometimes|nullable|array',
            ]);

            if (!empty($maintenanceMedicationData['medicines'])) {
                foreach ($maintenanceMedicationData['medicines'] as $index => $value) {
                    senior_citizen_maintenance_meds::create([
                        'senior_citizen_case_id' => $id,
                        'maintenance_medication' => $value,
                        'dosage_n_frequency'     => $maintenanceMedicationData['dosage_n_frequencies'][$index],
                        'quantity'               => $maintenanceMedicationData['maintenance_quantity'][$index],
                        'start_date'             => $maintenanceMedicationData['start_date'][$index],
                        'end_date'               => $maintenanceMedicationData['end_date'][$index],
                    ]);
                }
            }

            return response()->json(['message' => 'Patient Case Record is successfully updated']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    public function addCase(Request $request, $id)
    {
        try {
            // --- BLOCK IF CASE IS ALREADY CLOSED ---
            $alreadyFinal = senior_citizen_case_records::where('medical_record_case_id', $id)
                ->where('is_final', true)
                ->exists();

            if ($alreadyFinal) {
                return response()->json([
                    'errors' => [
                        'is_final' => ['This case is already closed. No new records can be added.']
                    ]
                ], 422);
            }

            $data = $request->validate([
                'new_patient_name'                   => 'required',
                'add_health_worker_id'               => 'required',
                'add_existing_medical_condition'     => 'sometimes|nullable|string',
                'add_alergies'                       => 'sometimes|nullable|string',
                'add_prescribe_by_nurse'             => 'required|string',
                'add_medication_maintenance_remarks' => 'sometimes|nullable|string',
                'add_date_of_comeback'               => 
                [
                    'required',
                    'date',
                    'before_or_equal:' . now()->addYears(5)->toDateString(),
                ],
            ], [
                'new_patient_name.required'          => 'The patient name field is required.',
                'add_health_worker_id.required'      => 'The health worker id field is required.',
                'add_prescribe_by_nurse.string'      => 'The prescribe by nurse field is required.',
                'add_date_of_comeback.required'      => 'The date of comeback field is required.',
                'add_date_of_comeback.date'          => 'The date of comeback field must be a valid date.',
                'add_date_of_comeback.after_or_equal' => 'The date of comeback must be today or a future date.',
                
            ]);

            $isFinal = $request->input('is_final', 0);

            $newCaseRecord = senior_citizen_case_records::create([
                'patient_name'               => $data['new_patient_name'],
                'medical_record_case_id'     => $id,
                'health_worker_id'           => $data['add_health_worker_id'],
                'existing_medical_condition' => $data['add_existing_medical_condition'] ?? '',
                'alergies'                   => $data['add_alergies'] ?? '',
                'prescribe_by_nurse'         => $data['add_prescribe_by_nurse'] ? ucwords($data['add_prescribe_by_nurse']) : '',
                'remarks'                    => $data['add_medication_maintenance_remarks'] ?? '',
                'type_of_record'             => 'Case Record',
                'date_of_comeback'           => $data['add_date_of_comeback'],
                'is_final'                   => (bool) $isFinal,  // <-- save it
            ]);

            $maintenanceMedicationData = $request->validate([
                'medicines'            => 'sometimes|nullable|array',
                'dosage_n_frequencies' => 'sometimes|nullable|array',
                'maintenance_quantity' => 'sometimes|nullable|array',
                'start_date'           => 'sometimes|nullable|array',
                'end_date'             => 'sometimes|nullable|array',
            ]);

            if (!empty($maintenanceMedicationData['medicines'])) {
                foreach ($maintenanceMedicationData['medicines'] as $index => $value) {
                    senior_citizen_maintenance_meds::create([
                        'senior_citizen_case_id' => $newCaseRecord->id,
                        'maintenance_medication' => $value,
                        'dosage_n_frequency'     => $maintenanceMedicationData['dosage_n_frequencies'][$index],
                        'quantity'               => $maintenanceMedicationData['maintenance_quantity'][$index],
                        'start_date'             => $maintenanceMedicationData['start_date'][$index],
                        'end_date'               => $maintenanceMedicationData['end_date'][$index],
                    ]);
                }
            }

            return response()->json(['message' => 'Patient Case Record is successfully Added']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function removeCase($id)
    {
        try {
            $seniorCitizenCase = senior_citizen_case_records::findOrFail($id);
            if ($seniorCitizenCase) {
                $seniorCitizenCase->update(['status' => 'Archived']);
            }
            return response()->json(['mesage' => 'Senior Citizen Case is successfully deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }
    }
}
