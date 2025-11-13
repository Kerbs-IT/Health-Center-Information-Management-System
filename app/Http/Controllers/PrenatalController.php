<?php

namespace App\Http\Controllers;

use App\Models\donor_names;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\pregnancy_checkups;
use App\Models\pregnancy_history_questions;
use App\Models\pregnancy_plans;
use App\Models\pregnancy_timeline_records;
use App\Models\prenatal_assessment;
use App\Models\prenatal_assessments;
use App\Models\prenatal_case_records;
use App\Models\prenatal_check_ups;
use App\Models\prenatal_medical_records;
use App\Models\staff;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class PrenatalController extends Controller
{
    //

    public function addPatient(Request $request)
    {
        try {
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
                'place_of_pregnancy' => 'sometimes|nullable|string',
                'authorized_by_philhealth' => 'sometimes|nullable|string',
                'cost_of_pregnancy' => 'sometimes|nullable|number',
                'payment_method' => 'sometimes|nullable|string',
                'transportation_mode' => 'sometimes|nullable|string',
                'accompany_person_to_hospital' => 'sometimes|nullable|string',
                'accompany_through_pregnancy' => 'sometimes|nullable|string',
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
                'health_worker_id' => $patientData['handled_by'],
                'type_of_record' => 'Case Record'
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
                'place_of_pregnancy' => $pregnancy_plan['place_of_pregnancy'] ?? null,
                'authorized_by_philhealth' => $pregnancy_plan['authorized_by_philhealth'] ?? null,
                'cost_of_pregnancy' => $pregnancy_plan['cost_of_pregnancy'] ?? null,
                'payment_method' => $pregnancy_plan['payment_method'] ?? null,
                'transportation_mode' => $pregnancy_plan['transportation_mode'] ?? null,
                'accompany_person_to_hospital' => $pregnancy_plan['accompany_person_to_hospital'] ?? null,
                'accompany_through_pregnancy' =>  $pregnancy_plan['accompany_through_pregnancy'] ?? null,
                'care_person' => $pregnancy_plan['care_person'] ?? null,
                'emergency_person_name' => $pregnancy_plan['emergency_person_name'] ?? null,
                'emergency_person_residency' => $pregnancy_plan['emergency_person_residency'] ?? null,
                'emergency_person_contact_number' => $pregnancy_plan['emergency_person_contact_number'] ?? null,
                'signature' => $pregnancy_plan['signature_image'] ?? null,
                'type_of_record' => 'Pregnancy Plan Record'
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
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function updateDetails(Request $request, $id)
    {
        try {
            $prenatalRecord = medical_record_cases::with(['patient', 'prenatal_medical_record'])->where('id', $id)->firstOrFail();
            $address = patient_addresses::where('patient_id', $prenatalRecord->patient->id)->firstOrFail();
            $caseRecord = prenatal_case_records::where('medical_record_case_id', $prenatalRecord->id)->firstOrFail();

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
                'family_serial_no' => 'sometimes|nullable|numeric',
                'nurse_decision' => 'sometimes|nullable|numeric'
            ]);

            // update the patient data first
            $prenatalRecord->patient->update([
                'first_name' => $data['first_name'] ?? $prenatalRecord->patient->first_name,
                'middle_initial' => $data['middle_initial'] ?? $prenatalRecord->patient->middle_initial,
                'last_name' => $data['last_name'] ?? $prenatalRecord->patient->last_name,
                'full_name' => ($data['first_name'] . ' ' . $data['middle_initial'] . ' ' . $data['last_name']) ?? $prenatalRecord->patient->full_name,
                'age' => $data['age'] ?? $prenatalRecord->patient->age,
                'sex' => $data['sex'] ?? $prenatalRecord->patient->sex,
                'civil_status' => $data['civil_status'] ?? $prenatalRecord->patient->civil_status,
                'contact_number' => $data['contact_number'] ?? $prenatalRecord->patient->contact_number,
                'date_of_birth' => $data['date_of_birth'] ?? $prenatalRecord->patient->date_of_birth,
                'nationality' => $data['nationality'] ?? $prenatalRecord->patient->nationality,
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

            // update the case
            $caseRecord->update([
                'decision' => $data['nurse_decision'] ?? $caseRecord->decision
            ]);

            $prenatalRecord->prenatal_medical_record->update([
                'family_head_name' => $data['family_head'] ?? $prenatalRecord->prenatal_medical_record->family_head_name,
                'blood_type' => $data['blood_type'] ?? $prenatalRecord->prenatal_medical_record->blood_type,
                'religion' => $data['religion'] ?? $prenatalRecord->prenatal_medical_record->religion,
                'philHealth_number' => $data['philhealth_number'] ?? $prenatalRecord->prenatal_medical_record->philHealth_number,
                'family_serial_no' => $data['family_serial_no'] ?? $prenatalRecord->prenatal_medical_record->family_serial_no,
                'family_planning_decision' => $data['family_planning'] ?? $prenatalRecord->prenatal_medical_record->family_planning_decision,
                'health_worker_id' => $data['handled_by'] ?? $prenatalRecord->prenatal_medical_record->health_worker_id,
            ]);
            // update the case info
            $prenatalCaseRecord = prenatal_case_records::where('medical_record_case_id', $prenatalRecord->id)->firstOrFail();
            $prenatalCaseRecord->update([
                'patient_name' => $prenatalRecord->patient->full_name,
                'health_worker_id' => $data['handled_by'] ?? $prenatalRecord->prenatal_case_record->health_worker_id,
                'blood_pressure' => $data['blood_pressure'] ?? $prenatalRecord->prenatal_case_record->blood_pressure,
                'temperature' => $data['temperature'] ?? $prenatalRecord->prenatal_case_record->temperature,
                'pulse_rate' => $data['pulse_rate'] ?? $prenatalRecord->prenatal_case_record->pulse_rate,
                'respiratory_rate' => $data['respiratory_rate'] ?? $prenatalRecord->prenatal_case_record->respiratory_rate,
                'height' => $data['height'] ?? $prenatalRecord->prenatal_case_record->height,
                'weight' => $data['weight'] ?? $prenatalRecord->prenatal_case_record->weight
            ]);

            // update the pregnancy history
            $pregnancyHistory = pregnancy_history_questions::where('prenatal_case_record_id', $prenatalCaseRecord->id)->firstOrfail();
            $pregnancyHistory->update([
                'number_of_children' => $data['number_of_children'] ?? $pregnancyHistory->number_of_children,
                'answer_1' => $data['answer_1'] ?? $pregnancyHistory->answer_1,
                'answer_2' => $data['answer_2'] ?? $pregnancyHistory->answer_2,
                'answer_3' => $data['answer_3'] ?? $pregnancyHistory->answer_3,
                'answer_4' => $data['answer_4'] ?? $pregnancyHistory->answer_4,
                'q2_answer1' => $data['q2_answer1'] ?? $pregnancyHistory->q2_answer1,
                'q2_answer2' => $data['q2_answer2'] ?? $pregnancyHistory->q2_answer2,
                'q2_answer3' => $data['q2_answer3'] ?? $pregnancyHistory->q2_answer3,
                'q2_answer4' => $data['q2_answer4'] ?? $pregnancyHistory->q2_answer4,
                'q2_answer5' => $data['q2_answer5'] ?? $pregnancyHistory->q2_answer5,
            ]);
            // update pregnancy plan patient name
            $pregnancyPlanRecord = pregnancy_plans::where('medical_record_case_id',$id)->firstOrFail();
            $pregnancyPlanRecord -> update([
                'patient_name' => $prenatalRecord->patient-> full_name
            ]);


            return response()->json(['message' => 'Updating Patient information Successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function viewPregnancyPlan($id)
    {
        try {
            $pregnancyRecord = pregnancy_plans::with('donor_name')->findOrFail($id);

            return response()->json([
                'pregnancyPlan' => $pregnancyRecord
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function updateCase(Request $request, $id)
    {
        try {
            // get the case record of the patient
            $caseRecord = prenatal_case_records::findOrFail($id);
            // get the prenatal timeline record using the id of the case record then delete to reset
            $prenatalTimelineRecord = pregnancy_timeline_records::where('prenatal_case_record_id', $id)->delete();
            // case record
            $data = $request->validate([
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
                'tt1' => 'sometimes|nullable|numeric',
                'tt2' => 'sometimes|nullable|numeric',
                'tt3' => 'sometimes|nullable|numeric',
                'tt4' => 'sometimes|nullable|numeric',
                'tt5' => 'sometimes|nullable|numeric'

            ]);

            // assessment validation
            $assessment = $request->validate([
                'spotting' => 'sometimes|nullable|string',
                'edema' => 'sometimes|nullable|string',
                'severe_headache' => 'sometimes|nullable|string',
                'blurring_of_vission' => 'sometimes|nullable|string',
                'watery_discharge' => 'sometimes|nullable|string',
                'severe_vomiting' => 'sometimes|nullable|string',
                'hx_smoking' => 'sometimes|nullable|string',
                'alcohol_drinker' => 'sometimes|nullable|string',
                'drug_intake' => 'sometimes|nullable|string'
            ]);

            // if it passess the validation,then:
            // update the values
            $caseRecord->update([
                'G' => $data['G'] ?? $caseRecord->G,
                'P' => $data['P'] ?? $caseRecord->P,
                'T' => $data['T'] ?? $caseRecord->T,
                'premature' => $data['premature'] ?? $caseRecord->premature,
                'abortion' => $data['abortion'] ?? $caseRecord->abortion,
                'living_children' => $data['living_children'] ?? $caseRecord->living_children,
                'LMP' => $data['LMP'] ?? $caseRecord->LMP,
                'expected_delivery' => $data['expected_delivery'] ?? $caseRecord->expected_delivery,
                'menarche' => $data['menarche'] ?? $caseRecord->menarche,
                'tetanus_toxoid_1' => $data['tt1'] ?? $caseRecord->tetanus_toxoid_1,
                'tetanus_toxoid_2' => $data['tt2'] ?? $caseRecord->tetanus_toxoid_2,
                'tetanus_toxoid_3' => $data['tt3'] ?? $caseRecord->tetanus_toxoid_3,
                'tetanus_toxoid_4' => $data['tt4'] ?? $caseRecord->tetanus_toxoid_4,
                'tetanus_toxoid_5' => $data['tt5'] ?? $caseRecord->tetanus_toxoid_5,
                'decision' => $data['nurse_decision'] ?? $caseRecord->decision,
            ]);

            // after resetting the record of pregnancy timeline add new record
            foreach ($data['preg_year'] as $index => $year) {
                pregnancy_timeline_records::create([
                    'prenatal_case_record_id' => $id,
                    'year' => $year ?? null,
                    'type_of_delivery' => $data['type_of_delivery'][$index] ?? null,
                    'place_of_delivery' => $data['place_of_delivery'][$index] ?? null,
                    'birth_attendant' => $data['birth_attendant'][$index] ?? null,
                    'compilation' => $data['compilation'][$index] ?? null,
                    'outcome' => $data['outcome'][$index] ?? null,
                ]);
            }

            // update the assessment
            $assessmentRecord = prenatal_assessments::where('prenatal_case_record_id', $id)->firstOrFail();

            $assessmentRecord->update([
                'spotting' => $assessment['spotting'] ?? 'no',
                'edema' => $assessment['edema'] ?? 'no',
                'severe_headache' => $assessment['severe_headache'] ?? 'no',
                'blumming_vission' => $assessment['blurring_of_vission'] ?? 'no',
                'water_discharge' => $assessment['watery_discharge'] ??  'no',
                'severe_vomitting' => $assessment['severe_vomiting'] ??  'no',
                'hx_smoking' => $assessment['hx_smoking'] ??  'no',
                'alchohol_drinker' => $assessment['alcohol_drinker'] ?? 'no',
                'drug_intake' => $assessment['drug_intake'] ?? 'no'
            ]);
           

            return response()->json(['message' => 'Patient Info is Updated'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function updatePregnancyPlan(Request $request, $id){
        try{
            $pregnancyPlanRecord = pregnancy_plans::findOrFail($id);

            $data = $request->validate([
                'midwife_name' => 'sometimes|nullable|string',
                'place_of_pregnancy' => 'sometimes|nullable|string',
                'authorized_by_philhealth' => 'sometimes|nullable|string',
                'cost_of_pregnancy' => 'sometimes|nullable|numeric',
                'payment_method' => 'sometimes|nullable|string',
                'transportation_mode' => 'sometimes|nullable|string',
                'accompany_person_to_hospital' => 'sometimes|nullable|string',
                'accompany_through_pregnancy' => 'sometimes|nullable|string',
                'care_person' => 'sometimes|nullable|string',
                'emergency_person_name' => 'sometimes|nullable|string',
                'emergency_person_residency' => 'sometimes|nullable|string',
                'emergency_person_contact_number' => 'sometimes|nullable|string',
                'signature_image' => 'sometimes|image|mimes:jpg,jpeg,png|max:5120',
                'donor_names' => 'sometimes|nullable|array'
            ]);

            // update

            $pregnancyPlanRecord->update([
                'midwife_name' => $data['midwife_name'] ?? $pregnancyPlanRecord->midwife_name,
                'place_of_pregnancy' => $data['place_of_pregnancy'] ?? $pregnancyPlanRecord->place_of_pregnancy,
                'authorized_by_philhealth' => $data['authorized_by_philhealth'] ?? $pregnancyPlanRecord->authorized_by_philhealth,
                'cost_of_pregnancy' => $data['cost_of_pregnancy'] ?? $pregnancyPlanRecord->cost_of_pregnancy,
                'payment_method' => $data['payment_method'] ?? $pregnancyPlanRecord->payment_method,
                'transportation_mode' => $data['transportation_mode'] ?? $pregnancyPlanRecord->transportation_mode,
                'accompany_person_to_hospital' => $data['accompany_person_to_hospital'] ?? $pregnancyPlanRecord->accompany_person_to_hospital,
                'accompany_through_pregnancy' =>  $data['accompany_through_pregnancy'] ?? $pregnancyPlanRecord->accompany_through_pregnancy,
                'care_person' => $data['care_person'] ?? $pregnancyPlanRecord->care_person,
                'emergency_person_name' => $data['emergency_person_name'] ?? $pregnancyPlanRecord->emergency_person_name,
                'emergency_person_residency' => $data['emergency_person_residency'] ?? $pregnancyPlanRecord->emergency_person_residency,
                'emergency_person_contact_number' => $data['emergency_person_contact_number'] ?? $pregnancyPlanRecord->emergency_person_contact_number,
                'signature' => $data['signature_image'] ?? $pregnancyPlanRecord->signature,
                'type_of_record' => 'Pregnancy Plan Record'
            ]);
            // delete all the donor name as update logic
            $donorNamesRecord = donor_names::where('pregnancy_plan_id', $pregnancyPlanRecord->id)->delete();

            if (isset($data['donor_names']) && !empty($data['donor_names'])) {
                foreach ($data['donor_names'] as $name) {
                    if (!empty(trim($name))) { // Also check if name is not empty
                        donor_names::create([
                            'pregnancy_plan_id' => $id,
                            'donor_name' => trim($name)
                        ]);
                    }
                }
            };
            
            return response()->json(['message' => 'Updating Patient Pregnancy Plan Successfully'], 200);
        }catch(ValidationException $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
        
    }
    public function viewPrenatalDetail($id)
    {
        try {
            $prenatalRecord = medical_record_cases::with([
                'patient',
                'prenatal_case_record.pregnancy_history_questions',
                'prenatal_medical_record'
            ])->where('id', $id)->firstOrFail();

            $prenatalCaseRecord = prenatal_case_records::with('pregnancy_history_questions')
                ->where('medical_record_case_id', $prenatalRecord->id)
                ->firstOrFail();

            $healthWorker = staff::where('user_id', $prenatalCaseRecord->health_worker_id)
                ->firstOrFail();

            return response()->json([
                'prenatalRecord'      => $prenatalRecord,
                'prenatalCaseRecord'  => $prenatalCaseRecord,
                'healthWorker'        => $healthWorker,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Record not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadPregnancyCheckup(Request $request,$id){
        try{
            $request->validate(
                [
                    // Required fields
                    'check_up_full_name'       => 'required|string|max:255',

                    // Optional fields
                    'health_worker_id'         => 'nullable|exists:staff,user_id',
                    'check_up_time'            => 'nullable|date_format:H:i',
                    'check_up_blood_pressure'  => 'nullable|string|max:20',
                    'check_up_temperature'     => 'nullable|numeric|min:20|max:45',
                    'check_up_pulse_rate'      => 'nullable|integer|min:30|max:250',
                    'check_up_respiratory_rate' => 'nullable|integer|min:5|max:80',
                    'check_up_height'          => 'nullable|numeric|min:0',
                    'check_up_weight'          => 'nullable|numeric|min:0',

                    // Symptom questions (all optional but strings)
                    'abdomen_question'                 => 'nullable|string|max:255',
                    'abdomen_question_remarks'         => 'nullable|string|max:500',
                    'vaginal_question'                 => 'nullable|string|max:255',
                    'vaginal_question_remarks'         => 'nullable|string|max:500',
                    'headache_question'                => 'nullable|string|max:255',
                    'headache_question_remarks'        => 'nullable|string|max:500',
                    'blurry_vission_question'          => 'nullable|string|max:255',
                    'blurry_vission_question_remarks'  => 'nullable|string|max:500',
                    'urination_question'               => 'nullable|string|max:255',
                    'urination_question_remarks'       => 'nullable|string|max:500',
                    'baby_move_question'               => 'nullable|string|max:255',
                    'baby_move_question_remarks'       => 'nullable|string|max:500',
                    'decreased_baby_movement'          => 'nullable|string|max:255',
                    'decreased_baby_movement_remarks'  => 'nullable|string|max:500',
                    'other_symptoms_question'          => 'nullable|string|max:255',
                    'other_symptoms_question_remarks'  => 'nullable|string|max:500',

                    // Final remarks
                    'overall_remarks'           => 'nullable|string|max:1000',
                ],
                [],// this is empty because we didn't customize the error message for each field we just change the name 
                [
                    // Replace attribute names for cleaner error messages
                    'check_up_time'             => 'Time',
                    'check_up_blood_pressure'   => 'Blood Pressure',
                    'check_up_temperature'      => 'Temperature',
                    'check_up_pulse_rate'       => 'Pulse Rate',
                    'check_up_respiratory_rate' => 'Respiratory Rate',
                    'check_up_height'           => 'Height',
                    'check_up_weight'           => 'Weight',

                    'abdomen_question'                 => 'Abdomen Question',
                    'abdomen_question_remarks'         => 'Abdomen Remarks',
                    'vaginal_question'                 => 'Vaginal Question',
                    'vaginal_question_remarks'         => 'Vaginal Remarks',
                    'headache_question'                => 'Headache Question',
                    'headache_question_remarks'        => 'Headache Remarks',
                    'blurry_vission_question'          => 'Blurry Vision Question',
                    'blurry_vission_question_remarks'  => 'Blurry Vision Remarks',
                    'urination_question'               => 'Urination Question',
                    'urination_question_remarks'       => 'Urination Remarks',
                    'baby_move_question'               => 'Baby Movement Question',
                    'baby_move_question_remarks'       => 'Baby Movement Remarks',
                    'decreased_baby_movement'          => 'Decreased Baby Movement',
                    'decreased_baby_movement_remarks'  => 'Decreased Baby Movement Remarks',
                    'other_symptoms_question'          => 'Other Symptoms Question',
                    'other_symptoms_question_remarks'  => 'Other Symptoms Remarks',

                    'overall_remarks'           => 'Overall Remarks',
                ]
            );

            // data insertion
            $prenatalCheckup = pregnancy_checkups::create([
                'medical_record_case_id'    => $id,
                'patient_name'              => $request->check_up_full_name,
                'health_worker_id'          => $request->health_worker_id,
                'check_up_time'             => $request->check_up_time,
                'check_up_blood_pressure'   => $request->check_up_blood_pressure,
                'check_up_temperature'      => $request->check_up_temperature,
                'check_up_pulse_rate'       => $request->check_up_pulse_rate,
                'check_up_respiratory_rate' => $request->check_up_respiratory_rate,
                'check_up_height'           => $request->check_up_height,
                'check_up_weight'           => $request->check_up_weight,

                'abdomen_question'                 => $request->abdomen_question,
                'abdomen_question_remarks'         => $request->abdomen_question_remarks,
                'vaginal_question'                 => $request->vaginal_question,
                'vaginal_question_remarks'         => $request->vaginal_question_remarks,
                'headache_question'                => $request->headache_question,
                'headache_question_remarks'        => $request->headache_question_remarks,
                'blurry_vission_question'          => $request->blurry_vission_question,
                'blurry_vission_question_remarks'  => $request->blurry_vission_question_remarks,
                'urination_question'               => $request->urination_question,
                'urination_question_remarks'       => $request->urination_question_remarks,
                'baby_move_question'               => $request->baby_move_question,
                'baby_move_question_remarks'       => $request->baby_move_question_remarks,
                'decreased_baby_movement'          => $request->decreased_baby_movement,
                'decreased_baby_movement_remarks'  => $request->decreased_baby_movement_remarks,
                'other_symptoms_question'          => $request->other_symptoms_question,
                'other_symptoms_question_remarks'  => $request->other_symptoms_question_remarks,

                'overall_remarks' => $request->overall_remarks,
                'status'=> 'Done'
            ]);
            return response()-> json(['message'=> 'Prenatal Check-up info is added successfully'],201);
        }catch(ValidationException $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        };

    }

    public function viewCheckUpInfo($id){
        try{
            $pregnancy_checkup = pregnancy_checkups::findOrFail($id);
            $healthWorker = staff::where('user_id', $pregnancy_checkup->health_worker_id)->firstOrFail();
            return response()-> json([
                'pregnancy_checkup_info'=> $pregnancy_checkup,
                'healthWorker'=> $healthWorker
            ]);
        }catch(\Exception $e){
            return response()-> json([
                'error'=> $e->getMessage()
            ]);
        }
       


    }
    public function updatePregnancyCheckUp(Request $request, $id){
        try {
            $checkUp = pregnancy_checkups::findOrFail($id);
            $request->validate(
                [
                    // Required fields
                    'edit_check_up_full_name'       => 'required|string|max:255',

                    // Optional fields
                    'edit_health_worker_id'         => 'nullable|exists:staff,user_id',
                    'edit_check_up_time'            => 'nullable|date_format:H:i:s',
                    'edit_check_up_blood_pressure'  => 'nullable|string|max:20',
                    'edit_check_up_temperature'     => 'nullable|numeric|min:20|max:45',
                    'edit_check_up_pulse_rate'      => 'nullable|integer|min:30|max:250',
                    'edit_check_up_respiratory_rate' => 'nullable|integer|min:5|max:80',
                    'edit_check_up_height'          => 'nullable|numeric|min:0',
                    'edit_check_up_weight'          => 'nullable|numeric|min:0',

                    // Symptom questions (all optional but strings)
                    'edit_abdomen_question'                 => 'nullable|string|max:255',
                    'edit_abdomen_question_remarks'         => 'nullable|string|max:500',
                    'edit_vaginal_question'                 => 'nullable|string|max:255',
                    'edit_vaginal_question_remarks'         => 'nullable|string|max:500',
                    'edit_headache_question'                => 'nullable|string|max:255',
                    'edit_headache_question_remarks'        => 'nullable|string|max:500',
                    'edit_swelling_question'                => 'nullable|string|max:255',
                    'edit_swelling_question_remarks'        => 'nullable|string|max:500',
                    'edit_blurry_vission_question'          => 'nullable|string|max:255',
                    'edit_blurry_vission_question_remarks'  => 'nullable|string|max:500',
                    'edit_urination_question'               => 'nullable|string|max:255',
                    'edit_urination_question_remarks'       => 'nullable|string|max:500',
                    'edit_baby_move_question'               => 'nullable|string|max:255',
                    'edit_baby_move_question_remarks'       => 'nullable|string|max:500',
                    'edit_decreased_baby_movement'          => 'nullable|string|max:255',
                    'edit_decreased_baby_movement_remarks'  => 'nullable|string|max:500',
                    'edit_other_symptoms_question'          => 'nullable|string|max:255',
                    'edit_other_symptoms_question_remarks'  => 'nullable|string|max:500',

                    // Final remarks
                    'edit_overall_remarks'           => 'nullable|string|max:1000',
                ],
                [], // this is empty because we didn't customize the error message for each field we just change the name 
                [
                    // Replace attribute names for cleaner error messages
                    'edit_check_up_time'             => 'Time',
                    'edit_check_up_blood_pressure'   => 'Blood Pressure',
                    'edit_check_up_temperature'      => 'Temperature',
                    'edit_check_up_pulse_rate'       => 'Pulse Rate',
                    'edit_check_up_respiratory_rate' => 'Respiratory Rate',
                    'edit_check_up_height'           => 'Height',
                    'edit_check_up_weight'           => 'Weight',

                    'edit_abdomen_question'                 => 'Abdomen Question',
                    'edit_abdomen_question_remarks'         => 'Abdomen Remarks',
                    'edit_vaginal_question'                 => 'Vaginal Question',
                    'edit_vaginal_question_remarks'         => 'Vaginal Remarks',
                    'edit_headache_question'                => 'Headache Question',
                    'edit_headache_question_remarks'        => 'Headache Remarks',
                    'edit_blurry_vission_question'          => 'Blurry Vision Question',
                    'edit_blurry_vission_question_remarks'  => 'Blurry Vision Remarks',
                    'edit_urination_question'               => 'Urination Question',
                    'edit_urination_question_remarks'       => 'Urination Remarks',
                    'edit_baby_move_question'               => 'Baby Movement Question',
                    'edit_baby_move_question_remarks'       => 'Baby Movement Remarks',
                    'edit_decreased_baby_movement'          => 'Decreased Baby Movement',
                    'edit_decreased_baby_movement_remarks'  => 'Decreased Baby Movement Remarks',
                    'edit_other_symptoms_question'          => 'Other Symptoms Question',
                    'edit_other_symptoms_question_remarks'  => 'Other Symptoms Remarks',

                    'edit_overall_remarks'           => 'Overall Remarks',
                ]
            );

            // data insertion
            $checkUp->update([
                'patient_name'              => $request->edit_check_up_full_name?? $checkUp->patient_name,
                'health_worker_id'          => $request->edit_health_worker_id?? $checkUp->health_worker_id,
                'check_up_time'             => $request->edit_check_up_time ?? $checkUp->check_up_time,
                'check_up_blood_pressure'   => $request->edit_check_up_blood_pressure ?? $checkUp->check_up_blood_pressure,
                'check_up_temperature'      => $request->edit_check_up_temperature ?? $checkUp->check_up_temperature,
                'check_up_pulse_rate'       => $request->edit_check_up_pulse_rate ?? $checkUp->check_up_pulse_rate,
                'check_up_respiratory_rate' => $request->edit_check_up_respiratory_rate?? $checkUp->check_up_respiratory_rate,
                'check_up_height'           => $request->edit_check_up_height?? $checkUp->check_up_height,
                'check_up_weight'               => $request->edit_check_up_weight ?? $checkUp->check_up_weight,

                'abdomen_question'              => $request->edit_abdomen_question ?? $checkUp->abdomen_question,
                'abdomen_question_remarks'      => $request->edit_abdomen_question_remarks ?? $checkUp->abdomen_question_remarks,

                'vaginal_question'              => $request->edit_vaginal_question ?? $checkUp->vaginal_question,
                'vaginal_question_remarks'      => $request->edit_vaginal_question_remarks ?? $checkUp->vaginal_question_remarks,

                'headache_question'             => $request->edit_headache_question ?? $checkUp->headache_question,
                'headache_question_remarks'     => $request->edit_headache_question_remarks ?? $checkUp->headache_question_remarks,

                'swelling_question'             => $request->edit_swelling_question ?? $checkUp->swelling_question,
                'swelling_question_remarks'     => $request->edit_swelling_question_remarks ?? $checkUp->swelling_question_remarks,

                'blurry_vission_question'       => $request->edit_blurry_vission_question ?? $checkUp->blurry_vission_question,
                'blurry_vission_question_remarks' => $request->edit_blurry_vission_question_remarks ?? $checkUp->blurry_vission_question_remarks,

                'urination_question'            => $request->edit_urination_question ?? $checkUp->urination_question,
                'urination_question_remarks'    => $request->edit_urination_question_remarks ?? $checkUp->urination_question_remarks,

                'baby_move_question'            => $request->edit_baby_move_question ?? $checkUp->baby_move_question,
                'baby_move_question_remarks'    => $request->edit_baby_move_question_remarks ?? $checkUp->baby_move_question_remarks,

                'decreased_baby_movement'       => $request->edit_decreased_baby_movement ?? $checkUp->decreased_baby_movement,
                'decreased_baby_movement_remarks' => $request->edit_decreased_baby_movement_remarks ?? $checkUp->decreased_baby_movement_remarks,

                'other_symptoms_question'       => $request->edit_other_symptoms_question ?? $checkUp->other_symptoms_question,
                'other_symptoms_question_remarks' => $request->edit_other_symptoms_question_remarks ?? $checkUp->other_symptoms_question_remarks,

                'overall_remarks'               => $request->edit_overall_remarks ?? $checkUp->overall_remarks,
                'status' => 'Done'
            ]);
            return response()->json(['message' => 'Prenatal Check-up info is updated successfully'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        };
    }
}
