<?php

namespace App\Http\Controllers;

use App\Mail\PatientAccountCreated;
use App\Models\medical_record_cases;
use App\Models\nurses;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\staff;
use App\Models\User;
use App\Models\users_address;
use App\Models\vaccination_case_records;
use App\Models\vaccination_masterlists;
use App\Models\vaccination_medical_records;
use App\Models\vaccineAdministered;
use App\Models\vaccines;
use App\Services\WraMasterlistService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str as SupportStr;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Psy\Util\Str;

class addPatientController extends Controller
{
    public function dashboard()
    {
        $healthworkers = User::where('role','staff')->orderBy('first_name', 'ASC')->where('status','active')->get();
        $vaccines = vaccines::get();
        $staffFullName = '';
        $nurseFullName = nurses::value('full_name') ?? 'N/A';
        if (Auth::user()->role == 'staff') {
            $staff = staff::where("user_id", Auth::user()->id)->first();
            $staffFullName = $staff->full_name;
        }
        return view('add_patient.add_patient', [
            'isActive' => true,
            'page' => 'ADD PATIENT',
            'healthworkers' => $healthworkers,
            'vaccines' => $vaccines,
            'healthWorkerFullName' => $staffFullName,
            'nurseFullName' => $nurseFullName
        ]);
    }

    // Function to generate secure password
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


    public function addVaccinationPatient(Request $request)
    {
        try {
            $data = $request->validate([
                'patient_id'              => 'nullable|exists:patients,id',
                'type_of_patient'         => 'required',
                'first_name'              => [
                    'required',
                    'string',
                    Rule::unique('patients')->where(function ($query) use ($request) {
                        return $query->where('first_name', $request->first_name)
                            ->where('last_name', $request->last_name);
                    })->ignore($request->patient_id)
                ],
                'last_name'               => 'required|string',
                'middle_initial'          => 'sometimes|nullable|string',
                'date_of_birth'           => 'required|date|before_or_equal:today',
                'place_of_birth'          => 'sometimes|nullable|string',
                'age'                     => 'sometimes|nullable|numeric',
                'sex'                     => 'required|string',
                'contact_number'          => 'required|digits_between:7,12',
                'nationality'             => 'sometimes|nullable|string',
                'date_of_registration'    => 'required|date',
                'handled_by'              => 'required|exists:users,id',
                'mother_name'             => 'sometimes|nullable|string',
                'father_name'             => 'sometimes|nullable|string',
                'civil_status'            => 'sometimes|nullable|string',
                'street'                  => 'required|string',
                'brgy'                    => 'required|string',
                'vaccination_height'      => ['required', 'numeric', 'min:1', 'max:250', 'regex:/^\d+(\.\d{1,2})?$/'],
                'vaccination_weight'      => ['required', 'numeric', 'min:1', 'max:250', 'regex:/^\d+(\.\d{1,2})?$/'],
                'date_of_vaccination'     => 'required|date',
                'time_of_vaccination'     => 'sometimes|nullable|date_format:H:i',
                'selected_vaccines'       => 'required|string',
                'dose_number'             => 'required|numeric',
                'remarks'                 => 'sometimes|nullable|string',
                'current_height'          => ['nullable', 'numeric', 'between:1,250'],
                'current_weight'          => ['nullable', 'numeric', 'between:1,300'],
                'current_temperature'     => ['nullable', 'numeric', 'between:35,42'],
                'date_of_comeback'        => 'required|date',
                'suffix'                  => 'sometimes|nullable|string',

                // Guardian account — optional, only used when notification_mode = guardian
                'guardian_account_id'     => 'nullable|exists:users,id',

                // Email: required only if NOT using guardian and NOT existing patient
                'email' => array_filter([
                    !$request->filled('guardian_account_id') && !$request->filled('patient_id')
                        ? 'required_without:patient_id'
                        : 'nullable',
                    'email',
                    !$request->user_account && !$request->patient_id && !$request->filled('guardian_account_id')
                        ? Rule::unique('users', 'email')
                        : null,
                ]),

                'user_account'            => 'sometimes|nullable|numeric',
            ], [
                'patient_id.exists'                  => 'The selected patient record does not exist.',
                'first_name.required'                => 'The first name field is required.',
                'first_name.string'                  => 'The first name must be a string.',
                'first_name.unique'                  => 'This patient already exists.',
                'last_name.required'                 => 'The last name field is required.',
                'last_name.string'                   => 'The last name must be a string.',
                'date_of_birth.required'             => 'The date of birth field is required.',
                'date_of_birth.date'                 => 'The date of birth must be a valid date.',
                'date_of_birth.before_or_equal'      => 'The date of birth must be today or earlier.',
                'sex.required'                       => 'The sex field is required.',
                'contact_number.required'            => 'The contact number field is required.',
                'contact_number.digits_between'      => 'The contact number must be between :min and :max digits.',
                'date_of_registration.required'      => 'The date of registration field is required.',
                'date_of_registration.date'          => 'The date of registration must be a valid date.',
                'street.required'                    => 'The street field is required.',
                'brgy.required'                      => 'The barangay field is required.',
                'email.required'                     => 'The email field is required.',
                'handled_by.required'                => 'The health worker field is required.',
                'handled_by.exists'                  => 'The selected health worker does not exist.',
                'guardian_account_id.exists'         => 'The selected guardian account does not exist.',
                'vaccination_height.required'        => 'The birth height field is required.',
                'vaccination_height.numeric'         => 'The birth height must be a number.',
                'vaccination_height.min'             => 'The birth height must be at least :min cm.',
                'vaccination_height.max'             => 'The birth height may not be greater than :max cm.',
                'vaccination_height.regex'           => 'The birth height format is invalid.',
                'vaccination_weight.required'        => 'The birth weight field is required.',
                'vaccination_weight.numeric'         => 'The birth weight must be a number.',
                'vaccination_weight.min'             => 'The birth weight must be at least :min kg.',
                'vaccination_weight.max'             => 'The birth weight may not be greater than :max kg.',
                'vaccination_weight.regex'           => 'The birth weight format is invalid.',
                'current_height.numeric'             => 'The current height must be a number.',
                'current_height.between'             => 'The current height must be between :min and :max cm.',
                'current_weight.numeric'             => 'The current weight must be a number.',
                'current_weight.between'             => 'The current weight must be between :min and :max kg.',
                'current_temperature.numeric'        => 'The current temperature must be a number.',
                'current_temperature.between'        => 'The current temperature must be between :min and :max °C.',
            ]);

            // ============================================================================
            // DETERMINE handled_by
            // ============================================================================
            if ($request->filled('patient_id')) {
                $handledBy = $data['handled_by_backup'] ?? null;
                if (!$handledBy) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors'  => ['handled_by' => ['The health worker field is required for existing patients.']]
                    ], 422);
                }
            } else {
                $handledBy = $data['handled_by'] ?? null;
                if (!$handledBy) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors'  => ['handled_by' => ['The health worker field is required.']]
                    ], 422);
                }
            }

            // ============================================================================
            // DETERMINE notification mode
            // guardian_account_id present = link guardian, skip creating user account
            // ============================================================================
            $guardianAccountId = $data['guardian_account_id'] ?? null;
            $isGuardianMode    = !empty($guardianAccountId);

            // ============================================================================
            // HANDLE EXISTING PATIENT RECORD
            // ============================================================================
            if ($request->filled('patient_id')) {
                $vaccinationPatient = patients::with('address')->findOrFail($data['patient_id']);

                $existingCase = medical_record_cases::where('patient_id', $vaccinationPatient->id)
                    ->where('type_of_case', $data['type_of_patient'])
                    ->where('status', 'Active')
                    ->first();

                if ($existingCase) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors'  => ['type_of_patient' => ['This patient already has an active vaccination case.']]
                    ], 422);
                }

                // -----------------------------------------------------------------------
                // UPDATE existing patient information (ported from Doc 2)
                // -----------------------------------------------------------------------
                $middle        = substr($data['middle_initial'] ?? '', 0, 1);
                $middle        = $middle ? strtoupper($middle) . '.' : null;
                $middleInitial = $data['middle_initial'] ? ucwords($data['middle_initial']) : '';
                $fullName      = ucwords(trim(implode(' ', array_filter([
                    strtolower($data['first_name']),
                    $middle,
                    strtolower($data['last_name']),
                    $data['suffix'] ?? null,
                ]))));
                $ageInYears = Carbon::parse($data['date_of_birth'])->age;

                $vaccinationPatient->update([
                    'first_name'           => ucwords(strtolower($data['first_name'])),
                    'middle_initial'       => $middleInitial,
                    'last_name'            => ucwords(strtolower($data['last_name'])),
                    'full_name'            => $fullName,
                    'age'                  => $ageInYears ?? 0,
                    'age_in_months'        => $this->calculateAgeInMonths($data['date_of_birth']),
                    'sex'                  => isset($data['sex']) ? ucfirst($data['sex']) : $vaccinationPatient->sex,
                    'civil_status'         => $data['civil_status'] ?? $vaccinationPatient->civil_status,
                    'contact_number'       => $data['contact_number'] ?? null,
                    'date_of_birth'        => $data['date_of_birth'] ?? null,
                    'nationality'          => $data['nationality'] ?? null,
                    'date_of_registration' => $data['date_of_registration'] ?? null,
                    'place_of_birth'       => $data['place_of_birth'] ?? null,
                    'suffix'               => $data['suffix'] ?? null,
                ]);

                // UPDATE address if it exists
                $blk_n_street = explode(',', $data['street']);
                if ($vaccinationPatient->address) {
                    $vaccinationPatient->address->update([
                        'house_number' => $blk_n_street[0],
                        'street'       => $blk_n_street[1] ?? null,
                        'purok'        => $data['brgy'],
                    ]);
                }
                // -----------------------------------------------------------------------

                $vaccinationPatientId = $vaccinationPatient->id;

                $patientAddress = $vaccinationPatient->fresh('address')->address;
                if (!$patientAddress) {
                    return response()->json([
                        'message' => 'Patient address not found.',
                        'errors'  => ['patient_id' => ['The selected patient does not have an address record.']]
                    ], 422);
                }

                $message = 'Vaccination case added and patient information updated successfully.';
            } else {
                // ============================================================================
                // CREATE NEW PATIENT RECORD
                // ============================================================================

                $middle        = substr($data['middle_initial'] ?? '', 0, 1);
                $middle        = $middle ? strtoupper($middle) . '.' : null;
                $middleInitial = $data['middle_initial'] ? ucwords($data['middle_initial']) : '';
                $parts         = [
                    strtolower($data['first_name']),
                    $middle,
                    strtolower($data['last_name']),
                    $data['suffix'] ?? null,
                ];
                $blk_n_street = explode(',', $data['street']);
                $fullName     = ucwords(trim(implode(' ', array_filter($parts))));
                $ageInYears   = Carbon::parse($data['date_of_birth'])->age;

                // Validate user account matching (only if patient account is linked, not guardian mode)
                if ($data['user_account'] && !$isGuardianMode) {
                    $errors = [];

                    try {
                        $user = User::with('user_address')->findOrFail((int)$data['user_account']);

                        if ($user->email != $data['email']) {
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

                        if ($data['brgy'] != $user->user_address->purok) {
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
                $vaccinationPatient = patients::create([
                    'user_id'             => null,
                    'guardian_user_id'    => $isGuardianMode ? $guardianAccountId : null, // NEW
                    'first_name'          => ucwords(strtolower($data['first_name'])),
                    'middle_initial'      => $middleInitial,
                    'last_name'           => ucwords(strtolower($data['last_name'])),
                    'full_name'           => $fullName,
                    'age'                 => $ageInYears ?? 0,
                    'age_in_months'       => $this->calculateAgeInMonths($data['date_of_birth']),
                    'sex'                 => isset($data['sex']) ? ucfirst($data['sex']) : null,
                    'civil_status'        => $data['civil_status'] ?? null,
                    'contact_number'      => $data['contact_number'] ?? null,
                    'date_of_birth'       => $data['date_of_birth'] ?? null,
                    'profile_image'       => 'images/default_profile.png',
                    'nationality'         => $data['nationality'] ?? null,
                    'date_of_registration' => $data['date_of_registration'] ?? null,
                    'place_of_birth'      => $data['place_of_birth'] ?? null,
                    'suffix'              => $data['suffix'] ?? null,
                    'status'              => 'Active',
                ]);

                // ----------------------------------------------------------------
                // ACCOUNT HANDLING: Guardian mode vs Patient account vs New account
                // ----------------------------------------------------------------
                if ($isGuardianMode) {
                    // GUARDIAN MODE: no user account created for the patient
                    // Guardian's existing account is already linked via guardian_user_id above
                    // Notifications will go to guardian's account
                    // No email sent, no password created

                } elseif ($data['user_account']) {
                    // EXISTING USER ACCOUNT: link and update
                    try {
                        $user = User::with('user_address')->findOrFail((int)$data['user_account']);

                        $user->update([
                            'patient_record_id' => $vaccinationPatient->id,
                            'first_name'        => ucwords(strtolower($data['first_name'])),
                            'middle_initial'    => $middleInitial,
                            'last_name'         => ucwords(strtolower($data['last_name'])),
                            'full_name'         => $fullName,
                            'email'             => $data['email'],
                            'contact_number'    => $data['contact_number'] ?? null,
                            'date_of_birth'     => $data['date_of_birth'] ?? null,
                            'suffix'            => $data['suffix'] ?? null,
                            'patient_type'      => $data['type_of_patient'],
                            'role'              => 'patient',
                            'status'            => 'active',
                        ]);

                        $vaccinationPatient->update(['user_id' => $user->id]);

                        if ($user->user_address) {
                            $user->user_address->update([
                                'patient_id'   => $vaccinationPatient->id,
                                'house_number' => $blk_n_street[0],
                                'street'       => $blk_n_street[1] ?? null,
                                'purok'        => $data['brgy'],
                            ]);
                        } else {
                            $user->user_address()->create([
                                'patient_id'   => $vaccinationPatient->id,
                                'house_number' => $blk_n_street[0],
                                'street'       => $blk_n_street[1] ?? null,
                                'purok'        => $data['brgy'],
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
                            'patient_record_id' => $vaccinationPatient->id,
                            'first_name'        => ucwords(strtolower($data['first_name'])),
                            'middle_initial'    => $middleInitial,
                            'last_name'         => ucwords(strtolower($data['last_name'])),
                            'full_name'         => $fullName,
                            'email'             => $data['email'],
                            'contact_number'    => $data['contact_number'] ?? null,
                            'date_of_birth'     => $data['date_of_birth'] ?? null,
                            'suffix'            => $data['suffix'] ?? null,
                            'patient_type'      => $data['type_of_patient'],
                            'password'          => Hash::make($temporaryPassword),
                            'role'              => 'patient',
                            'status'            => 'active',
                        ]);

                        $vaccinationPatient->update(['user_id' => $user->id]);

                        Mail::to($user->email)->send(new PatientAccountCreated($user, $temporaryPassword));

                        $user->user_address()->create([
                            'patient_id'   => $vaccinationPatient->id,
                            'house_number' => $blk_n_street[0],
                            'street'       => $blk_n_street[1] ?? null,
                            'purok'        => $data['brgy'],
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'An error occurred while creating patient account.',
                            'errors'  => ['server' => ['Please try again or contact support.']]
                        ], 500);
                    }
                }

                $vaccinationPatientId = $vaccinationPatient->id;

                $patientAddress = patient_addresses::create([
                    'patient_id'   => $vaccinationPatientId,
                    'house_number' => $blk_n_street[0] ?? $data['blk_n_street'],
                    'street'       => $blk_n_street[1] ?? null,
                    'purok'        => $data['brgy'],
                    'postal_code'  => '4109',
                    'latitude'     => null,
                    'longitude'    => null,
                ]);

                $message = 'Vaccination patient information added successfully.';
            }

            // ============================================================================
            // CREATE MEDICAL CASE RECORD (Common for both new and existing patients)
            // ============================================================================

            $medicalCase = medical_record_cases::create([
                'patient_id'   => $vaccinationPatientId,
                'type_of_case' => $data['type_of_patient'],
                'status'       => 'Active',
                'date_of_registration' => $data['date_of_registration'],
            ]);

            $medicalCaseId = $medicalCase->id;

            $vaccinationMedicalRecord = vaccination_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'date_of_registration'   => $data['date_of_registration'] ?? $vaccinationPatient->date_of_registration,
                'mother_name'            => ucwords($data['mother_name'] ?? ''),
                'father_name'            => ucwords($data['father_name'] ?? ''),
                'birth_height'           => $data['vaccination_height'] ?? null,
                'birth_weight'           => $data['vaccination_weight'] ?? null,
                'type_of_record'         => 'Medical Record',
                'health_worker_id'       => $handledBy,
            ]);

            // Resolve vaccine acronyms
            $vaccines              = explode(',', $data['selected_vaccines']);
            $selectedVaccinesArray = [];

            foreach ($vaccines as $vaccineId) {
                $vaccineText             = vaccines::find($vaccineId);
                $selectedVaccinesArray[] = $vaccineText->vaccine_acronym;
            }

            $selectedVaccines = implode(', ', $selectedVaccinesArray);

            $medicalCaseRecord = vaccination_case_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'patient_name'           => $vaccinationPatient->full_name,
                'date_of_vaccination'    => $data['date_of_vaccination'] ?? null,
                'time'                   => $data['time_of_vaccination'] ?? null,
                'vaccine_type'           => $selectedVaccines,
                'dose_number'            => $data['dose_number'] ?? null,
                'remarks'                => $data['remarks'] ?? null,
                'type_of_record'         => 'Vaccination Record',
                'health_worker_id'       => $handledBy,
                'height'                 => $data['current_height'] ?? null,
                'weight'                 => $data['current_weight'] ?? null,
                'temperature'            => $data['current_temperature'] ?? null,
                'date_of_comeback'       => $data['date_of_comeback'],
                'vaccination_status'     => 'completed',
            ]);

            $medicalCaseRecordId = $medicalCaseRecord->id;

            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);
                vaccineAdministered::create([
                    'vaccination_case_record_id' => $medicalCaseRecordId,
                    'vaccine_type'               => $vaccine->type_of_vaccine,
                    'dose_number'                => $data['dose_number'] ?? null,
                    'vaccine_id'                 => $vaccineId ?? null,
                ]);
            }

            // Vaccination masterlist
            $ageInMonths = null;
            if ($vaccinationPatient->age == 0 && $vaccinationPatient->date_of_birth) {
                $ageInMonths = $this->calculateAgeInMonths($vaccinationPatient->date_of_birth);
            }

            $fullAddress = "$patientAddress->house_number $patientAddress->street $patientAddress->purok $patientAddress->barangay $patientAddress->city $patientAddress->province";

            $nurse = User::with('nurses')->where('role', 'nurse')->first();
            $nurseFullname = $nurse && $nurse->nurses ? ucwords($nurse->nurses->full_name) : 'Unknown';

            $vaccinationMasterlist = vaccination_masterlists::create([
                'brgy_name'              => $patientAddress->purok,
                'midwife'                => "Nurse " . $nurseFullname ?? null,
                'health_worker_id'       => $handledBy,
                'medical_record_case_id' => $medicalCaseId,
                'name_of_child'          => $vaccinationPatient->full_name,
                'patient_id'             => $vaccinationPatient->id,
                'address_id'             => $patientAddress->id,
                'Address'                => trim($fullAddress, " "),
                'sex'                    => $vaccinationPatient->sex,
                'age'                    => $vaccinationPatient->age,
                'age_in_months'          => $ageInMonths,
                'date_of_birth'          => $vaccinationPatient->date_of_birth,
            ]);

            $wraMasterlistService = new WraMasterlistService();
            $wraMasterlistService->createIfNotExists([
                'patient'                => $vaccinationPatient,
                'patient_address'        => $patientAddress,
                'full_address'           => $fullAddress,
                'health_worker_id'       => $handledBy,
                'medical_record_case_id' => $medicalCaseId,
                'wra_with_MFP_unmet_need' => 'yes',
            ]);

            foreach ($vaccines as $vaccineId) {
                $vaccine      = vaccines::find($vaccineId);
                $vaccineText  = $vaccine->vaccine_acronym == 'Hepatitis B' ? $vaccine->vaccine_acronym : SupportStr::upper($vaccine->vaccine_acronym);
                $itemColumn   = $vaccineText == 'Hepatitis B' ? $vaccineText : $vaccineText . "_" . $medicalCaseRecord->dose_number;

                $vaccineTypes = ['BCG', 'Hepatitis B', 'PENTA_1', 'PENTA_2', 'PENTA_3', 'OPV_1', 'OPV_2', 'OPV_3', 'PCV_1', 'PCV_2', 'PCV_3', 'IPV_1', 'IPV_2', 'MCV_1', 'MCV_2'];
                if (in_array($itemColumn, $vaccineTypes)) {
                    $vaccinationMasterlist->update([
                        "$itemColumn" => $medicalCaseRecord->date_of_vaccination,
                    ]);
                }
            }

            return response()->json(['message' => $message], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Vaccination Patient Creation Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']]
            ], 500);
        }
    }

    private function calculateAgeInMonths($dateOfBirth)
    {
        if (!$dateOfBirth) {
            return null;
        }

        $dob = Carbon::parse($dateOfBirth);
        $now = Carbon::now();

        return $dob->diffInMonths($now);
    }
}
