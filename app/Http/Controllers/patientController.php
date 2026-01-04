<?php

namespace App\Http\Controllers;

use App\Models\addresses;
use App\Models\family_planning_case_records;
use App\Models\family_planning_medical_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\pregnancy_checkups;
use App\Models\pregnancy_plans;
use App\Models\prenatal_case_records;
use App\Models\prenatal_medical_records;
use App\Models\senior_citizen_case_records;
use App\Models\senior_citizen_medical_records;
use App\Models\tb_dots_case_records;
use App\Models\tb_dots_check_ups;
use App\Models\tb_dots_medical_records;
use App\Models\User;
use App\Models\users_address;
use App\Models\vaccination_case_records;
use App\Models\vaccination_medical_records;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class patientController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::user()->id;
        $address = users_address::where('user_id', (int) $userId)->first();
        $patientType = null;
        $medicalRecordInfo = null;

        $patient = patients::where('user_id', $userId)
            ->where('status', '!=', 'Archived')
            ->first();

        if ($patient) {
            // Get all medical record case types for this patient
            $medicalRecordCases = medical_record_cases::where('patient_id', $patient->id)
                ->where('status', '!=', 'Archived')
                ->get();

            $caseTypes = $medicalRecordCases->pluck('type_of_case')
                ->unique()
                ->values()
                ->toArray();

            // change the address i the patient is present
            $address = patient_addresses::where('patient_id',$patient->id)->first();

            // Determine patient type based on priority
            if (in_array('vaccination', $caseTypes)) {
                $patientType = 'vaccination';
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'vaccination')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = vaccination_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
            } elseif (in_array('prenatal', $caseTypes)) {
                $patientType = 'prenatal';
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'prenatal')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = prenatal_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
            } elseif (in_array('senior-citizen', $caseTypes)) {
                $patientType = 'senior-citizen';
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'senior-citizen')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = senior_citizen_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
            } elseif (in_array('tb-dots', $caseTypes)) {
                $patientType = 'tb-dots';
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'tb-dots')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = tb_dots_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
            } elseif (count($caseTypes) === 1 && in_array('family-planning', $caseTypes)) {
                // Only family planning, no other types
                $patientType = 'family-planning';

                // Get the medical record case for family planning
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'family-planning')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = family_planning_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
            } else {
                // No recognized patient type or multiple types without clear priority
                $patientType = null;
                $medicalRecordInfo = null;
            }
        }

        // Build full address
        $fullAddress = collect([
            $address?->house_number,
            $address?->street,
            $address?->purok,
            $address?->barangay,
            $address?->city,
            $address?->province,
        ])
            ->filter()
            ->implode(', ');

        return view('dashboard.patient', [
            'isActive' => true,
            'page' => 'DASHBOARD',
            'fullAddress' => $fullAddress,
            'typeOfPatient' => $patientType,
            'medicalRecord' => $medicalRecordInfo
        ]);
    }

    public function info($id)
    {
        $user = User::findOrFail((int)$id);
        $patient = patients::where('user_id', (int)$id)->first();

        if ($patient) {
            // Get all medical record case types for this patient
            $medicalRecordCases = medical_record_cases::where('patient_id', $patient->id)
                ->where('status', '!=', 'Archived')
                ->get();

            $caseTypes = $medicalRecordCases->pluck('type_of_case')
                ->unique()
                ->values()
                ->toArray();

            // Get the address for the patient
            $address = patient_addresses::where('patient_id', $patient->id)->first();

            // Determine patient type based on priority
            if (in_array('vaccination', $caseTypes)) {
                $patientType = 'vaccination';
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'vaccination')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = vaccination_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
                $combined = array_merge([
                    'user' => $user->toArray(),
                    'patient' => $patient->toArray(),
                    'patient_address' => $address ? $address->toArray() : null,
                    'medicalRecord' => $medicalRecordInfo,
                    'typeOfPatient' => 'vaccination'
                ]);
            } elseif (in_array('prenatal', $caseTypes)) {
                $patientType = 'prenatal';
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'prenatal')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = prenatal_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
                $combined = array_merge([
                    'user' => $user->toArray(),
                    'patient' => $patient->toArray(),
                    'patient_address' => $address ? $address->toArray() : null,
                    'medicalRecord' => $medicalRecordInfo,
                    'typeOfPatient' => 'prenatal'
                ]);
            } elseif (in_array('senior-citizen', $caseTypes)) {
                $patientType = 'senior-citizen';
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'senior-citizen')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = senior_citizen_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
                $combined = array_merge([
                    'user' => $user->toArray(),
                    'patient' => $patient->toArray(),
                    'patient_address' => $address ? $address->toArray() : null,
                    'medicalRecord' => $medicalRecordInfo,
                    'typeOfPatient' => 'senior-citizen'
                ]);
            } elseif (in_array('tb-dots', $caseTypes)) {
                $patientType = 'tb-dots';
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'tb-dots')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = tb_dots_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
                $combined = array_merge([
                    'user' => $user->toArray(),
                    'patient' => $patient->toArray(),
                    'patient_address' => $address ? $address->toArray() : null,
                    'medicalRecord' => $medicalRecordInfo,
                    'typeOfPatient' => 'tb-dots'
                ]);
            } elseif (count($caseTypes) === 1 && in_array('family-planning', $caseTypes)) {
                // Only family planning, no other types
                $patientType = 'family-planning';

                // Get the medical record case for family planning
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'family-planning')->first();

                if ($medicalRecordCase) {
                    $medicalRecordInfo = family_planning_medical_records::where('medical_record_case_id', $medicalRecordCase->id)
                        ->first();
                }
                $combined = array_merge([
                    'user' => $user->toArray(),
                    'patient' => $patient->toArray(),
                    'patient_address' => $address ? $address->toArray() : null,
                    'medicalRecord' => $medicalRecordInfo,
                    'typeOfPatient' => 'family-planning'
                ]);
            } else {
                // No recognized patient type or multiple types without clear priority
                $combined = array_merge([
                    'user' => $user->toArray(),
                    'patient' => $patient->toArray(),
                    'patient_address' => $address ? $address->toArray() : null,
                    'medicalRecord' => null,
                    'typeOfPatient' => null
                ]);
            }
        } else {
            // User has no patient record
            $address = users_address::where('user_id', $user->id)->first();
            $combined = array_merge([
                'user' => $user->toArray(),
                'patient' => null,  // Changed from $patient->toArray() to null
                'patient_address' => $address ? $address->toArray() : null,
                'medicalRecord' => null,
                'typeOfPatient' => null
            ]);
        }

        return response()->json(['response' => $combined]);
    }


    public function updateInfo(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $hasPatient = $user->patient()->exists();

            $data = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'middle_initial' => 'sometimes|nullable|string',
                'age' => $hasPatient ? 'required|numeric' : 'sometimes|nullable|numeric',
                'date_of_birth' => 'sometimes|nullable|date',
                'sex' => 'sometimes|nullable|string',
                'civil_status' => 'sometimes|nullable|string',
                'contact_number' => 'sometimes|nullable|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'username' => 'required',
                'email' => ['required', 'email'],
                'blk_n_street' => 'required',
                'patient_purok_dropdown' => 'required',
                'password' => ['sometimes', 'nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'profile_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $additionalData = $request->validate([
                'mother_name' => 'sometimes|nullable|string',
                'father_name' => 'sometimes|nullable|string',
                'vaccination_height' => ['nullable', 'numeric', 'min:30', 'max:250'],
                'vaccination_weight' => ['nullable', 'numeric', 'min:1', 'max:300'],
                'family_head_name' => 'sometimes|nullable|string',
                'blood_type' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
                'philhealth_number_radio' => 'sometimes|nullable|string',
                'philHealth_number' => 'sometimes|nullable|string',
                'occupation' => 'sometimes|nullable|string',
                'SSS' => 'sometimes|nullable|string',
                'philhealth_no' => 'sometimes|nullable|string',
                'philhealth_id' => 'sometimes|nullable|numeric'
            ]);

            // Update address
            $address = users_address::where('user_id', $user->id)->first();
            $blk_n_street = explode(',', $data['blk_n_street'], 2);
            $house_number = trim($blk_n_street[0] ?? '');
            $street = trim($blk_n_street[1] ?? null);

            $address->update([
                'house_number' => $house_number,
                'street' => $street,
                'purok' => $data['patient_purok_dropdown']
            ]);

            $address->refresh();
            $fullAddress = collect([
                $address->house_number,
                $address->street,
                $address->purok,
                $address->barangay ?? null,
                $address->city ?? null,
                $address->province ?? null,
            ])->filter()->join(', ');

            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                // Delete old profile image from all possible locations
                $this->deleteOldProfileImage($user);

                $file = $request->file('profile_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('images/profile_images');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $filename);
                $profileImagePath = 'images/profile_images/' . $filename;

                // Update profile image across all related tables
                $this->updateProfileImageAcrossAllTables($user, $profileImagePath);
            }

            // Prepare name components
            $middle = substr($data['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $parts = [
                strtolower($data['first_name']),
                $middle,
                strtolower($data['last_name'])
            ];
            $fullName = ucwords(trim(implode(' ', array_filter($parts))));

            $patient = $user->patient ?? null;

            if ($patient) {
                // Patient exists - only update patient and related records

                // Update patient information
                $patient->update([
                    'first_name' => ucwords($data['first_name']) ?? null,
                    'middle_initial' => ucwords($data['middle_initial']) ?? null,
                    'last_name' => ucwords($data['last_name']) ?? null,
                    'full_name' => $fullName,
                    'age' => $data['age'],
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'sex' => $data['sex'] ?? null,
                    'civil_status' => $data['civil_status'] ?? null,
                    'contact_number' => $data['contact_number'] ?? null,
                    'nationality' => $data['nationality'] ?? null,
                ]);

                $patient->address->update([
                    'house_number' => $house_number,
                    'street' => $street,
                    'purok' => $data['patient_purok_dropdown']
                ]);

                // Update medical record cases
                $medicalRecordCases = medical_record_cases::where('patient_id', $patient->id)
                    ->where('status', '!=', 'Archived')
                    ->get();

                if ($medicalRecordCases->isEmpty()) {
                    return response()->json(['error' => "Record doesn't have existing type of patient"]);
                }

                $this->updateMedicalRecordByType($request, $medicalRecordCases, $additionalData);
            } else {
                // No patient - update user table

                $userUpdateData = [
                    'username' => $data['username'] ?? $user->username,
                    'email' => $data['email'] ?? $user->email,
                    'first_name' => $data['first_name'] ?? $user->first_name,
                    'last_name' => $data['last_name'] ?? $user->last_name,
                    'middle_initial' => $data['middle_initial'] ?? $user->middle_initial,
                    'date_of_birth' => $data['date_of_birth'] ?? $user->date_of_birth,
                    'contact_number' => $data['contact_number'],
                    'address' => $fullAddress,
                ];

                // Add password to update if provided
                if (!empty($data['password'])) {
                    $userUpdateData['password'] = Hash::make($data['password']);
                }

                $user->update($userUpdateData);
            }

            return response()->json(['success' => 'Information has been updated successfully']);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Delete old profile image from all possible locations
     */
    private function deleteOldProfileImage($user)
    {
        $imagesToDelete = [];

        // Check user table
        if (!empty($user->profile_image) && $user->profile_image !== 'images/default_profile.png') {
            $imagesToDelete[] = $user->profile_image;
        }

        // Check patient table
        if ($user->patient && !empty($user->patient->profile_image) && $user->patient->profile_image !== 'images/default_profile.png') {
            $imagesToDelete[] = $user->patient->profile_image;
        }

        // Check staff table
        if ($user->staff && !empty($user->staff->profile_image) && $user->staff->profile_image !== 'images/default_profile.png') {
            $imagesToDelete[] = $user->staff->profile_image;
        }

        // Check nurse table
        if ($user->nurses && !empty($user->nurses->profile_image) && $user->nurses->profile_image !== 'images/default_profile.png') {
            $imagesToDelete[] = $user->nurses->profile_image;
        }

        // Delete unique images only
        $imagesToDelete = array_unique($imagesToDelete);
        foreach ($imagesToDelete as $imagePath) {
            $oldImagePath = public_path(ltrim($imagePath, '/'));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    }

    /**
     * Update profile image across all related tables
     */
    private function updateProfileImageAcrossAllTables($user, $profileImagePath)
    {
        // Update user table
        $user->profile_image = $profileImagePath;
        $user->save();

        // Update patient table if exists
        if ($user->patient) {
            $user->patient->profile_image = $profileImagePath;
            $user->patient->save();
        }

        // Update staff table if exists
        if ($user->staff) {
            $user->staff->profile_image = $profileImagePath;
            $user->staff->save();
        }

        // Update nurse table if exists
        if ($user->nurses) {
            $user->nurses->profile_image = $profileImagePath;
            $user->nurses->save();
        }
    }

    private function updateMedicalRecordByType($request, $medicalRecordCases, $additionalData)
    {
        $typeOfPatient = $request->typeOfPatient;

        switch ($typeOfPatient) {
            case 'vaccination':
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'vaccination')->first();
                if ($medicalRecordCase) {
                    $medicalRecordInfo = vaccination_medical_records::where('medical_record_case_id', $medicalRecordCase->id)->first();
                    if ($medicalRecordInfo) {
                        $medicalRecordInfo->update([
                            'mother_name' => $additionalData['mother_name'] ?? $medicalRecordInfo->mother_name,
                            'father_name' => $additionalData['father_name'] ?? $medicalRecordInfo->father_name,
                            'birth_height' => $additionalData['vaccination_height'] ?? $medicalRecordInfo->birth_height,
                            'birth_weight' => $additionalData['vaccination_weight'] ?? $medicalRecordInfo->birth_weight,
                        ]);
                    }
                }
                break;

            case 'prenatal':
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'prenatal')->first();
                if ($medicalRecordCase) {
                    $medicalRecordInfo = prenatal_medical_records::where('medical_record_case_id', $medicalRecordCase->id)->first();
                    if ($medicalRecordInfo) {
                        $medicalRecordInfo->update([
                            'family_head_name' => $additionalData['family_head_name'] ?? $medicalRecordInfo->family_head_name,
                            'blood_type' => $additionalData['blood_type'] ?? $medicalRecordInfo->blood_type,
                            'religion' => $additionalData['religion'] ?? $medicalRecordInfo->religion,
                            'philHealth_number' => ($additionalData['philhealth_number_radio'] ?? null) === 'yes'
                                ? ($additionalData['philHealth_number'] ?? null)
                                : (($additionalData['philhealth_number_radio'] ?? null) === 'no'
                                    ? 'no'
                                    : $medicalRecordInfo->philHealth_number),
                        ]);
                    }
                }
                break;

            case 'tb-dots':
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'tb-dots')->first();
                if ($medicalRecordCase) {
                    $medicalRecordInfo = tb_dots_medical_records::where('medical_record_case_id', $medicalRecordCase->id)->first();
                    if ($medicalRecordInfo) {
                        $medicalRecordInfo->update([
                            'philhealth_id_no' => $additionalData['philhealth_id'] ?? $medicalRecordInfo->philhealth_id_no
                        ]);
                    }
                }
                break;

            case 'senior-citizen':
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'senior-citizen')->first();
                if ($medicalRecordCase) {
                    $medicalRecordInfo = senior_citizen_medical_records::where('medical_record_case_id', $medicalRecordCase->id)->first();
                    if ($medicalRecordInfo) {
                        $medicalRecordInfo->update([
                            'occupation' => $additionalData['occupation'] ?? $medicalRecordInfo->occupation,
                            'religion' => $additionalData['religion'] ?? $medicalRecordInfo->religion,
                            'SSS' => $additionalData['SSS'] ?? $medicalRecordInfo->SSS,
                        ]);
                    }
                }
                break;

            case 'family-planning':
                $medicalRecordCase = $medicalRecordCases->where('type_of_case', 'family-planning')->first();
                if ($medicalRecordCase) {
                    $medicalRecordInfo = family_planning_medical_records::where('medical_record_case_id', $medicalRecordCase->id)->first();
                    if ($medicalRecordInfo) {
                        $medicalRecordInfo->update([
                            'occupation' => $additionalData['occupation'] ?? $medicalRecordInfo->occupation,
                            'religion' => $additionalData['religion'] ?? $medicalRecordInfo->religion,
                            'philhealth_no' => $additionalData['philhealth_no'] ?? $medicalRecordInfo->philhealth_no
                        ]);
                    }
                }
                break;
        }
    }

    public function renderData($userId)
    {
        // sorting variables
        $perPage  = request('per_page', 10);
        $search   = request('search');
        $dateSort = request('date_sort', 'desc');


        $user = User::findOrFail($userId);
        $patient = patients::where('user_id', $userId)
            ->where('status', '!=', 'Archived')
            ->first();

        

        if (!$patient) {
            return view('patient-info.patient-records', [
                'isActive' => true,
                'page' => 'RECORD',
                'typeOfPatient' => null,
            ]);
        }
        // to check if the patient has any medical records
        $patientType = medical_record_cases::where('patient_id', $patient->id)
            ->where('status','!=','Archived')
            ->pluck('type_of_case')
            ->unique()
            ->values()
            ->toArray();

        // VACCINATION
        if (in_array('vaccination', $patientType)) {
            $medicalCase = medical_record_cases::where('patient_id', $patient->id)
                ->where('type_of_case', 'vaccination')
                ->where('status', 'Active')
                ->first();

            if (!$medicalCase) {
                return response()->json(['errors' => 'No record Found']);
            }
            // query to apply the sorting
            $query = vaccination_case_records::where(
                'medical_record_case_id',
                $medicalCase->id
            )
                ->where('status', '!=', 'Archived');

            // Search (by record ID for now)
            if ($search) {
                $query->where('id', 'like', "%{$search}%");
            }

            // Sort by date
            if (in_array($dateSort, ['asc', 'desc'])) {
                $query->orderBy('created_at', $dateSort);
            }

            // Paginate
            $vaccination_case_records = $query
                ->paginate($perPage)
                ->withQueryString();

            
            return view('patient-info.patient-records', [
                'isActive' => true,
                'page' => 'RECORD',
                'typeOfPatient' => 'vaccination',
                'vaccination_case_record' => $vaccination_case_records
            ]);
        }
        if (in_array('prenatal', $patientType)) {
            // Get the medical record cases
            $prenatalMedicalRecordCase = medical_record_cases::where('patient_id', $patient->id)
                ->where('type_of_case', 'prenatal')
                ->where('status', '!=', 'Archived')
                ->with(['patient', 'prenatal_medical_record'])
                ->first();

            if (!$prenatalMedicalRecordCase) {
                return view('patient-info.patient-records', [
                    'isActive' => true,
                    'page' => 'RECORD',
                    'typeOfPatient' => null
                ]);
            }

            $familyPlanningMedicalRecordCase = medical_record_cases::where('patient_id', $patient->id)
                ->where('type_of_case', 'family-planning')
                ->where('status', '!=', 'Archived')
                ->first();

            // Fetch all records individually (needed for modals/PDFs)
            $prenatal_case_record = prenatal_case_records::with('pregnancy_timeline_records')
                ->where('medical_record_case_id', $prenatalMedicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->first();

            $pregnancy_plan = pregnancy_plans::where('medical_record_case_id', $prenatalMedicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->first();

            $prenatalCheckupRecords = pregnancy_checkups::where('medical_record_case_id', $prenatalMedicalRecordCase->id)
                ->where('status', '!=', 'Archived')
                ->orderBy('created_at', 'desc')
                ->get();

            $familyPlanCaseInfo = null;
            $familyPlanSideB = collect();

            if ($familyPlanningMedicalRecordCase) {
                $familyPlanCaseInfo = family_planning_case_records::where('medical_record_case_id', $familyPlanningMedicalRecordCase->id)
                    ->where('status', '!=', 'Archived')
                    ->first();

                $familyPlanSideB = family_planning_side_b_records::where('medical_record_case_id', $familyPlanningMedicalRecordCase->id)
                    ->where('status', '!=', 'Archived')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            // ===== MERGE ALL RECORDS INTO UNIFIED COLLECTION =====
            $allRecords = collect();

            // 1. Add Prenatal Case Record (Priority 1)
            if ($prenatal_case_record) {
                $allRecords->push([
                    'id' => $prenatal_case_record->id,
                    'service_type' => 'Prenatal',
                    'type_of_record' => 'Case Record',
                    'date_registered' => $prenatal_case_record->created_at,
                    'record_type' => 'prenatal_case',
                    'sort_priority' => 1,
                    'data' => $prenatal_case_record
                ]);
            }

            // 2. Add Pregnancy Plan (Priority 2)
            if ($pregnancy_plan) {
                $allRecords->push([
                    'id' => $pregnancy_plan->id,
                    'service_type' => 'Prenatal',
                    'type_of_record' => 'Pregnancy Plan',
                    'date_registered' => $pregnancy_plan->created_at,
                    'record_type' => 'pregnancy_plan',
                    'sort_priority' => 2,
                    'data' => $pregnancy_plan
                ]);
            }

            // 3. Add Family Planning Side A (Priority 3)
            if ($familyPlanCaseInfo) {
                $allRecords->push([
                    'id' => $familyPlanCaseInfo->id,
                    'service_type' => 'Family Plan',
                    'type_of_record' => 'Family Plan:Assessment Record - Side A',
                    'date_registered' => $familyPlanCaseInfo->created_at,
                    'record_type' => 'family_plan_side_a',
                    'sort_priority' => 3,
                    'data' => $familyPlanCaseInfo
                ]);
            }

            // 4. Add Family Planning Side B records (Priority 4)
            foreach ($familyPlanSideB as $record) {
                $allRecords->push([
                    'id' => $record->id,
                    'service_type' => 'Family Plan',
                    'type_of_record' => 'Family Plan:Assessment Record - Side B',
                    'date_registered' => $record->created_at,
                    'record_type' => 'family_plan_side_b',
                    'sort_priority' => 4,
                    'data' => $record
                ]);
            }

            // 5. Add Prenatal Checkups (Priority 5)
            foreach ($prenatalCheckupRecords as $record) {
                $allRecords->push([
                    'id' => $record->id,
                    'service_type' => 'Prenatal',
                    'type_of_record' => 'Follow-up Check-up',
                    'date_registered' => $record->created_at,
                    'record_type' => 'prenatal_checkup',
                    'sort_priority' => 5,
                    'data' => $record
                ]);
            }
            $recordTypeFilter = request('record_type_filter');

            // sorting via filter
            if ($recordTypeFilter && $recordTypeFilter !== 'all') {
                $allRecords = $allRecords->filter(function ($record) use ($recordTypeFilter) {
                    return $record['record_type'] === $recordTypeFilter;
                });
            }

            // Filter by search (ID)
            if ($search) {
                $allRecords = $allRecords->filter(function ($record) use ($search) {
                    return stripos((string)$record['id'], $search) !== false;
                });
            }

            // Sort by date
            if ($dateSort === 'asc') {
                $allRecords = $allRecords->sortBy('date_registered');
            } else {
                $allRecords = $allRecords->sortByDesc('date_registered');
            }

            // Maintain priority sorting if no date sort is applied
            if (!request()->has('date_sort') && !$recordTypeFilter) {
                $allRecords = $allRecords->sortBy([
                    ['sort_priority', 'asc'],
                    ['date_registered', 'desc']
                ]);
            }


            // Sort by priority first, then by date (newest first within same priority)
            $allRecords = $allRecords->sortBy([
                ['sort_priority', 'asc'],
                ['date_registered', 'desc']
            ]);

            // ===== PAGINATE THE MERGED COLLECTION =====
            $perPage = 10;
            $currentPage = request()->get('page', 1);
            $paginatedRecords = new \Illuminate\Pagination\LengthAwarePaginator(
                $allRecords->forPage($currentPage, $perPage),
                $allRecords->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('patient-info.patient-records', [
                'isActive' => true,
                'page' => 'RECORD',
                'patientInfo' => $prenatalMedicalRecordCase,
                'allRecords' => $paginatedRecords, // Single paginated collection
                'typeOfPatient' => 'prenatal',
            ]);
        }

        if(in_array('senior-citizen', $patientType)){
            $medicalCase = medical_record_cases::where('patient_id', $patient->id)
                ->where('type_of_case', 'senior-citizen')
                ->where('status', 'Active')
                ->first();

            if (!$medicalCase) {
                return view('patient-info.patient-records', [
                    'isActive' => true,
                    'page' => 'RECORD',
                    'typeOfPatient' => null
                ]);
            }

            $query = senior_citizen_case_records::where('medical_record_case_id', $medicalCase->id)
                ->where('status', '!=', 'Archived');

            // Search (by record ID for now)
            if ($search) {
                $query->where('id', 'like', "%{$search}%");
            }

            // Sort by date
            if (in_array($dateSort, ['asc', 'desc'])) {
                $query->orderBy('created_at', $dateSort);
            }

            // get the senior citizen cases
            $allRecords = $query
                ->paginate($perPage)
                ->withQueryString();
            
            if(!$allRecords){
                return view('patient-info.patient-records', [
                    'isActive' => true,
                    'page' => 'RECORD',
                    'typeOfPatient' => 'senior-citizen',
                    'allRecords' => null
                ]);
            }

            return view('patient-info.patient-records', [
                'isActive' => true,
                'page' => 'RECORD',
                'typeOfPatient' => 'senior-citizen',
                'allRecords' => $allRecords
            ]);
            
        }
        if(in_array('tb-dots',$patientType)){
            $medicalCase = medical_record_cases::where('patient_id', $patient->id)
                ->where('type_of_case', 'tb-dots')
                ->where('status', 'Active')
                ->first();

            if (!$medicalCase) {
                return view('patient-info.patient-records', [
                    'isActive' => true,
                    'page' => 'RECORD',
                    'typeOfPatient' => null
                ]);
            }
            $tbDotsCaseRecord = tb_dots_case_records::where('medical_record_case_id', $medicalCase->id)
            ->where('status','!=','Archived')
            ->first();
            $tbDotsCheckUpRecord = tb_dots_check_ups::where('medical_record_case_id', $medicalCase->id)
                ->where('status', '!=', 'Archived')
                ->orderBy('created_at', 'asc')
                ->get();

            $allRecords = collect();

            if($tbDotsCaseRecord){
                $allRecords->push([
                    'id' => $tbDotsCaseRecord->id,
                    'service_type' => 'Tb-dots',
                    'type_of_record' => 'Case Record',
                    'date_registered' => $tbDotsCaseRecord->created_at,
                    'record_type' => 'tb_dots_case',
                    'sort_priority' => 1,
                    'data' => $tbDotsCaseRecord
                ]);
            }
            if($tbDotsCheckUpRecord){
                foreach($tbDotsCheckUpRecord as $record){
                    $allRecords->push([
                        'id' => $record->id,
                        'service_type' => 'Tb-dots',
                        'type_of_record' => 'Follow-up Check-Up',
                        'date_registered' => $record->created_at,
                        'record_type' => 'tb_dots_checkup',
                        'sort_priority' => 2,
                        'data' => $record
                    ]);
                }
            }
            $recordTypeFilter = request('record_type_filter');

            // sorting via filter
            if ($recordTypeFilter && $recordTypeFilter !== 'all') {
                $allRecords = $allRecords->filter(function ($record) use ($recordTypeFilter) {
                    return $record['record_type'] === $recordTypeFilter;
                });
            }

            // Filter by search (ID)
            if ($search) {
                $allRecords = $allRecords->filter(function ($record) use ($search) {
                    return stripos((string)$record['id'], $search) !== false;
                });
            }

            // Sort by date
            if ($dateSort === 'asc') {
                $allRecords = $allRecords->sortBy('date_registered');
            } else {
                $allRecords = $allRecords->sortByDesc('date_registered');
            }

            // Maintain priority sorting if no date sort is applied
            // Apply sorting based on user selection
            if (request()->has('date_sort')) {
                // User explicitly selected a date sort
                if ($dateSort === 'asc') {
                    $allRecords = $allRecords->sortBy('date_registered')->values();
                } else {
                    $allRecords = $allRecords->sortByDesc('date_registered')->values();
                }
            } else {
                // Default: sort by priority first, then by date
                $allRecords = $allRecords->sortBy([
                    ['sort_priority', 'asc'],
                    ['date_registered', 'asc']
                ])->values();
            }


            
            // ===== PAGINATE THE MERGED COLLECTION =====
            $perPage = 10;
            $currentPage = request()->get('page', 1);
            $paginatedRecords = new \Illuminate\Pagination\LengthAwarePaginator(
                $allRecords->forPage($currentPage, $perPage),
                $allRecords->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            // return the tb-dots record
            return view('patient-info.patient-records', [
                'isActive' => true,
                'page' => 'RECORD',
                'allRecords' => $paginatedRecords, // Single paginated collection
                'typeOfPatient' => 'tb-dots',
            ]);
        }

        if(is_array($patientType) &&
            count($patientType) === 1 &&
            in_array('family-planning', $patientType)){
            $familyPlanningMedicalRecordCase = medical_record_cases::where('patient_id', $patient->id)
                ->where('type_of_case', 'family-planning')
                ->where('status', '!=', 'Archived')
                ->first();


            $familyPlanCaseInfo = null;
            $familyPlanSideB = collect();

            if ($familyPlanningMedicalRecordCase) {
                $familyPlanCaseInfo = family_planning_case_records::where('medical_record_case_id', $familyPlanningMedicalRecordCase->id)
                    ->where('status', '!=', 'Archived')
                    ->first();

                $familyPlanSideB = family_planning_side_b_records::where('medical_record_case_id', $familyPlanningMedicalRecordCase->id)
                    ->where('status', '!=', 'Archived')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            $allRecords = collect();
            if ($familyPlanCaseInfo) {
                $allRecords->push([
                    'id' => $familyPlanCaseInfo->id,
                    'service_type' => 'Family Plan',
                    'type_of_record' => 'Family Plan:Assessment Record - Side A',
                    'date_registered' => $familyPlanCaseInfo->created_at,
                    'record_type' => 'family_plan_side_a',
                    'sort_priority' => 3,
                    'data' => $familyPlanCaseInfo
                ]);
            }

            // 4. Add Family Planning Side B records (Priority 4)
            foreach ($familyPlanSideB as $record) {
                $allRecords->push([
                    'id' => $record->id,
                    'service_type' => 'Family Plan',
                    'type_of_record' => 'Family Plan:Assessment Record - Side B',
                    'date_registered' => $record->created_at,
                    'record_type' => 'family_plan_side_b',
                    'sort_priority' => 4,
                    'data' => $record
                ]);
            }
            $recordTypeFilter = request('record_type_filter');

            // sorting via filter
            if ($recordTypeFilter && $recordTypeFilter !== 'all') {
                $allRecords = $allRecords->filter(function ($record) use ($recordTypeFilter) {
                    return $record['record_type'] === $recordTypeFilter;
                });
            }

            // Filter by search (ID)
            if ($search) {
                $allRecords = $allRecords->filter(function ($record) use ($search) {
                    return stripos((string)$record['id'], $search) !== false;
                });
            }

            // Sort by date
            if ($dateSort === 'asc') {
                $allRecords = $allRecords->sortBy('date_registered');
            } else {
                $allRecords = $allRecords->sortByDesc('date_registered');
            }

            // Maintain priority sorting if no date sort is applied
            // Apply sorting based on user selection
            if (request()->has('date_sort')) {
                // User explicitly selected a date sort
                if ($dateSort === 'asc') {
                    $allRecords = $allRecords->sortBy('date_registered')->values();
                } else {
                    $allRecords = $allRecords->sortByDesc('date_registered')->values();
                }
            } else {
                // Default: sort by priority first, then by date
                $allRecords = $allRecords->sortBy([
                    ['sort_priority', 'asc'],
                    ['date_registered', 'asc']
                ])->values();
            }

            // ===== PAGINATE THE MERGED COLLECTION =====
            $perPage = 10;
            $currentPage = request()->get('page', 1);
            $paginatedRecords = new \Illuminate\Pagination\LengthAwarePaginator(
                $allRecords->forPage($currentPage, $perPage),
                $allRecords->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('patient-info.patient-records', [
                'isActive' => true,
                'page' => 'RECORD',
                'allRecords' => $paginatedRecords, // Single paginated collection
                'typeOfPatient' => 'family-planning',
            ]);

        }
        

        

        // If no patient type matches
        return view('patient-info.patient-records', [
            'isActive' => true,
            'page' => 'RECORD'
        ]);
    }
}
