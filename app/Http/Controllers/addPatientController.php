<?php

namespace App\Http\Controllers;

use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\staff;
use App\Models\vaccination_case_records;
use App\Models\vaccination_medical_records;
use App\Models\vaccineAdministered;
use App\Models\vaccines;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class addPatientController extends Controller
{
    public function dashboard(){
        $healthworkers = staff::orderBy('first_name','ASC')->get();
        $vaccines = vaccines::get();
        return view('add_patient.add_patient', ['isActive' => true, 'page' => 'ADD PATIENT', 'healthworkers' => $healthworkers, 'vaccines'=> $vaccines]);
    }

    public function addVaccinationPatient(Request $request){
        try{
            // validates the data
            $data = $request->validate([
                'type_of_patient' =>'required',
                'first_name' => 'sometimes|nullable|string',
                'last_name' => 'sometimes|nullable|string',
                'middle_initial' => 'sometimes|nullable|string|max:2',
                'date_of_birth' => 'sometimes|nullable|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'sometimes|nullable|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'mother_name' => 'sometimes|nullable|string',
                'father_name' => 'sometimes|nullable|string',
                'civil_status' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy'=> 'required',
                'vaccination_height' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'vaccination_weight' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'date_of_vaccination' => 'required|date',
                'time_of_vaccination' => 'sometimes|nullable|date_format:H:i',
                'selected_vaccines' => 'required|string',
                'dose_number' => 'required|numeric',
                'remarks' => 'sometimes|nullable|string'
            ]);

            // create the patient information record

            $vaccinationPatient = patients::create([
                'user_id' => null,
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'],
                'last_name' => $data['last_name'],
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'age' => $data['age']?? null,
                'sex' => $data['sex']?? null,
                'civil_status'=> $data['civil_status'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'date_of_birth'=> $data['date_of_birth']?? null,
                'profile_image' => 'images/default_profile.png',
                'nationality' => $data['nationality'] ?? null,
                'date_of_registration' => $data['date_of_registration']??null,
                'place_of_birth' => $data['place_of_birth']??null,
            ]);


            // use the id of the created patient for medical case record
            $vaccinationPatientId = $vaccinationPatient->id;

            // add the patient address
            // dd($patient->id);
            $blk_n_street = explode(',', $data['street']);
            // dd($blk_n_street);
            patient_addresses::create([
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
                'mother_name' => $data['mother_name']?? null,
                'father_name' => $data['father_name']?? null,
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
                'patient_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']),
                'date_of_vaccination' => $data['date_of_vaccination']??null,
                'time' => $data['time_of_vaccination']??null,
                'vaccine_type' => $selectedVaccines,
                'dose_number' => $data['dose_number']??null,
                'remarks' => $data['remarks']??null,
                'type_of_record' => 'Vaccination Record',
                'health_worker_id' => $data['handled_by']
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

            return response()->json(['message' => 'Patient has been added'], 201);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }

    }
}
