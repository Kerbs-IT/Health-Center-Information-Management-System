<?php

namespace App\Http\Controllers;

use App\Mail\PatientAccountCreated;
use App\Models\gc_case_records;
use App\Models\gc_medical_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\staff;
use App\Models\User;
use App\Services\WraMasterlistService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GeneralConsultation extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $generalConsultationRecord = medical_record_cases::with('patient')->where('type_of_case', 'general-consultation')->where('status','Active')->get();
        return view('records.generalConsultation.index', ['isActive' => true, 'page' => 'RECORD', 'record' => $generalConsultationRecord]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
                'age'                  => 'sometimes|nullable|numeric',
                'sex'                  => 'required|string',
                'contact_number'       => 'required|digits_between:7,12',
                'nationality'          => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date|before_or_equal:today', // ← updated
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
                'user_account' => 'sometimes|nullable|numeric',
            ], [
                'patient_id.exists'                    => 'The selected patient record does not exist.',
                'type_of_patient.required'             => 'The type of patient is required.',
                'first_name.required'                  => 'The first name is required.',
                'first_name.string'                    => 'The first name must be a valid text value.',
                'first_name.unique'                    => 'This patient already exists.',
                'last_name.required'                   => 'The last name is required.',
                'last_name.string'                     => 'The last name must be a valid text value.',
                'date_of_birth.required'               => 'The date of birth is required.',
                'date_of_birth.date'                   => 'Please enter a valid date of birth.',
                'date_of_birth.before_or_equal'        => 'The date of birth cannot be a future date.',
                'age.numeric'                          => 'The age must be a number.',
                'contact_number.required'              => 'The contact number is required.',
                'contact_number.digits_between'        => 'The contact number must be between :min and :max digits.',
                'date_of_registration.required'        => 'The date of registration is required.',
                'date_of_registration.date'            => 'Please enter a valid date of registration.',
                'date_of_registration.before_or_equal' => 'The date of registration cannot be a future date.',
                'handled_by.exists'                    => 'The selected health worker does not exist.',
                'handled_by_backup.exists'             => 'The selected backup health worker does not exist.',
                'guardian_account_id.exists'           => 'The selected guardian account does not exist.',
                'user_account.numeric'                 => 'The user account must be a number.',
                'email.required_without'               => 'The email is required when no guardian account is selected.',
                'email.email'                          => 'Please enter a valid email address.',
                'email.unique'                         => 'This email is already taken.',
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

            // ============================================================================
            // VALIDATE GENERAL CONSULTATION CASE RECORD FIELDS
            // ============================================================================
            $gcCaseData = $request->validate([
                'gc_date'          => 'required|date',
                'gc_symptoms'      => 'required|string',
                'gc_diagnosis'     => 'required|string',
                'gc_treatment'     => 'required|string',
                'blood_pressure'   => ['sometimes', 'nullable', 'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'],
                'temperature'      => 'nullable|numeric|between:30,45',
                'pulse_rate'       => 'nullable|string|max:20',
                'respiratory_rate' => 'nullable|integer|min:5|max:60',
                'height'           => 'nullable|numeric|between:1,250',
                'weight'           => 'nullable|numeric|between:1,250',
            ], [
                'gc_date.required'      => 'The date of consultation is required.',
                'gc_date.date'          => 'The date of consultation must be a valid date.',
                'gc_symptoms.required'  => 'The symptoms / chief complaint field is required.',
                'gc_diagnosis.required' => 'The diagnosis / assessment field is required.',
                'gc_treatment.required' => 'The treatment / plan field is required.',
                'blood_pressure.regex'  => 'The blood pressure format is invalid.',
                'pulse_rate.max'        => 'The pulse rate may not be greater than :max characters.',
                'respiratory_rate.min'  => 'The respiratory rate must be at least :min.',
                'respiratory_rate.max'  => 'The respiratory rate may not be greater than :max.',
            ]);

            // ============================================================================
            // HANDLE EXISTING PATIENT RECORD
            // ============================================================================
            if ($request->filled('patient_id')) {

                $patient = patients::with('address')->findOrFail($patientData['patient_id']);

                $existingCase = medical_record_cases::where('patient_id', $patient->id)
                    ->where('type_of_case', $patientData['type_of_patient'])
                    ->where('status', 'Active')
                    ->first();

                if ($existingCase) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors'  => ['type_of_patient' => ['This patient already has an active General Consultation case.']]
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

                $patient->update([
                    'first_name'           => ucwords(strtolower($patientData['first_name'])),
                    'middle_initial'       => $middleName,
                    'last_name'            => ucwords(strtolower($patientData['last_name'])),
                    'full_name'            => $fullName,
                    'suffix'               => $patientData['suffix'] ?? '',
                    'contact_number'       => $patientData['contact_number'],
                    'nationality'          => $patientData['nationality'] ?? $patient->nationality,
                    'date_of_birth'        => $patientData['date_of_birth'],
                    'age'                  => \Carbon\Carbon::parse($patientData['date_of_birth'])->age,
                    'place_of_birth'       => $patientData['place_of_birth'] ?? $patient->place_of_birth,
                    'civil_status'         => $patientData['civil_status'] ?? $patient->civil_status,
                    'sex'                  => ucfirst(strtolower($patientData['sex'])),
                    'date_of_registration' => $patientData['date_of_registration'],
                ]);

                $blk_n_street   = explode(',', $patientData['street']);
                $patientAddress = $patient->address;

                if ($patientAddress) {
                    $patientAddress->update([
                        'house_number' => $blk_n_street[0],
                        'street'       => $blk_n_street[1] ?? null,
                        'purok'        => $patientData['brgy'],
                    ]);
                }

                $patientId = $patient->id;
                $message   = 'General Consultation case added to existing patient successfully.';
            } else {
                // ============================================================================
                // CREATE NEW PATIENT RECORD
                // ============================================================================
                $middle       = substr($patientData['middle_initial'] ?? '', 0, 1);
                $middle       = $middle ? strtoupper($middle) . '.' : null;
                $middleName   = $patientData['middle_initial'] ? ucwords($patientData['middle_initial']) : '';
                $blk_n_street = explode(',', $patientData['street']);
                $fullName     = ucwords(trim(implode(' ', array_filter([
                    strtolower($patientData['first_name']),
                    $middle,
                    strtolower($patientData['last_name']),
                    $patientData['suffix'] ?? null,
                ]))));

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

                $patient = patients::create([
                    'user_id'              => null,
                    'guardian_user_id'     => $isGuardianMode ? $guardianAccountId : null,
                    'first_name'           => ucwords(strtolower($patientData['first_name'])),
                    'middle_initial'       => $middleName,
                    'last_name'            => ucwords(strtolower($patientData['last_name'])),
                    'full_name'            => $fullName,
                    'age'                  => \Carbon\Carbon::parse($patientData['date_of_birth'])->age,
                    'sex'                  => ucfirst(strtolower($patientData['sex'])),
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
                    // Guardian mode: no user account created, notifications go to guardian

                } elseif ($patientData['user_account']) {
                    // Link existing user account
                    try {
                        $user = User::with('user_address')->findOrFail((int)$patientData['user_account']);

                        $user->update([
                            'patient_record_id' => $patient->id,
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

                        $patient->update(['user_id' => $user->id]);

                        if ($user->user_address) {
                            $user->user_address->update([
                                'patient_id'   => $patient->id,
                                'house_number' => $blk_n_street[0],
                                'street'       => $blk_n_street[1] ?? null,
                                'purok'        => $patientData['brgy'],
                            ]);
                        } else {
                            $user->user_address()->create([
                                'patient_id'   => $patient->id,
                                'house_number' => $blk_n_street[0],
                                'street'       => $blk_n_street[1] ?? null,
                                'purok'        => $patientData['brgy'],
                            ]);
                        }
                    } catch (ModelNotFoundException $e) {
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
                    // Create fresh user account and send credentials
                    $temporaryPassword = $this->generateSecurePassword(8);
                    try {
                        $user = User::create([
                            'patient_record_id' => $patient->id,
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

                        $patient->update(['user_id' => $user->id]);

                        Mail::to($user->email)->send(new PatientAccountCreated($user, $temporaryPassword));

                        $user->user_address()->create([
                            'patient_id'   => $patient->id,
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

                $patientId = $patient->id;

                patient_addresses::create([
                    'patient_id'   => $patientId,
                    'house_number' => $blk_n_street[0],
                    'street'       => $blk_n_street[1] ?? null,
                    'purok'        => $patientData['brgy'],
                    'postal_code'  => '4109',
                    'latitude'     => null,
                    'longitude'    => null,
                ]);

                $message = 'General Consultation patient added successfully.';
            }

            // ============================================================================
            // CREATE MEDICAL CASE RECORD
            // ============================================================================
            $medicalCase = medical_record_cases::create([
                'patient_id'           => $patientId,
                'type_of_case'         => $patientData['type_of_patient'],
                'status'               => 'Active',
                'date_of_registration' => $patientData['date_of_registration'],
            ]);

            $medicalCaseId = $medicalCase->id;

            // ============================================================================
            // CREATE GENERAL CONSULTATION MEDICAL RECORD
            // ============================================================================
            gc_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id'       => $handledBy,
                'patient_name'           => $patient->full_name,
            ]);

            // ============================================================================
            // CREATE GENERAL CONSULTATION CASE RECORD
            // ============================================================================
            gc_case_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id'       => $handledBy,
                'blood_pressure'         => $gcCaseData['blood_pressure'] ?? null,
                'temperature'            => $gcCaseData['temperature'] ?? null,
                'pulse_rate'             => $gcCaseData['pulse_rate'] ?? null,
                'respiratory_rate'       => $gcCaseData['respiratory_rate'] ?? null,
                'height'                 => $gcCaseData['height'] ?? null,
                'weight'                 => $gcCaseData['weight'] ?? null,
                'date_of_consultation'   => $gcCaseData['gc_date'],
                'symptoms'               => $gcCaseData['gc_symptoms'],
                'diagnosis'              => $gcCaseData['gc_diagnosis'],
                'treatment_plan'         => $gcCaseData['gc_treatment'],
                'status'                 => 'Active',
                'type_of_record'         => 'Case Record',
            ]);

            // ============================================================================
            // WRA MASTERLIST
            // ============================================================================
            $patientAddress = $patient->fresh('address')->address;

            $fullAddress = collect([
                $patientAddress->house_number,
                $patientAddress->street,
                $patientAddress->purok,
                $patientAddress->barangay ?? null,
                $patientAddress->city ?? null,
                $patientAddress->province ?? null,
            ])->filter()->join(', ');

            $WraMasterlistService = new WraMasterlistService();

            $WraMasterlistService->createIfNotExists([
                'patient'                 => $patient,
                'patient_address'         => $patientAddress,
                'full_address'            => $fullAddress,
                'health_worker_id'        => $handledBy,
                'medical_record_case_id'  => $medicalCaseId,
                'wra_with_MFP_unmet_need' => 'yes',
            ]);

            return response()->json(['message' => $message], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('General Consultation Patient Creation Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gcRecord = medical_record_cases::with(['patient', 'gc_medical_record'])
            ->where('id', $id)
            ->where('type_of_case', 'general-consultation')
            ->where('status', 'Active')
            ->findOrFail($id);

       

        $address = patient_addresses::where('patient_id', $gcRecord->patient->id)->firstOrFail();

        $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;

        return view('records.generalConsultation.viewPatientDetails', [
            'isActive'    => true,
            'page'        => 'RECORD',
            'gcRecord'    => $gcRecord,
            'fullAddress' => $fullAddress,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gcRecord = medical_record_cases::with(['patient', 'gc_medical_record'])
            ->where('id', $id)
            ->where('type_of_case', 'general-consultation')
            ->where('status', 'Active')
            ->findOrFail($id);

        $address = patient_addresses::where('patient_id', $gcRecord->patient->id)->firstOrFail();

        return view('records.generalConsultation.edit', [
            'isActive' => true,
            'page'     => 'RECORD',
            'gcRecord' => $gcRecord,
            'address'  => $address,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $gcRecord = medical_record_cases::with([
                'patient',
                'patient.address',
                'gc_medical_record',
            ])->findOrFail($id);

            $patientData = $request->validate([
                'first_name' => [
                    'required',
                    'string',
                    Rule::unique('patients')->where(function ($query) use ($request) {
                        return $query->where('first_name', $request->first_name)
                            ->where('last_name', $request->last_name);
                    })->ignore($gcRecord->patient->id),
                ],
                'last_name'            => 'required|string',
                'middle_initial'       => 'sometimes|nullable|string',
                'date_of_birth'        => 'required|date|before_or_equal:today',
                'place_of_birth'       => 'sometimes|nullable|string',
                'age'                  => 'sometimes|nullable|numeric',
                'sex'                  => 'required|string',
                'contact_number'       => 'required|digits_between:7,12',
                'nationality'          => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date|before_or_equal:today', // ← updated
                'handled_by'           => 'required|exists:users,id',
                'street'               => 'required',
                'brgy'                 => 'required',
                'civil_status'         => 'sometimes|nullable|string',
                'suffix'               => 'sometimes|nullable|string',
            ], [
                'first_name.required'                  => 'The first name is required.',
                'first_name.string'                    => 'The first name must be a valid text value.',
                'first_name.unique'                    => 'This patient already exists.',
                'last_name.required'                   => 'The last name is required.',
                'last_name.string'                     => 'The last name must be a valid text value.',
                'date_of_birth.required'               => 'The date of birth is required.',
                'date_of_birth.date'                   => 'Please enter a valid date of birth.',
                'date_of_birth.before_or_equal'        => 'The date of birth cannot be a future date.',
                'age.numeric'                          => 'The age must be a number.',
                'contact_number.required'              => 'The contact number is required.',
                'contact_number.digits_between'        => 'The contact number must be between :min and :max digits.',
                'date_of_registration.required'        => 'The date of registration is required.',
                'date_of_registration.date'            => 'Please enter a valid date of registration.',
                'date_of_registration.before_or_equal' => 'The date of registration cannot be a future date.',
                'handled_by.required'                  => 'Please select the health worker who handled this record.',
                'handled_by.exists'                    => 'The selected health worker does not exist.',
                'street.required'                      => 'The street address is required.',
                'brgy.required'                        => 'The purok is required.',
            ]);
            $middle     = substr($patientData['middle_initial'] ?? '', 0, 1);
            $middle     = $middle ? strtoupper($middle) . '.' : null;
            $middleName = $patientData['middle_initial'] ? ucwords(strtolower($patientData['middle_initial'])) : '';
            $fullName   = ucwords(trim(implode(' ', array_filter([
                strtolower($patientData['first_name']),
                $middle,
                strtolower($patientData['last_name']),
                $patientData['suffix'] ?? null,
            ]))));

            // Update patient
            $gcRecord->patient->update([
                'first_name'           => ucwords(strtolower($patientData['first_name'])),
                'middle_initial'       => $middleName,
                'last_name'            => ucwords(strtolower($patientData['last_name'])),
                'full_name'            => $fullName,
                'suffix'               => $patientData['suffix'] ?? '',
                'contact_number'       => $patientData['contact_number'],
                'nationality'          => $patientData['nationality'] ?? $gcRecord->patient->nationality,
                'date_of_birth'        => $patientData['date_of_birth'],
                'age'                  => \Carbon\Carbon::parse($patientData['date_of_birth'])->age,
                'place_of_birth'       => $patientData['place_of_birth'] ?? $gcRecord->patient->place_of_birth,
                'civil_status'         => $patientData['civil_status'] ?? $gcRecord->patient->civil_status,
                'sex'                  => ucfirst(strtolower($patientData['sex'])),
                'date_of_registration' => $patientData['date_of_registration'],
            ]);

            // Update address
            $blk_n_street   = explode(',', $patientData['street']);
            $patientAddress = $gcRecord->patient->address;

            if ($patientAddress) {
                $patientAddress->update([
                    'house_number' => $blk_n_street[0],
                    'street'       => $blk_n_street[1] ?? null,
                    'purok'        => $patientData['brgy'],
                ]);
            }

            // Update medical case date_of_registration
            $gcRecord->update([
                'date_of_registration' => $patientData['date_of_registration'],
            ]);

            // Update health worker on gc_medical_record
            if ($gcRecord->gc_medical_record) {
                $gcRecord->gc_medical_record->update([
                    'health_worker_id' => $patientData['handled_by'],
                    'patient_name'     => $fullName,
                ]);
            }

            return response()->json(['message' => 'General Consultation patient details updated successfully.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GC Update Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']]
            ], 500);
        }
    }
    public function updateCase(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'update_handled_by'    => 'required|exists:users,id',
                'date_of_consultation' => 'required|date|before_or_equal:today', // ← updated
                'symptoms'             => 'required|string',
                'diagnosis'            => 'required|string',
                'treatment_plan'       => 'required|string',
                'blood_pressure'       => ['nullable', 'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'],
                'temperature'          => 'nullable|numeric|between:30,45',
                'pulse_rate'           => 'nullable|string|max:20',
                'respiratory_rate'     => 'nullable|integer|min:5|max:60',
                'height'               => 'nullable|numeric|between:1,250',
                'weight'               => 'nullable|numeric|between:1,250',
            ], [
                'update_handled_by.required'         => 'Please select the health worker who handled this record.',
                'update_handled_by.exists'           => 'The selected health worker does not exist.',
                'date_of_consultation.required'      => 'The date of consultation is required.',
                'date_of_consultation.date'          => 'Please enter a valid date of consultation.',
                'date_of_consultation.before_or_equal' => 'The date of consultation cannot be a future date.',
                'symptoms.required'                  => 'The symptoms / chief complaint field is required.',
                'diagnosis.required'                 => 'The diagnosis / assessment field is required.',
                'treatment_plan.required'            => 'The treatment / plan field is required.',
                'blood_pressure.regex'               => 'Please enter a valid blood pressure format (e.g., 120/80).',
                'temperature.numeric'                => 'The temperature must be a valid number.',
                'temperature.between'                => 'The temperature must be between 30°C and 45°C.',
                'pulse_rate.max'                     => 'The pulse rate may not exceed :max characters.',
                'respiratory_rate.min'               => 'The respiratory rate must be at least :min breaths per minute.',
                'respiratory_rate.max'               => 'The respiratory rate may not exceed :max breaths per minute.',
                'height.numeric'                     => 'The height must be a valid number.',
                'height.between'                     => 'The height must be between 1 and 250 cm.',
                'weight.numeric'                     => 'The weight must be a valid number.',
                'weight.between'                     => 'The weight must be between 1 and 250 kg.',
            ]);
            $record = gc_case_records::findOrFail($id);

            // Check for duplicates (excluding current record)
            $duplicate = gc_case_records::where('medical_record_case_id', $record->medical_record_case_id)
                ->where('date_of_consultation', $request->date_of_consultation)
                ->where('id', '!=', $id)
                ->where('status', '!=', 'Archived')
                ->exists();

            if ($duplicate) {
                return response()->json([
                    'success' => false,
                    'errors' => ['date_of_consultation' => ['A consultation record for this date already exists for this patient.']]
                ], 422);
            }

            $record->update([
                'health_worker_id'     => $request->update_handled_by,
                'date_of_consultation' => $request->date_of_consultation,
                'symptoms'             => $request->symptoms,
                'diagnosis'            => $request->diagnosis,
                'treatment_plan'       => $request->treatment_plan,
                'blood_pressure'       => $request->blood_pressure ?: null,
                'temperature'          => $request->temperature ?: null,
                'pulse_rate'           => $request->pulse_rate ?: null,
                'respiratory_rate'     => $request->respiratory_rate ?: null,
                'height'               => $request->height ?: null,
                'weight'               => $request->weight ?: null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Consultation record has been updated successfully.',
                'data' => $record
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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

    public function patientCase($id)
    {
        $healthWorkerName = '';
        if (Auth::user()->role == 'staff') {
            $staffInfo = staff::where("user_id", Auth::user()->id)->first();
            $healthWorkerName = $staffInfo->full_name;
        }
        $medical_record_case = medical_record_cases::with(['patient', 'vaccination_medical_record'])->findOrFail($id);
        $vaccination_case_record = gc_case_records::where('medical_record_case_id', $medical_record_case->id)->where('status', 'Active')->get();
        // dd($vaccination_case_record);


        // $vaccine_administered = vaccineAdministered::where('vaccination_case_record_id', $vaccination_case_record[0]->id)->get();
        // dd($medical_record_case, $vaccination_case_record, $vaccine_administered);
        return view('records.generalConsultation.patientCase', [
            'isActive' => true,
            'page' => 'RECORD',
            'gc_case_record' => $vaccination_case_record,
            'medical_record_case' => $medical_record_case,
            'healthWorkerName' => $healthWorkerName,
            'medical_record_id' => $id
        ]);
    }

    public function archiveIndex()
    {
        return view('records.archivedRecord.gc-case-archive-record', [
            'isActive' => true,
            'page'     => 'RECORD',
        ]);
    }
    public function caseIndex(string $id)
    {
        $medical_record_case = medical_record_cases::with(['patient', 'gc_medical_record'])
            ->where('id', $id)
            ->where('type_of_case', 'general-consultation')
            ->where('status', 'Active')
            ->findOrFail($id);

        $healthWorkerName = '';
        if (Auth::user()->role == 'staff') {
            $staffInfo = \App\Models\staff::where('user_id', Auth::user()->id)->first();
            $healthWorkerName = $staffInfo?->full_name ?? '';
        }

        return view('records.generalConsultation.patientCase', [
            'isActive'            => true,
            'page'                => 'RECORD',
            'medical_record_case' => $medical_record_case,
            'healthWorkerName'    => $healthWorkerName,
        ]);
    }

    // In GeneralConsultation controller

    public function getCaseRecord(string $id)
    {
        $gcCase = gc_case_records::findOrFail($id);
        $patientName = $gcCase -> medical_record_case -> patient -> full_name;
        return response()->json(['gcCase' => $gcCase,'patient_name'=> $patientName]);
    }

    public function addCaseRecord(Request $request, string $id)
    {
        try {
            $medicalCase = medical_record_cases::findOrFail($id);
            $data = $request->validate([
                'add_handled_by'           => 'required|exists:users,id',
                'add_date_of_consultation' => 'required|date|before_or_equal:today', // ← updated
                'add_symptoms'             => 'required|string',
                'add_diagnosis'            => 'required|string',
                'add_treatment_plan'       => 'required|string',
                'add_blood_pressure'       => ['sometimes', 'nullable', 'regex:/^(7\d|[8-9]\d|1\d{2}|2[0-4]\d|250)\/(4\d|[5-9]\d|1[0-4]\d|150)$/'],
                'add_temperature'          => 'nullable|numeric|between:30,45',
                'add_pulse_rate'           => 'nullable|string|max:20',
                'add_respiratory_rate'     => 'nullable|integer|min:5|max:60',
                'add_height'               => 'nullable|numeric|between:1,250',
                'add_weight'               => 'nullable|numeric|between:1,250',
            ], [
                'add_handled_by.required'                => 'Please select the health worker who handled this record.',
                'add_handled_by.exists'                  => 'The selected health worker does not exist.',
                'add_date_of_consultation.required'      => 'The date of consultation is required.',
                'add_date_of_consultation.date'          => 'Please enter a valid date of consultation.',
                'add_date_of_consultation.before_or_equal' => 'The date of consultation cannot be a future date.',
                'add_symptoms.required'                  => 'The symptoms / chief complaint field is required.',
                'add_diagnosis.required'                 => 'The diagnosis / assessment field is required.',
                'add_treatment_plan.required'            => 'The treatment / plan field is required.',
                'add_blood_pressure.regex'               => 'Please enter a valid blood pressure format (e.g., 120/80).',
                'add_temperature.numeric'                => 'The temperature must be a valid number.',
                'add_temperature.between'                => 'The temperature must be between 30°C and 45°C.',
                'add_pulse_rate.max'                     => 'The pulse rate may not exceed :max characters.',
                'add_respiratory_rate.min'               => 'The respiratory rate must be at least :min breaths per minute.',
                'add_respiratory_rate.max'               => 'The respiratory rate may not exceed :max breaths per minute.',
                'add_height.numeric'                     => 'The height must be a valid number.',
                'add_height.between'                     => 'The height must be between 1 and 250 cm.',
                'add_weight.numeric'                     => 'The weight must be a valid number.',
                'add_weight.between'                     => 'The weight must be between 1 and 250 kg.',
            ]);

            // duplicate check
            $duplicate = gc_case_records::where('medical_record_case_id', $medicalCase->id)
                ->where('date_of_consultation', $data['add_date_of_consultation'])
                ->where('status', '!=', 'Archived')
                ->first();

            if ($duplicate) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => ['add_date_of_consultation' => ['A consultation record for this date already exists.']]
                ], 422);
            }

            gc_case_records::create([
                'medical_record_case_id' => $medicalCase->id,
                'health_worker_id'       => $data['add_handled_by'],
                'patient_name'           => $medicalCase->patient->full_name,
                'date_of_consultation'   => $data['add_date_of_consultation'],
                'symptoms'               => $data['add_symptoms'],
                'diagnosis'              => $data['add_diagnosis'],
                'treatment_plan'         => $data['add_treatment_plan'],
                'blood_pressure'         => $data['add_blood_pressure'] ?? null,
                'temperature'            => $data['add_temperature'] ?? null,
                'pulse_rate'             => $data['add_pulse_rate'] ?? null,
                'respiratory_rate'       => $data['add_respiratory_rate'] ?? null,
                'height'                 => $data['add_height'] ?? null,
                'weight'                 => $data['add_weight'] ?? null,
                'status'                 => 'Active',
                'type_of_record'         => 'Case Record',
            ]);

            return response()->json(['message' => 'Consultation record added successfully.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('GC Add Case Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors'  => ['server' => ['Please try again or contact support.']]
            ], 500);
        }
    }
}
