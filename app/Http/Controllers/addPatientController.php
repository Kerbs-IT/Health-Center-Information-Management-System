<?php

namespace App\Http\Controllers;

use App\Models\medical_record_cases;
use App\Models\nurses;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\staff;
use App\Models\User;
use App\Models\vaccination_case_records;
use App\Models\vaccination_masterlists;
use App\Models\vaccination_medical_records;
use App\Models\vaccineAdministered;
use App\Models\vaccines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function addVaccinationPatient(Request $request){
        try{
            // validates the data
            $data = $request->validate([
                'type_of_patient' =>'required',
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
                'remarks' => 'sometimes|nullable|string',
                'current_height' => 'nullable|numeric',
                'current_weight' => 'nullable|numeric',
                'current_temperature'    => 'nullable|numeric',
                'date_of_comeback' => 'required|date'

            ]);

            // create the patient information record

            $middle = substr($data['middle_initial'] ?? '', 0, 1);
            $middle = $middle ? strtoupper($middle) . '.' : null;
            $parts = [
                strtolower($data['first_name']),
                $middle,
                strtolower($data['last_name'])
            ];

            $fullName = ucwords(trim(implode(' ', array_filter($parts))));

            $vaccinationPatient = patients::create([
                'user_id' => null,
                'first_name' => ucwords(strtolower($data['first_name'])),
                'middle_initial' => ucwords(strtolower($data['middle_initial'])),
                'last_name' => ucwords(strtolower($data['last_name'])),
                'full_name' => $fullName,
                'age' => $data['age']?? 0,
                'sex' => ucfirst($data['sex'])?? 'male',
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
}
