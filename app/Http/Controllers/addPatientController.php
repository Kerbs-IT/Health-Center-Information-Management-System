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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str as SupportStr;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Psy\Util\Str;

class addPatientController extends Controller
{
    public function dashboard(){
        $healthworkers = staff::orderBy('first_name','ASC')->get();
        $vaccines = vaccines::get();
        $staffFullName = '';
        if(Auth::user() -> role == 'staff'){
            $staff = staff::where("user_id", Auth::user()->id)->first();
            $staffFullName = $staff->full_name;
        }
        return view('add_patient.add_patient', ['isActive' => true, 
        'page' => 'ADD PATIENT', 
        'healthworkers' => $healthworkers, 
        'vaccines'=> $vaccines,
        'healthWorkerFullName'=> $staffFullName]);
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

    public function addVaccinationPatient(Request $request){
        try{
            // validates the data
            $data = $request->validate([
                'type_of_patient' => 'required',
                'first_name' => ['required', 'string', Rule::unique('patients')->where(function ($query) use ($request) {
                    return $query->where('first_name', $request->first_name)
                        ->where('last_name', $request->last_name);
                })],
                'last_name' => 'required|string',
                'middle_initial' => 'sometimes|nullable|string',
                'date_of_birth' => 'required|date|before_or_equal:today',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'required|numeric',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'required|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'mother_name' => 'sometimes|nullable|string',
                'father_name' => 'sometimes|nullable|string',
                'civil_status' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy' => 'required',
                'vaccination_height' => ['required', 'numeric', 'min:1', 'max:250', 'regex:/^\d+(\.\d{1,2})?$/'],
                'vaccination_weight' => ['required', 'numeric', 'min:1', 'max:250', 'regex:/^\d+(\.\d{1,2})?$/'],
                'date_of_vaccination' => 'required|date',
                'time_of_vaccination' => 'sometimes|nullable|date_format:H:i',
                'selected_vaccines' => 'required|string',
                'dose_number' => 'required|numeric',
                'remarks' => 'sometimes|nullable|string',
                'current_height' => [
                    'nullable',
                    'numeric',
                    'between:1,250'
                ],
                'current_weight' => [
                    'nullable',
                    'numeric',
                    'between:1,300'
                ],
                'current_temperature' => [
                    'nullable',
                    'numeric',
                    'between:35,42'
                ],
                'date_of_comeback' => 'required|date',
                'suffix' => 'sometimes|nullable|string',
                'user_account' => 'sometimes|nullable|numeric',
                'email' => 'required|email'
            ], [
                // Custom messages with friendly attribute names
                'vaccination_height.required' => 'The height field is required.',
                'vaccination_height.numeric' => 'The height must be a number.',
                'vaccination_height.min' => 'The height must be at least :min cm.',
                'vaccination_height.max' => 'The height may not be greater than :max cm.',
                'vaccination_height.regex' => 'The height format is invalid.',

                'vaccination_weight.required' => 'The weight field is required.',
                'vaccination_weight.numeric' => 'The weight must be a number.',
                'vaccination_weight.min' => 'The weight must be at least :min kg.',
                'vaccination_weight.max' => 'The weight may not be greater than :max kg.',
                'vaccination_weight.regex' => 'The weight format is invalid.',

                'current_height.numeric' => 'The current height must be a number.',
                'current_height.between' => 'The current height must be between :min and :max cm.',

                'current_weight.numeric' => 'The current weight must be a number.',
                'current_weight.between' => 'The current weight must be between :min and :max kg.',

                'current_temperature.numeric' => 'The current temperature must be a number.',
                'current_temperature.between' => 'The current temperature must be between :min and :max °C.',
            ]);
            

            // create the patient information record

            $middle = substr($data['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $middleInitial = $data['middle_initial']? ucwords($data['middle_initial']):'';
            $parts = [
                strtolower($data['first_name']),
                $middle,
                strtolower($data['last_name']),
                $data['suffix']??null,
            ];
            // blk & street
            $blk_n_street = explode(',', $data['street']);

            // Check if the user account matches the credentials
            if ($data['user_account']) {
                $errors = [];

                try {
                    $user = User::with('user_address')->findOrFail((int)$data['user_account']);

                    // Validate email
                    if ($user->email != $data['email']) {
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
                    if ($data['brgy'] != $user->user_address->purok) {
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


            $fullName = ucwords(trim(implode(' ', array_filter($parts))));
            $ageInYears = Carbon::parse($data['date_of_birth'])->age;
            $vaccinationPatient = patients::create([
                'user_id' => null,
                'first_name' => ucwords(strtolower($data['first_name'])),
                'middle_initial' => $middleInitial,
                'last_name' => ucwords(strtolower($data['last_name'])),
                'full_name' => $fullName,
                'age' => $ageInYears?? 0,
                'age_in_months' => $this->calculateAgeInMonths($data['date_of_birth']),
                'sex' => isset($data['sex']) ? ucfirst($data['sex']) : null,
                'civil_status'=> $data['civil_status'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'date_of_birth'=> $data['date_of_birth']?? null,
                'profile_image' => 'images/default_profile.png',
                'nationality' => $data['nationality'] ?? null,
                'date_of_registration' => $data['date_of_registration']??null,
                'place_of_birth' => $data['place_of_birth']??null,
                'suffix' => $data['suffix']??null
            ]);

            
            // Insert user data or update only
            if ($data['user_account']) {
                try {
                    $user = User::with('user_address')->findOrFail((int)$data['user_account']);

                    // Update existing user
                    $user->update([
                        'patient_record_id' => $vaccinationPatient->id,
                        'first_name' => ucwords(strtolower($data['first_name'])),
                        'middle_initial' => $middleInitial,
                        'last_name' => ucwords(strtolower($data['last_name'])),
                        'full_name' => $fullName,
                        'email' => $data['email'],
                        'contact_number' => $data['contact_number'] ?? null,
                        'date_of_birth' => $data['date_of_birth'] ?? null,
                        'suffix' => $data['suffix'] ?? null,
                        'patient_type' => $data['type_of_patient'],
                        'role' => 'patient',
                        'status' => 'active'
                    ]);

                    // update the patient record
                    $vaccinationPatient->update([
                        'user_id' => $user->id
                    ]);

                    // Update or create user address
                    if ($user->user_address) {
                        $user->user_address->update([
                            'patient_id' => $vaccinationPatient->id,
                            'house_number' => $blk_n_street[0],
                            'street' => $blk_n_street[1] ?? null,
                            'purok' => $data['brgy']
                        ]);
                    } else {
                        // Create address if it doesn't exist
                        $user->user_address()->create([
                            'patient_id' => $vaccinationPatient->id,
                            'house_number' => $blk_n_street[0],
                            'street' => $blk_n_street[1] ?? null,
                            'purok' => $data['brgy']
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
                $temporaryPassword = $this -> generateSecurePassword(8);
                try {
                    // Create user
                    $user = User::create([
                        'patient_record_id' => $vaccinationPatient->id,
                        'first_name' => ucwords(strtolower($data['first_name'])),
                        'middle_initial' => $middleInitial,
                        'last_name' => ucwords(strtolower($data['last_name'])),
                        'full_name' => $fullName,
                        'email' => $data['email'],
                        'contact_number' => $data['contact_number'] ?? null,
                        'date_of_birth' => $data['date_of_birth'] ?? null,
                        'suffix' => $data['suffix'] ?? null,
                        'patient_type' => $data['type_of_patient'],
                        'password' => Hash::make($temporaryPassword),
                        'role' => 'patient',
                        'status' => 'active',
                    ]);

                    $vaccinationPatient->update([
                        'user_id' => $user->id
                    ]);

                    // Send email with credentials
                    Mail::to($user->email)->send(new PatientAccountCreated($user, $temporaryPassword));
                    // Create user address
                    $user->user_address()->create([
                        'patient_id' => $vaccinationPatient->id,
                        'house_number' => $blk_n_street[0],
                        'street' => $blk_n_street[1] ?? null,
                        'purok' => $data['brgy']
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

            // use the id of the created patient for medical case record
            $vaccinationPatientId = $vaccinationPatient->id;

            // add the patient address
            // dd($patient->id);
            
            // dd($blk_n_street);
            $patientAddress = patient_addresses::create([
                'patient_id' => $vaccinationPatientId,
                'house_number' => $blk_n_street[0] ?? $data['blk_n_street'],
                'street' => $blk_n_street[1] ?? null,
                'purok' => $data['brgy'],
                'postal_code' => '4109',
                'latitude' => null,
                'longitude' => null,
            ]);

            // add record for medical_case table
            $medicalCase = medical_record_cases::create([
                'patient_id'=> $vaccinationPatientId,
                'type_of_case' => $data['type_of_patient'],

            ]);

            // add record for vaccination medical record
            $medicalCaseId = $medicalCase -> id;

            $vaccinationMedicalRecord = vaccination_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'date_of_registration' => $data['date_of_registration']??null,
                'mother_name' => ucwords($data['mother_name'])?? null,
                'father_name' => ucwords($data['father_name'])?? null,
                'birth_height' => $data['vaccination_height']?? null,
                'birth_weight' => $data['vaccination_weight']?? null,
                'type_of_record' => 'Medical Record',
                'health_worker_id' => $data['handled_by']
            ]);

            // get the vaccine types
            $vaccines = explode(',',$data['selected_vaccines']);
            $selectedVaccinesArray = [];

            foreach ($vaccines as $key => $vaccineId) {
                $vaccineText = vaccines::find($vaccineId);

                $selectedVaccinesArray[] = $vaccineText-> vaccine_acronym;
            }

            $selectedVaccines = implode(', ', $selectedVaccinesArray);

            // create a case record
            $medicalCaseRecord = vaccination_case_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'patient_name' => $fullName,
                'date_of_vaccination' => $data['date_of_vaccination']??null,
                'time' => $data['time_of_vaccination']??null,
                'vaccine_type' => $selectedVaccines,
                'dose_number' => $data['dose_number']??null,
                'remarks' => $data['remarks']??null,
                'type_of_record' => 'Vaccination Record',
                'health_worker_id' => $data['handled_by'],
                'height' => $data['current_height']??null,
                'weight' => $data['current_weight'] ?? null,
                'temperature' => $data['current_temperature']?? null,
                'date_of_comeback' => $data['date_of_comeback'],
                'vaccination_status' => 'completed'
            ]);

            // id of medical case record
            $medicalCaseRecordId = $medicalCaseRecord->id;

            foreach($vaccines as $vaccineId){
                $vaccine = vaccines::find($vaccineId);

                $vaccineAdministered = vaccineAdministered::create([
                    'vaccination_case_record_id' => $medicalCaseRecordId,
                    'vaccine_type' => $vaccine -> type_of_vaccine,
                    'dose_number' => $data['dose_number']??null,
                    'vaccine_id' => $vaccineId?? null
                ]);
            }

            // vaccination masterlist

            $vaccinationRecord = medical_record_cases::with(['patient', 'vaccination_medical_record'])->where('type_of_case','vaccination')->where('id', $medicalCaseId)->first();

            $fullAddress = "$patientAddress->house_number $patientAddress->street $patientAddress->purok $patientAddress->barangay $patientAddress->city $patientAddress->province";
            // create the record
            // nurse
            $ageInMonths = null;
            if ($vaccinationPatient->age == 0 && $vaccinationPatient->date_of_birth) {
                $ageInMonths = $this->calculateAgeInMonths($vaccinationPatient->date_of_birth);
            }
            $nurse = User::where("role",'nurse')->first();
            $nurseInfo = nurses::where("user_id",$nurse->id)->first();
            $nurseFullname = ucwords($nurseInfo->full_name);
            $vaccinationMasterlist = vaccination_masterlists::create([
                'brgy_name' => $patientAddress-> purok,
                'midwife'=> "Nurse ". $nurseFullname??null,
                'health_worker_id' => $data['handled_by'],
                'medical_record_case_id'=> $medicalCaseId,
                'name_of_child' => $vaccinationPatient->full_name,
                'patient_id'=> $vaccinationPatient->id,
                'address_id'=> $patientAddress->id,
                'Address' => trim($fullAddress," "),
                'sex'=> $vaccinationPatient->sex,
                'age'=> $vaccinationPatient->age,
                'age_in_months' => $ageInMonths,
                'date_of_birth' => $vaccinationPatient->date_of_birth
            ]);

            

            //  loop through
            foreach($vaccines as $vaccineId){
                $vaccine = vaccines::find($vaccineId);
                $vaccineText = $vaccine->vaccine_acronym == 'Hepatitis B'? $vaccine->vaccine_acronym : SupportStr::upper($vaccine->vaccine_acronym);
                $itemColumn = $vaccineText == 'Hepatitis B'? $vaccineText: $vaccineText . "_" . $medicalCaseRecord->dose_number;

                $vaccineTypes = ['BCG','Hepatitis B','PENTA_1','PENTA_2','PENTA_3','OPV_1','OPV_2','OPV_3','PCV_1','PCV_2','PCV_3','IPV_1','IPV_2','MCV_1','MCV_2'];
                if(in_array($itemColumn,$vaccineTypes)){
                    $vaccinationMasterlist->update([
                        "$itemColumn" => $medicalCaseRecord->date_of_vaccination
                    ]);
                }
            }



            return response()->json(['message' => 'Patient has been added'], 200);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }catch(\Exception $e){
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
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
