<?php

namespace App\Http\Controllers;

use App\Models\donor_names;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\pregnancy_history_questions;
use App\Models\pregnancy_plans;
use App\Models\pregnancy_timeline_records;
use App\Models\prenatal_assessment;
use App\Models\prenatal_assessments;
use App\Models\prenatal_case_records;
use App\Models\prenatal_medical_records;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class PrenatalController extends Controller
{
    //

    public function addPatient(Request $request)
    {
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
            ]);

            $medicalCaseData = $request->validate([
                'family_head_name' => 'sometimes|nullable|string',
                'family_serial_no' => 'sometimes|nullable|string',
                'blood_type' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
                'philHealth_number' => 'sometimes|nullable|string',
                'family_planning' => 'sometimes|nullable|string'
            ]);

            // case record
            $prenatalCaseData = $request->validate([
                'G' => 'sometimes|nullable|numeric',
                'P' => 'sometimes|nullable|numeric',
                'T' => 'sometimes|nullable|numeric',
                'premature' => 'sometimes|nullable|numeric',
                'abortion' => 'sometimes|nullable|numeric',
                'living_children' => 'sometimes|nullable|numeric',
                'preg_year' => 'required|array',
                'type_of_delivery' => 'required|array',
                'place_of_delivery' => 'required|array',
                'birth_attendant' => 'required|array',
                'compilation' => 'required|array',
                'outcome' => 'required|array',
                'LMP' => 'required|date',
                'expected_delivery' => 'required|date',
                'menarche' => 'sometimes|nullable|numeric',
                'TT1' => 'sometimes|nullable|numeric',
                'TT2' => 'sometimes|nullable|numeric',
                'TT3' => 'sometimes|nullable|numeric',
                'TT4' => 'sometimes|nullable|numeric',
                'TT5' => 'sometimes|nullable|numeric',
                'nurse_decision' => 'sometimes|nullable|string',
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range

            ]);

            // assessment validation
            $assessment = $request->validate([
                'spotting' => 'sometimes|nullable|string',
                'edema' => 'sometimes|nullable|string',
                'severe_headache' => 'sometimes|nullable|string',
                'blumming_of_vission' => 'sometimes|nullable|string',
                'watery_discharge' => 'sometimes|nullable|string',
                'severe_vomiting' => 'sometimes|nullable|string',
                'hx_of_smoking' => 'sometimes|nullable|string',
                'alcohol_drinker' => 'sometimes|nullable|string',
                'drug_intake' => 'sometimes|nullable|string'
            ]);

            // pregnancy history questions validation
            $pregnancy_question = $request->validate([
                'number_of_children' => 'sometimes|nullable|numeric',
                'answer_1' => 'sometimes|nullable|string',
                'answer_2' => 'sometimes|nullable|string',
                'answer_3' => 'sometimes|nullable|string',
                'answer_4' => 'sometimes|nullable|string',
                'q2_answer1' => 'sometimes|nullable|string',
                'q2_answer2' => 'sometimes|nullable|string',
                'q2_answer3' => 'sometimes|nullable|string',
                'q2_answer4' => 'sometimes|nullable|string',
                'q2_answer5' => 'sometimes|nullable|string',
            ]);

            // pregnancy plan validation

            $pregnancy_plan = $request->validate([
                'midwife_name' => 'sometimes|nullable|string',
                'place_of_birth' => 'sometimes|nullable|string',
                'authorized_by_philhealth' => 'sometimes|nullable|string',
                'cost_of_property' => 'sometimes|nullable|string',
                'payment_method' => 'sometimes|nullable|string',
                'transportation_mode' => 'sometimes|nullable|string',
                'accompany_person_to_hospital' => 'sometimes|nullable|string',
                'accompnay_through_pregnancy' => 'sometimes|nullable|string',
                'care_person' => 'sometimes|nullable|string',
                'emergency_person_name' => 'sometimes|nullable|string',
                'emergency_person_residency' => 'sometimes|nullable|string',
                'emergency_person_contact_number' => 'sometimes|nullable|string',
                'signature_image' => 'sometimes|image|mimes:jpg,jpeg,png|max:5120',
                'names_of_donor' => 'sometimes|nullable|array'
            ]);

            // insertion of the information

            // PATIENT INFO
            // create the patient information record

            $prenatalPatient = patients::create([
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
            $prenatalPatientId =  $prenatalPatient->id;

            // add the patient address
            // dd($patient->id);
            $blk_n_street = explode(',', $patientData['street']);
            // dd($blk_n_street);
            patient_addresses::create([
                'patient_id' => $prenatalPatientId,
                'house_number' => $blk_n_street[0] ?? $patientData['blk_n_street'],
                'street' => $blk_n_street[1] ?? null,
                'purok' => $patientData['brgy'],
                'postal_code' => '4109',
                'latitude' => null,
                'longitude' => null,
            ]);

            // add record for medical_case table
            $medicalCase = medical_record_cases::create([
                'patient_id' => $prenatalPatientId,
                'type_of_case' => $patientData['type_of_patient'],
            ]);

            // get the id of the medical case for prenatal medical record and case
            $medicalCaseId = $medicalCase->id;

            // create a record for the prenatal medical record
            $prenatalMedicalRecord = prenatal_medical_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'family_head_name' => $medicalCaseData['family_head_name'] ?? null,
                'blood_type' => $medicalCaseData['blood_type'] ?? null,
                'religion' => $medicalCaseData['religion'] ?? null,
                'philHealth_number' => $medicalCaseData['philHealth_number'],
                'family_serial_no' => $medicalCaseData['family_serial_no'],
                'family_planning_decision' => $medicalCaseData['family_planning'],
                'health_worker_id' => $patientData['handled_by'],
                'type_of_record' => 'Medical Record'
            ]);

            // create a record for prenatal case
            $prenatalCaseRecord = prenatal_case_records::create([
                'medical_record_case_id' => $medicalCaseId,
                'patient_name' => $prenatalPatient->full_name,
                'G' => $prenatalCaseData['G'] ?? null,
                'P' => $prenatalCaseData['P'] ?? null,
                'T' => $prenatalCaseData['T'] ?? null,
                'premature' => $prenatalCaseData['premature'] ?? null,
                'abortion' => $prenatalCaseData['abortion'] ?? null,
                'living_children' => $prenatalCaseData['living_children'],
                'LMP' => $prenatalCaseData['LMP'],
                'expected_delivery' => $prenatalCaseData['expected_delivery'],
                'menarche' => $prenatalCaseData['menarche'],
                'tetanus_toxoid_1' => $prenatalCaseData['TT1'] ?? null,
                'tetanus_toxoid_2' => $prenatalCaseData['TT2'] ?? null,
                'tetanus_toxoid_3' => $prenatalCaseData['TT3'] ?? null,
                'tetanus_toxoid_4' => $prenatalCaseData['TT4'] ?? null,
                'tetanus_toxoid_5' => $prenatalCaseData['TT5'] ?? null,
                'decision' => $prenatalCaseData['nurse_decision'] ?? null,
                'blood_pressure' => $prenatalCaseData['blood_pressure'] ?? null,
                'temperature' => $prenatalCaseData['temperature'] ?? null,
                'pulse_rate' => $prenatalCaseData['pulse_rate'] ?? null,
                'respiratory_rate' => $prenatalCaseData['respiratory_rate'] ?? null,
                'height' => $prenatalCaseData['height'] ?? null,
                'weight' => $prenatalCaseData['weight'] ?? null,
                'health_worker_id'=> $patientData['handled_by'],
                'type_of_record'=> 'Case Record'
            ]);

            // insert the pregnancy timeline
            foreach ($prenatalCaseData['preg_year'] as $index => $year) {
                pregnancy_timeline_records::create([
                    'prenatal_case_record_id' => $prenatalCaseRecord->id,
                    'year' => $year ?? null,
                    'type_of_delivery' => $prenatalCaseData['type_of_delivery'][$index] ?? null,
                    'place_of_delivery' => $prenatalCaseData['place_of_delivery'][$index] ?? null,
                    'birth_attendant' => $prenatalCaseData['birth_attendant'][$index] ?? null,
                    'compilation' => $prenatalCaseData['compilation'][$index] ?? null,
                    'outcome' => $prenatalCaseData['outcome'][$index] ?? null,
                ]);
            };


            // insert assessment
            $prenatalAssessment = prenatal_assessments::create([
                'prenatal_case_record_id' => $prenatalCaseRecord->id,
                'spotting' => $assessment['spotting'] ?? null,
                'edema' => $assessment['edema'] ?? null,
                'severe_headache' => $assessment['severe_headache'] ?? null,
                'blumming_vission' => $assessment['blumming_of_vission'] ?? null,
                'water_discharge' => $assessment['water_discharge'] ?? null,
                'severe_vomitting' => $assessment['severe_vomiting'] ?? null,
                'hx_smoking' => $assessment['hx_of_smoking'] ?? null,
                'alchohol_drinker' => $assessment['alcohol_drinker'] ?? null,
                'drug_intake' => $assessment['drug_intake'] ?? null,
            ]);



            // pregnancy history questions
            $pregnancyQuestions = pregnancy_history_questions::create([
                'prenatal_case_record_id' => $prenatalCaseRecord->id,
                'number_of_children' => $pregnancy_question['number_of_children'] ?? null,
                'answer_1' => $pregnancy_question['answer_1'] ?? null,
                'answer_2' => $pregnancy_question['answer_2'] ?? null,
                'answer_3' => $pregnancy_question['answer_3'] ?? null,
                'answer_4' => $pregnancy_question['answer_4'] ?? null,
                'q2_answer1' => $pregnancy_question['q2_answer1'] ?? null,
                'q2_answer2' => $pregnancy_question['q2_answer2'] ?? null,
                'q2_answer3' => $pregnancy_question['q2_answer3'] ?? null,
                'q2_answer4' => $pregnancy_question['q2_answer4'] ?? null,
                'q2_answer5' => $pregnancy_question['q2_answer5'] ?? null,
            ]);

            // pregnancy plan


            $pregnancyPlanRecord = pregnancy_plans::create([
                'medical_record_case_id' => $medicalCaseId,
                'patient_name' => $prenatalPatient->full_name,
                'midwife_name' => $pregnancy_plan['midwife_name'] ?? null,
                'place_of_birth' => $pregnancy_plan['place_of_birth'] ?? null,
                'authorized_by_philhealth' => $pregnancy_plan['authorized_by_philhealth'] ?? null,
                'cost_of_property' => $pregnancy_plan['cost_of_property'] ?? null,
                'payment_method' => $pregnancy_plan['payment_method'] ?? null,
                'transportation_mode' => $pregnancy_plan['transportation_mode'] ?? null,
                'accompany_person_to_hospital' => $pregnancy_plan['accompany_person_to_hospital'] ?? null,
                'accompnay_through_pregnancy' =>  $pregnancy_plan['accompnay_through_pregnancy'] ?? null,
                'care_person' => $pregnancy_plan['care_person'] ?? null,
                'emergency_person_name' => $pregnancy_plan['emergency_person_name'] ?? null,
                'emergency_person_residency' => $pregnancy_plan['emergency_person_residency'] ?? null,
                'emergency_person_contact_number' => $pregnancy_plan['emergency_person_contact_number'] ?? null,
                'signature' => $pregnancy_plan['signature_image'] ?? null,
            ]);

            // insert 
            foreach ($pregnancy_plan['names_of_donor'] as $index => $name) {
                donor_names::create([
                    'pregnancy_plan_id' => $pregnancyPlanRecord->id,
                    'donor_name' => $name
                ]);
            };

            return response()->json(['message' => 'Patient has been added'], 201);
        
        } catch (ValidationException $e) {
            return response()->json([
                'errors'=> $e->errors()
            ], 422);
        }
    }

    public function updateDetails(Request $request, $id){
        try{
            $prenatalRecord = medical_record_cases::with(['patient', 'prenatal_medical_record'])->where('id', $id)->firstOrFail();
            $address = patient_addresses::where('patient_id', $prenatalRecord->patient->id)->firstOrFail();
            
            $data = $request -> validate([
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
                'family_head' => 'sometimes|nullable|string',
                'civil_status' => 'sometimes|nullable|string',
                'blood_type' => 'sometimes|nullable|string',
                'religion' => 'sometimes|nullable|string',
                'philhealth' => 'sometimes|nullable|string',
                'philhealth_number' => 'sometimes|nullable|string',
                'family_planning' => 'sometimes|nullable|string',
                'street' => 'required',
                'brgy' => 'required',
                'blood_pressure' => 'sometimes|nullable|numeric',
                'temperature'       => 'nullable|numeric|between:30,45', // typical human body range
                'pulse_rate'        => 'nullable|string|max:20',         // stored as string, e.g., "60-100"
                'respiratory_rate'  => 'nullable|integer|min:5|max:60',  // breaths/min
                'height'            => 'nullable|numeric|between:30,300', // cm range
                'weight'            => 'nullable|numeric|between:1,500',  // kg range
                'number_of_children' => 'sometimes|nullable|numeric',
                'answer_1' => 'sometimes|nullable|string',
                'answer_2' => 'sometimes|nullable|string',
                'answer_3' => 'sometimes|nullable|string',
                'answer_4' => 'sometimes|nullable|string',
                'q2_answer1' => 'sometimes|nullable|string',
                'q2_answer2' => 'sometimes|nullable|string',
                'q2_answer3' => 'sometimes|nullable|string',
                'q2_answer4' => 'sometimes|nullable|string',
                'q2_answer5' => 'sometimes|nullable|string',
                'family_serial_no'=> 'sometimes|nullable|numeric'
            ]);

            // update the patient data first
            $prenatalRecord -> patient -> update([
                'first_name' => $data['first_name']?? $prenatalRecord-> patient-> first_name,
                'middle_initial' => $data['middle_initial'] ?? $prenatalRecord->patient->middle_initial,
                'last_name' => $data['last_name']?? $prenatalRecord->patient->last_name,
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']) ?? $prenatalRecord->patient->full_name,
                'age' => $data['age'] ?? $prenatalRecord->patient->age,
                'sex' => $data['sex'] ?? $prenatalRecord->patient->sex,
                'civil_status' => $data['civil_status'] ?? $prenatalRecord->patient->civil_status,
                'contact_number' => $data['contact_number'] ?? $prenatalRecord->patient->contact_number,
                'date_of_birth' => $data['date_of_birth'] ?? $prenatalRecord->patient->date_of_birth,
                'nationality' => $data['nationality'] ?? $prenatalRecord->patient-> nationality,
                'date_of_registration' => $data['date_of_registration'] ?? $prenatalRecord->patient->date_of_registration,
                'place_of_birth' => $data['place_of_birth'] ?? $prenatalRecord->patient->place_of_birth,
            ]);

            // update the address
            $blk_n_street = explode(',', $data['street']);
            $address->update([
                'house_number' => $blk_n_street[0] ?? $address->house_number,
                'street' => $blk_n_street[1] ?? $address->street,
                'purok' => $data['brgy'] ?? $address->purok
            ]);

            $prenatalRecord-> prenatal_medical_record-> update([
                'family_head_name' => $data['family_head']?? $prenatalRecord->prenatal_medical_record-> family_head_name,
                'blood_type'=> $data['blood_type']?? $prenatalRecord->prenatal_medical_record->blood_type,
                'religion'=>$data['religion']?? $prenatalRecord->prenatal_medical_record->religion,
                'philHealth_number'=> $data['philhealth_number']?? $prenatalRecord->prenatal_medical_record->philHealth_number,
                'family_serial_no'=> $data['family_serial_no']?? $prenatalRecord->prenatal_medical_record->family_serial_no,
                'family_planning_decision'=> $data['family_planning']?? $prenatalRecord->prenatal_medical_record->family_planning_decision,
                'health_worker_id'=> $data['handled_by']?? $prenatalRecord->prenatal_medical_record->health_worker_id,
            ]);
            // update the case info
            $prenatalCaseRecord = prenatal_case_records::where('medical_record_case_id', $prenatalRecord->id)->firstOrFail();
            $prenatalCaseRecord-> update([
                'health_worker_id'=> $data['handled_by']?? $prenatalRecord->prenatal_case_record-> health_worker_id,
                'blood_pressure'=> $data['blood_pressure']?? $prenatalRecord->prenatal_case_record->blood_pressure,
                'temperature'=>$data['temperature']?? $prenatalRecord->prenatal_case_record->temperature,
                'pulse_rate'=>$data['pulse_rate']?? $prenatalRecord->prenatal_case_record->pulse_rate,
                'respiratory_rate'=> $data['respiratory_rate']?? $prenatalRecord->prenatal_case_record->respiratory_rate,
                'height'=> $data['height']?? $prenatalRecord->prenatal_case_record->height,
                'weight'=> $data['weight']?? $prenatalRecord->prenatal_case_record->weight
            ]);

            // update the pregnancy history
            $pregnancyHistory = pregnancy_history_questions::where('prenatal_case_record_id', $prenatalCaseRecord->id)->firstOrfail();
            $pregnancyHistory-> update([
                'number_of_children'=> $data['number_of_children']?? $pregnancyHistory-> number_of_children,
                'answer_1'=> $data['answer_1'] ?? $pregnancyHistory->answer_1,
                'answer_2' => $data['answer_2'] ?? $pregnancyHistory->answer_2,
                'answer_3' => $data['answer_3'] ?? $pregnancyHistory->answer_3,
                'answer_4' => $data['answer_4'] ?? $pregnancyHistory->answer_4,
                'q2_answer1'=> $data['q2_answer1']??$pregnancyHistory-> q2_answer1,
                'q2_answer2' => $data['q2_answer2'] ?? $pregnancyHistory->q2_answer2,
                'q2_answer3' => $data['q2_answer3'] ?? $pregnancyHistory->q2_answer3,
                'q2_answer4' => $data['q2_answer4'] ?? $pregnancyHistory->q2_answer4,
                'q2_answer5' => $data['q2_answer5'] ?? $pregnancyHistory->q2_answer5,
            ]);
            

            return response()-> json(['message'=> 'Updating Patient information Successfully'],200);
        }catch(ValidationException $e){
            return response() -> json([
                'errors'=> $e -> errors()
            ],422);
        }
    }
}
