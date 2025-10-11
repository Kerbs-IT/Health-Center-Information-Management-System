<?php

namespace App\Http\Controllers;

use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\senior_citizen_medical_records;
use App\Models\staff;
use App\Models\tb_dots_case_records;
use App\Models\tb_dots_check_ups;
use App\Models\tb_dots_maintenance_medicines;
use App\Models\tb_dots_medical_records;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TbDotsController extends Controller
{
    //

    public function addPatient(Request $request){

        try{
            $patientData = $request->validate([
                'type_of_patient' => 'required',
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
                'street' => 'required',
                'brgy' => 'required',
                'civil_status' => 'sometimes|nullable|string',
            ]);

            // validate for medical
            $patientMedicalRecord = $request->validate([
                'philhealth_id' => 'sometimes|nullable|string',
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
            ]);


            // create the patient information
            $tbDotsPatient = patients::create([
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
            $tbDotsPatientId = $tbDotsPatient->id;

            // add the patient address
            // dd($patient->id);
            $blk_n_street = explode(',', $patientData['street']);
            // dd($blk_n_street);
            patient_addresses::create([
                'patient_id' =>  $tbDotsPatientId,
                'house_number' => $blk_n_street[0] ?? $patientData['blk_n_street'],
                'street' => $blk_n_street[1] ?? null,
                'purok' => $patientData['brgy'],
                'postal_code' => '4109',
                'latitude' => null,
                'longitude' => null,
            ]);

            // add record for medical_case table
            $medicalCase = medical_record_cases::create([
                'patient_id' => $tbDotsPatientId,
                'type_of_case' => $patientData['type_of_patient'],
            ]);


            $medicalCaseId = $medicalCase->id;
            // create the data of the senior citizen medical record
            tb_dots_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id' => $patientData['handled_by'],
                'patient_name' =>  $tbDotsPatient->full_name,
                'philhealth_id_no' => $patientMedicalRecord['philhealth_id']?? null,
                'blood_pressure' => $patientMedicalRecord['blood_pressure'] ?? null,
                'temperature' => $patientMedicalRecord['temperature'] ?? null,
                'pulse_rate' => $patientMedicalRecord['pulse_rate'] ?? null,
                'respiratory_rate' => $patientMedicalRecord['respiratory_rate'] ?? null,
                'height' => $patientMedicalRecord['height'] ?? null,
                'weight' => $patientMedicalRecord['weight'] ?? null,
                'type_of_record' => 'Medical Record'
            ]);

            // case information

            $caseData = $request -> validate([
                'tb_type' => 'required|string',
                'tb_case_type' => 'required|string',
                'tb_date_of_diagnosis' => 'required|date',
                'name_of_physician' => 'sometimes|nullable|string',
                'sputum_result'=> 'sometimes|nullable|string',
                'treatment_medicine_name' => 'sometimes|nullable|string',
                'tb_date_of_medication_administered' => 'required|date',
                'treatment_side_effect' => 'sometimes|nullable|string',
                'tb_outcome'=> 'sometimes|nullable|string',
                'tb_remarks' => 'sometimes|nullable|string',
            ]);

            // create the case info
            $tbDotsCaseRecord = tb_dots_case_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'health_worker_id' => $patientData['handled_by'],
                'patient_name' => $tbDotsPatient -> full_name,
                'type_of_tuberculosis' =>  $caseData['tb_type'],
                'type_of_tb_case' => $caseData['tb_case_type'],
                'date_of_diagnosis' => $caseData['tb_date_of_diagnosis'],
                'name_of_physician' => $caseData['name_of_physician'],
                'sputum_test_results' => $caseData['sputum_result'],
                'treatment_category' => $caseData['treatment_medicine_name'],
                'date_administered' => $caseData['tb_date_of_medication_administered'],
                'side_effect' => $caseData['treatment_side_effect']??null,
                'remarks' => $caseData['tb_remarks']??null,
                'outcome' => $caseData['tb_outcome']??null,
                'type_of_record' => 'Case Record'
            ]);

            // case id
            $caseId = $tbDotsCaseRecord -> id;
            // insert in the maintenance medication
            $maintenanceMedicationData = $request->validate([
                'medicines' => 'sometimes|nullable|array',
                'dosage_n_frequencies' => 'sometimes|nullable|array',
                'medicine_quantity' => 'sometimes|nullable|array',
                'start_date' => 'sometimes|nullable|array',
                'end_date' => 'sometimes|nullable|array'
            ]);

            if (!empty($maintenanceMedicationData['medicines'])) {
                // insert each record
                foreach ($maintenanceMedicationData['medicines'] as $index => $value) {
                    tb_dots_maintenance_medicines::create([
                        'tb_dots_case_id' => $caseId,
                        'medicine_name' => $value,
                        'dosage_n_frequency' => $maintenanceMedicationData['dosage_n_frequencies'][$index],
                        'quantity' => $maintenanceMedicationData['medicine_quantity'][$index],
                        'start_date' => $maintenanceMedicationData['start_date'][$index],
                        'end_date' => $maintenanceMedicationData['end_date'][$index],
                    ]);
                };
            }
            return response()->json(['message' => 'Tb Dots Patient information is added Successfully'], 200);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function updatePatientDetails(Request $request,$id){
        try{
            $tbDotsRecord = medical_record_cases::with(['patient', 'tb_dots_medical_record'])->findOrFail($id);

            $tbDotsCase = tb_dots_case_records::where('medical_record_case_id', $id)->get();
            // address
            $address = patient_addresses::where('patient_id', $tbDotsRecord->patient->id)->firstorFail();
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
                'street' => 'required',
                'brgy' => 'required',
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
                'philheath_id' => 'sometimes|nullable|string'

            ]);

            // update the patient data first
            $tbDotsRecord->patient->update([
                'first_name' => $data['first_name'] ?? $tbDotsRecord->patient->first_name,
                'middle_initial' => $data['middle_initial'] ?? $tbDotsRecord->patient->middle_initial,
                'last_name' => $data['last_name'] ?? $tbDotsRecord->patient->last_name,
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']) ?? $tbDotsRecord->patient->full_name,
                'age' => $data['age'] ?? $tbDotsRecord->patient->age,
                'sex' => $data['sex'] ?? $tbDotsRecord->patient->sex,
                'civil_status' => $data['civil_status'] ?? $tbDotsRecord->patient->civil_status,
                'contact_number' => $data['contact_number'] ?? $tbDotsRecord->patient->contact_number,
                'date_of_birth' => $data['date_of_birth'] ?? $tbDotsRecord->patient->date_of_birth,
                'nationality' => $data['nationality'] ?? $tbDotsRecord->patient->nationality,
                'date_of_registration' => $data['date_of_registration'] ?? $tbDotsRecord->patient->date_of_registration,
                'place_of_birth' => $data['place_of_birth'] ?? $tbDotsRecord->patient->place_of_birth,
            ]);
            // update the address
            $blk_n_street = explode(',', $data['street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street' => $blk_n_street[1] ?? $address->street,
                'purok' => $data['brgy'] ?? $address->purok
            ]);

            $tbDotsRecord->patient->refresh();

            // update medical record
            $tbDotsRecord->tb_dots_medical_record->update([
                'health_worker_id' => $data['handled_by'] ?? $tbDotsRecord->tb_dots_medical_record->health_worker_id,
                'patient_name' => $tbDotsRecord->patient->full_name,
                'philheath_id_no' => $data['philheath_id']?? $tbDotsRecord->tb_dots_medical_record->philheath_id_no,
                'blood_pressure' => $data['blood_pressure'] ?? $tbDotsRecord->tb_dots_medical_record->blood_pressure,
                'temperature' => $data['temperature'] ?? $tbDotsRecord->tb_dots_medical_record->temperature,
                'pulse_rate' => $data['pulse_rate'] ?? $tbDotsRecord->tb_dots_medical_record->pulse_rate,
                'respiratory_rate' => $data['respiratory_rate'] ?? $tbDotsRecord->tb_dots_medical_record->respiratory_rate,
                'height' => $data['height'] ?? $tbDotsRecord->tb_dots_medical_record->height,
                'weight' => $data['weight'] ?? $tbDotsRecord->tb_dots_medical_record->weight
            ]);

            // update each case patient name

            foreach ($tbDotsCase as $record) {
                $record->update([
                    'patient_name' => $tbDotsRecord->patient->full_name ?? $record->patient_name
                ]);
            };

            return response()->json(['message' => 'Updating Patient information Successfully'], 200);

        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage() // e.g. "Attempt to read property 'blood_pressure' on null"
            ], 500);
        }
      
    }

    public function caseInfo($id){
        try{
            $caseInfo = tb_dots_case_records::with('tb_dots_maintenance_med')->findOrFail($id);
            $healthWorker = staff::where('user_id', $caseInfo->health_worker_id)->firstOrFail();

            return response()-> json(['caseInfo'=> $caseInfo, 'healthWorker'=> $healthWorker]);
        }catch(\Exception $e){
            return response() -> json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function updateCase(Request $request,$id){
        try{
            $caseRecord = tb_dots_case_records::findOrFail($id);

            $data = $request ->validate([
                'edit_type_of_tuberculosis' => 'required|string',
                'edit_type_of_tb_case' =>  'required|string',
                'edit_date_of_diagnosis' => 'required|date',
                'edit_name_of_physician' => 'sometimes|nullable|string',
                'edit_sputum_test_results' => 'sometimes|nullable|string',
                'edit_treatment_category' => 'sometimes|nullable|string',
                'edit_date_administered' => 'required|date',
                'edit_side_effect' =>  'sometimes|nullable|string',
                'edit_tb_remarks' => 'sometimes|nullable|string',
                'edit_tb_outcome' => 'sometimes|nullable|string',
            ]);

            $caseRecord-> update([
                'type_of_tuberculosis' => $data['edit_type_of_tuberculosis']?? $caseRecord->type_of_tuberculosis,
                'type_of_tb_case' => $data['edit_type_of_tb_case']?? $caseRecord ->type_of_tb_case,
                'date_of_diagnosis' => $data['edit_date_of_diagnosis']?? $caseRecord->date_of_diagnosis,
                'name_of_physician' => $data['edit_name_of_physician']??$caseRecord-> name_of_physician,
                'sputum_test_results' => $data['edit_sputum_test_results'] ?? $caseRecord->sputum_test_results,
                'treatment_category' => $data['edit_treatment_category']?? $caseRecord->treatment_categorym,
                'date_administered' => $data['edit_date_administered'] ?? $caseRecord->date_administered,
                'side_effect' =>  $data['edit_side_effect'] ?? $caseRecord-> side_effect,
                'remarks' => $data['edit_tb_remarks'] ?? $caseRecord->remarks,
                'outcome' => $data['edit_tb_outcome'] ?? $caseRecord->outcome,
                'status' => 'Done'
            ]);

            $medicineAdministered = tb_dots_maintenance_medicines::where('tb_dots_case_id',$caseRecord->id)->delete();

            // insert in the maintenance medication
            $maintenanceMedicationData = $request->validate([
                'medicines' => 'sometimes|nullable|array',
                'dosage_n_frequencies' => 'sometimes|nullable|array',
                'medicine_quantity' => 'sometimes|nullable|array',
                'start_date' => 'sometimes|nullable|array',
                'end_date' => 'sometimes|nullable|array'
            ]);

            if (!empty($maintenanceMedicationData['medicines'])) {
                // insert each record
                foreach ($maintenanceMedicationData['medicines'] as $index => $value) {
                    tb_dots_maintenance_medicines::create([
                        'tb_dots_case_id' => $caseRecord->id,
                        'medicine_name' => $value,
                        'dosage_n_frequency' => $maintenanceMedicationData['dosage_n_frequencies'][$index],
                        'quantity' => $maintenanceMedicationData['medicine_quantity'][$index],
                        'start_date' => $maintenanceMedicationData['start_date'][$index],
                        'end_date' => $maintenanceMedicationData['end_date'][$index],
                    ]);
                };
            }

            return response()->json(['message' => 'Tb Dots Patient information is Successfully updated'], 200);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function addPatientCheckUp(Request $request, $id){
        try{
            $data = $request->validate([
                'patient_name' => 'required|string',
                'date_of_visit' =>'required|date',
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
                'adherence_of_treatment' => 'required|string',
                'side_effect' => 'sometimes|nullable|string',
                'progress_note' => 'sometimes|nullable|string',
                'sputum_test_result' => 'sometimes|nullable|string',
                'treatment_phase' => 'sometimes|nullable|string',
                'outcome' => 'sometimes|nullable|string',
                'handled_by' => 'required'
            ]);

            // create the record
            $tbDotsCheckUpRecord = tb_dots_check_ups::create([
                'medical_record_case_id' => $id,
                'health_worker_id' => $data['handled_by']??null,
                'patient_name' => $data['patient_name']??null,
                'date_of_visit' => $data['date_of_visit']??null,
                'blood_pressure' => $data['blood_pressure']??null,
                'temperature' => $data['temperature']??null,
                'pulse_rate' => $data['pulse_rate']??null,
                'respiratory_rate' => $data['respiratory_rate']??null,
                'height' => $data['height']??null,
                'weight' => $data['weight']??null,
                'adherence_of_treatment' => $data['adherence_of_treatment']??null,
                'side_effect' => $data['side_effect']??null,
                'progress_note' => $data['progress_note']??null,
                'sputum_test_result' => $data['sputum_test_result']??null,
                'treatment_phase' => $data['treatment_phase']??null,
                'outcome' => $data['outcome']??null,
                'status'=> 'Done'
            ]);

            return response()->json(['message' => 'Tb Dots Patient information is added Successfully'], 200);

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

    public function viewPatientCheckUp($id){
        try{
            $checkUpRecord = tb_dots_check_ups::findOrFail($id);

            return response()->json(['checkUpInfo' => $checkUpRecord], 200);
        }catch(\Exception $e){
            return response()->json([
                'errors' => $e->getMessage()
            ], 422);
        }
       
    }
    public function updatePatientCheckUpInfo(Request $request, $id){
        try{
            $checkUpRecord = tb_dots_check_ups::findOrFail($id);
            $data = $request->validate([
                'edit_checkup_date_of_visit' => 'required|date',
                'edit_checkup_blood_pressure' => 'sometimes|nullable|numeric',
                'edit_checkup_temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'edit_checkup_pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'edit_checkup_respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'edit_checkup_height'            => 'nullable|numeric|between:30,300', // cm range
                'edit_checkup_weight'            => 'nullable|numeric|between:1,500',  // kg range
                'edit_checkup_adherence_of_treatment' => 'required|string',
                'edit_checkup_side_effect' => 'sometimes|nullable|string',
                'edit_checkup_progress_note' => 'sometimes|nullable|string',
                'edit_checkup_sputum_test_result' => 'sometimes|nullable|string',
                'edit_checkup_treatment_phase' => 'sometimes|nullable|string',
                'edit_checkup_outcome' => 'sometimes|nullable|string',
            ]);

            $checkUpRecord-> update([
                'date_of_visit' => $data['edit_checkup_date_of_visit'] ?? $checkUpRecord->date_of_visit,
                'blood_pressure' => $data['edit_checkup_blood_pressure'] ?? $checkUpRecord->blood_pressure,
                'temperature' => $data['edit_checkup_temperature'] ?? $checkUpRecord->temperature,
                'pulse_rate' => $data['edit_checkup_pulse_rate'] ?? $checkUpRecord->pulse_rate,
                'respiratory_rate' => $data['edit_checkup_respiratory_rate'] ?? $checkUpRecord->respiratory_rate,
                'height' => $data['edit_checkup_height'] ?? $checkUpRecord->height,
                'weight' => $data['edit_checkup_weight'] ?? $checkUpRecord->weight,
                'adherence_of_treatment' => $data['edit_checkup_adherence_of_treatment'] ?? $checkUpRecord->adherence_of_treatment,
                'side_effect' => $data['edit_checkup_side_effect'] ?? $checkUpRecord->side_effect,
                'progress_note' => $data['edit_checkup_progress_note'] ?? $checkUpRecord->progress_note,
                'sputum_test_result' => $data['edit_checkup_sputum_test_result'] ?? $checkUpRecord->sputum_test_result,
                'treatment_phase' => $data['edit_checkup_treatment_phase'] ?? $checkUpRecord->treatment_phase,
                'outcome' => $data['edit_checkup_outcome'] ?? $checkUpRecord->outcome
            ]);
            return response()->json(['message' => 'Tb Dots Patient Check-up information is updated Successfully'], 200);
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
