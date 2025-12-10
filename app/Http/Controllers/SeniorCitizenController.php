<?php

namespace App\Http\Controllers;

use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\senior_citizen_case_records;
use App\Models\senior_citizen_maintenance_meds;
use App\Models\senior_citizen_medical_records;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SeniorCitizenController extends Controller
{
    //

    public function addPatient(Request $request){

        try{
            $patientData = $request->validate([
                'type_of_patient' => 'required',
                'first_name' => ['required', 'string', Rule::unique('patients')->where(function ($query) use ($request) {
                    return $query->where('first_name', $request->first_name)
                        ->where('last_name', $request->last_name);
                })],
                'last_name' => 'sometimes|nullable|string',
                'middle_initial' => 'sometimes|nullable|string|max:2',
                'date_of_birth' => 'sometimes|nullable|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric|min:60',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'sometimes|nullable|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'street' => 'required',
                'brgy' => 'required',
                'civil_status' => 'sometimes|nullable|string',
            ]);

            // validate for medical
            $patientMedicalRecord = $request->validate([
                'occupation' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
                'SSS' => 'sometimes|nullable|string',
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
            ]);

            // validate case info
            $patientCase = $request->validate([
                'existing_medical_condition' => 'sometimes|nullable|string',
                'alergies' => 'sometimes|nullable|string',
                'prescribe_by_nurse' => 'sometimes|nullable|string',
                'medication_maintenance_remarks' => 'sometimes|nullable|string'
            ]);


            $seniorCitizenPatient = patients::create([
                'user_id' => null,
                'first_name' => $patientData['first_name'],
                'middle_initial' => $patientData['middle_initial'],
                'last_name' => $patientData['last_name'],
                'full_name' => ($patientData['first_name'] . ' ' . $patientData['middle_initial'] . ' ' . $patientData['last_name']),
                'age' => $patientData['age'] ?? null,
                'sex' => $patientData['sex'] ?? null,
                'civil_status' => $patientData['civil_status'] ?? null,
                'contact_number' => $patientData['contact_number'] ?? null,
                'date_of_birth' => $patientData['date_of_birth'] ?? null,
                'profile_image' => 'images/default_profile.png',
                'nationality' => $patientData['nationality'] ?? null,
                'date_of_registration' => $patientData['date_of_registration'] ?? null,
                'place_of_birth' => $patientData['place_of_birth'] ?? null,
            ]);

            // use the id of the created patient for medical case record
            $seniorCitizenPatientId = $seniorCitizenPatient->id;

            // add the patient address
            // dd($patient->id);
            $blk_n_street = explode(',', $patientData['street']);
            // dd($blk_n_street);
            patient_addresses::create([
                'patient_id' => $seniorCitizenPatientId,
                'house_number' => $blk_n_street[0] ?? $patientData['blk_n_street'],
                'street' => $blk_n_street[1] ?? null,
                'purok' => $patientData['brgy'],
                'postal_code' => '4109',
                'latitude' => null,
                'longitude' => null,
            ]);

            // add record for medical_case table
            $medicalCase = medical_record_cases::create([
                'patient_id' => $seniorCitizenPatientId,
                'type_of_case' => $patientData['type_of_patient'],
            ]);

            $medicalCaseId = $medicalCase->id;

            // create the data of the senior citizen medical record
            senior_citizen_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id' => $patientData['handled_by'],
                'patient_name' => $seniorCitizenPatient->full_name,
                'occupation' => $patientMedicalRecord['occupation'] ?? null,
                'religion' => $patientMedicalRecord['religion'] ?? null,
                'SSS' => $patientMedicalRecord['SSS'] ?? null,
                'blood_pressure' => $patientMedicalRecord['blood_pressure'] ?? null,
                'temperature' => $patientMedicalRecord['temperature'] ?? null,
                'pulse_rate' => $patientMedicalRecord['pulse_rate'] ?? null,
                'respiratory_rate' => $patientMedicalRecord['respiratory_rate'] ?? null,
                'height' => $patientMedicalRecord['height'] ?? null,
                'weight' => $patientMedicalRecord['weight'] ?? null,
                'type_of_record' => 'Medical Record'
            ]);


            // case Record

            $seniorCitizenCase = senior_citizen_case_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id' => $patientData['handled_by'],
                'patient_name' => $seniorCitizenPatient->full_name,
                'existing_medical_condition' => $patientCase['existing_medical_condition'],
                'alergies' => $patientCase['alergies'],
                'prescribe_by_nurse' => $patientCase['prescribe_by_nurse'],
                'remarks' => $patientCase['medication_maintenance_remarks'],
                'type_of_record' => 'Case Record'
            ]);

            $caseId = $seniorCitizenCase->id;

            // insert in the maintenance medication
            $maintenanceMedicationData = $request->validate([
                'medicines' => 'sometimes|nullable|array',
                'dosage_n_frequencies' => 'sometimes|nullable|array',
                'maintenance_quantity' => 'sometimes|nullable|array',
                'start_date' => 'sometimes|nullable|array',
                'end_date' => 'sometimes|nullable|array'
            ]);

            if (!empty($maintenanceMedicationData['medicines'])) {
                // insert each record
                foreach ($maintenanceMedicationData['medicines'] as $index => $value) {
                    senior_citizen_maintenance_meds::create([
                        'senior_citizen_case_id' => $caseId,
                        'maintenance_medication' => $value,
                        'dosage_n_frequency' => $maintenanceMedicationData['dosage_n_frequencies'][$index],
                        'quantity' => $maintenanceMedicationData['maintenance_quantity'][$index],
                        'start_date' => $maintenanceMedicationData['start_date'][$index],
                        'end_date' => $maintenanceMedicationData['end_date'][$index],
                    ]);
                };
            }

            return response()->json(['message' => 'Senior Citizen Patient information is added Successfully'], 200);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
        
        
        
    }

    public function updateDetails(Request $request,$id){
        try{
            $seniorCitizenRecord = medical_record_cases::with(['patient', 'senior_citizen_medical_record', 'senior_citizen_case_record'])->findOrFail($id);
            $seniorCitizenCase = senior_citizen_case_records::where('medical_record_case_id', $id)->get();
            // address
            $address = patient_addresses::where('patient_id', $seniorCitizenRecord->patient->id)->firstorFail();
            $data = $request->validate([
                'first_name' => 'required|nullable|string',
                'last_name' => 'required|nullable|string',
                'middle_initial' => 'sometimes|nullable|string|max:2',
                'date_of_birth' => 'sometimes|nullable|date',
                'place_of_birth' => 'sometimes|nullable|string',
                'age' => 'sometimes|nullable|numeric',
                'sex' => 'sometimes|nullable|string',
                'contact_number' => 'sometimes|nullable|digits_between:7,12',
                'nationality' => 'sometimes|nullable|string',
                'date_of_registration' => 'required|date',
                'handled_by' => 'required',
                'civil_status' => 'sometimes|nullable|string',
                'occupation' => 'sometimes|nullable|string',
                'SSS' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy' => 'required',
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
             
            ]);

            // update the patient data first
            $seniorCitizenRecord->patient->update([
                'first_name' => $data['first_name'] ?? $seniorCitizenRecord->patient->first_name,
                'middle_initial' => $data['middle_initial'] ?? $seniorCitizenRecord->patient->middle_initial,
                'last_name' => $data['last_name'] ?? $seniorCitizenRecord->patient->last_name,
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']) ?? $seniorCitizenRecord->patient->full_name,
                'age' => $data['age'] ?? $seniorCitizenRecord->patient->age,
                'sex' => $data['sex'] ?? $seniorCitizenRecord->patient->sex,
                'civil_status' => $data['civil_status'] ?? $seniorCitizenRecord->patient->civil_status,
                'contact_number' => $data['contact_number'] ?? $seniorCitizenRecord->patient->contact_number,
                'date_of_birth' => $data['date_of_birth'] ?? $seniorCitizenRecord->patient->date_of_birth,
                'nationality' => $data['nationality'] ?? $seniorCitizenRecord->patient->nationality,
                'date_of_registration' => $data['date_of_registration'] ?? $seniorCitizenRecord->patient->date_of_registration,
                'place_of_birth' => $data['place_of_birth'] ?? $seniorCitizenRecord->patient->place_of_birth,
            ]);
            // update the address
            $blk_n_street = explode(',', $data['street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street' => $blk_n_street[1] ?? $address->street,
                'purok' => $data['brgy'] ?? $address->purok
            ]);

            $seniorCitizenRecord->patient->refresh();

            // update medical record
            $seniorCitizenRecord->senior_citizen_medical_record-> update([
                'health_worker_id'=> $data['handled_by']?? $seniorCitizenRecord->senior_citizen_medical_record->health_worker_id,
                'patient_name' => $seniorCitizenRecord->patient->full_name,
                'occupation'=> $data['occupation']?? $seniorCitizenRecord->senior_citizen_medical_record->occupation,
                'religion' => $data['religion']?? $seniorCitizenRecord->senior_citizen_medical_record->religion,
                'SSS'=> $data['SSS']?? $seniorCitizenRecord->senior_citizen_medical_record->SSS,
                'blood_pressure' => $data['blood_pressure']?? $seniorCitizenRecord->senior_citizen_medical_record->blood_pressure,
                'temperature' => $data['temperature']?? $seniorCitizenRecord->senior_citizen_medical_record->temperature,
                'pulse_rate'=> $data['pulse_rate']?? $seniorCitizenRecord->senior_citizen_medical_record->pulse_rate,
                'respiratory_rate'=> $data['respiratory_rate']?? $seniorCitizenRecord->senior_citizen_medical_record-> respiratory_rate,
                'height' => $data['height']?? $seniorCitizenRecord->senior_citizen_medical_record->height,
                'weight' => $data['weight']?? $seniorCitizenRecord->senior_citizen_medical_record-> weight
            ]);

            foreach ($seniorCitizenCase as $record) {
                $record -> update([
                    'patient_name'=> $seniorCitizenRecord->patient->full_name?? $record->patient_name
                ]);
            };

            return response()->json(['message' => 'Updating Patient information Successfully'], 200);

        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // catch other runtime errors (like null property, query failure, etc.)
            return response()->json([
                'message' => $e->getMessage() // e.g. "Attempt to read property 'blood_pressure' on null"
            ], 500);
        };
    }
    public function viewCaseDetails($id){
        try {
            $caseRecord = senior_citizen_case_records::with('senior_citizen_maintenance_med') ->findOrFail($id);
            $patient_name = $caseRecord->patient_name;
            return response()->json([
                'seniorCaseRecord' => $caseRecord,
                'patient_name' => $patient_name
            ],200 );
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Record not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
       
    }

    public function updateCase(Request $request,$id){
        try{
            $seniorCitizenCase = senior_citizen_case_records::findOrFail($id);

            $data = $request->validate([
                'edit_existing_medical_condition' => 'sometimes|nullable|string',
                'edit_alergies' => 'sometimes|nullable|string',
                'edit_prescribe_by_nurse' => 'sometimes|nullable|string',
                'edit_medication_maintenance_remarks' => 'sometimes|nullable|string'
            ]);

            $seniorCitizenCase->update([
                'existing_medical_condition' =>$data['edit_existing_medical_condition'],
                'alergies' =>$data['edit_alergies'],
                'prescribe_by_nurse' =>$data['edit_prescribe_by_nurse'],
                'remarks' =>$data['edit_medication_maintenance_remarks'],
                'status' => 'Done'
            ]);

            // maintenance medicine
            $maintenanceMedicine = senior_citizen_maintenance_meds::where('senior_citizen_case_id',$id)->delete();

            $maintenanceMedicationData = $request->validate([
                'medicines' => 'sometimes|nullable|array',
                'dosage_n_frequencies' => 'sometimes|nullable|array',
                'maintenance_quantity' => 'sometimes|nullable|array',
                'start_date' => 'sometimes|nullable|array',
                'end_date' => 'sometimes|nullable|array'
            ]);

            if (!empty($maintenanceMedicationData['medicines'])) {
                // insert each record
                foreach ($maintenanceMedicationData['medicines'] as $index => $value) {
                    senior_citizen_maintenance_meds::create([
                        'senior_citizen_case_id' => $id,
                        'maintenance_medication' => $value,
                        'dosage_n_frequency' => $maintenanceMedicationData['dosage_n_frequencies'][$index],
                        'quantity' => $maintenanceMedicationData['maintenance_quantity'][$index],
                        'start_date' => $maintenanceMedicationData['start_date'][$index],
                        'end_date' => $maintenanceMedicationData['end_date'][$index],
                    ]);
                };
            }


            return response()-> json([
                'message'=> 'Patient Case Record is successfully updated'
            ]);




        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function addCase(Request $request, $id){
        try{
            $data = $request ->validate([
                'new_patient_name'=> 'required',
                'add_health_worker_id' => 'required',
                'add_existing_medical_condition' => 'sometimes|nullable|string',
                'add_alergies' => 'sometimes|nullable|string',
                'add_prescribe_by_nurse' => 'sometimes|nullable|string',
                'add_medication_maintenance_remarks' => 'sometimes|nullable|string'
            ]);

            // create the record
            $newCaseRecord = senior_citizen_case_records::create([
                'patient_name'=> $data['new_patient_name'],
                'medical_record_case_id' => $id,
                'health_worker_id' => $data['add_health_worker_id'],
                'existing_medical_condition' => $data['add_existing_medical_condition'] ?? '',
                'alergies'=> $data['add_alergies']??'',
                'prescribe_by_nurse' => $data['add_prescribe_by_nurse']??'',
                'remarks' => $data['add_medication_maintenance_remarks']??'',
                'type_of_record'=> 'Case Record'
            ]);

            $maintenanceMedicationData = $request->validate([
                'medicines' => 'sometimes|nullable|array',
                'dosage_n_frequencies' => 'sometimes|nullable|array',
                'maintenance_quantity' => 'sometimes|nullable|array',
                'start_date' => 'sometimes|nullable|array',
                'end_date' => 'sometimes|nullable|array'
            ]);

            if (!empty($maintenanceMedicationData['medicines'])) {
                // insert each record
                foreach ($maintenanceMedicationData['medicines'] as $index => $value) {
                    senior_citizen_maintenance_meds::create([
                        'senior_citizen_case_id' => $newCaseRecord->id,
                        'maintenance_medication' => $value,
                        'dosage_n_frequency' => $maintenanceMedicationData['dosage_n_frequencies'][$index],
                        'quantity' => $maintenanceMedicationData['maintenance_quantity'][$index],
                        'start_date' => $maintenanceMedicationData['start_date'][$index],
                        'end_date' => $maintenanceMedicationData['end_date'][$index],
                    ]);
                };
            }

            return response()->json([
                'message' => 'Patient Case Record is successfully Added'
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function removeCase($id){
        try {
           $seniorCitizenCase = senior_citizen_case_records::findOrFail($id);
           if($seniorCitizenCase){
            $seniorCitizenCase->update([
                'status'=>'Archived'
            ]);
           }
           return response()->json([
            'mesage'=> 'Senior Citizen Case is successfully deleted'
           ],200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ],422);
        }
    }
}
