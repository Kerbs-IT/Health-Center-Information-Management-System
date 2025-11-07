<?php

namespace App\Http\Controllers;

use App\Models\family_planning_case_records;
use App\Models\family_planning_side_b_records;
use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\prenatal_case_records;
use App\Models\senior_citizen_case_records;
use App\Models\staff;
use App\Models\tb_dots_case_records;
use App\Models\tb_dots_check_ups;
use App\Models\vaccination_case_records;
use App\Models\vaccination_medical_records;
use App\Models\vaccineAdministered;
use App\Models\vaccines;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Type\Integer;

class RecordsController extends Controller
{
    public function allRecords()
    {
        return view('records.allRecords.allRecord', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecordPatientDetails()
    {
        return view('records.allRecords.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecord_editPatientDetails()
    {
        return view('records.allRecords.editPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecordsCase()
    {
        return view('records.allRecords.patientCase', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function viewVaccinationRecord()
    {
        return view('records.allRecords.viewVaccinationRecord', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function updateVacciationCaseRecord(Request $request, $id)
    {

        try{

            $data = $request->validate([
                'update_handled_by' => 'required',
                'date_of_vaccination' => 'required',
                'time_of_vaccination' => 'sometimes',
                'selected_vaccine' => 'required',
                'case_record_id' => 'required',
                'dose' => 'required',
                'remarks' => 'sometimes'
            ]);

            // delete the existing vaccine administed first, then create a new record of the vaccines
            $currentlyAdministedVaccine = vaccineAdministered::where('vaccination_case_record_id',$data['case_record_id'])->delete();
            

            // this if for compiling the selected vaccines
            // get the vaccine types
            $vaccines = explode(',', $data['selected_vaccine']);
            $selectedVaccinesArray = [];

            foreach ($vaccines as $key => $vaccineId) {
                $vaccineText = vaccines::find($vaccineId);

                $selectedVaccinesArray[] = $vaccineText->vaccine_acronym;
            }

            $selectedVaccines = implode(', ', $selectedVaccinesArray);

            // GET THE MEDICAL RECORD CASE THAT WE WANT TO UPDATE
            $vaccination_case_record = vaccination_case_records::findOrFail($data['case_record_id']);
            // UPDATE THE DATA
            $vaccination_case_record -> update([
                'health_worker_id' => $data['update_handled_by']?? $vaccination_case_record->health_worker_id,
                'date_of_vaccination' => $data['date_of_vaccination']?? $vaccination_case_record-> date_of_vaccination,
                'time'=> $data['time_of_vaccination']?? $vaccination_case_record-> time,
                'vaccine_type' => $selectedVaccines?? $vaccination_case_record-> vaccine_type,
                'dose_number'=> $data['dose']?? $vaccination_case_record->dose,
                'remarks'=> $data['remarks']?? $vaccination_case_record-> remarks
            ]);

            // UPLOAD THE NEW SET OF VACCINES
            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);

                $vaccineAdministered = vaccineAdministered::create([
                    'vaccination_case_record_id' => $data['case_record_id'],
                    'vaccine_type' => $vaccine->type_of_vaccine,
                    'dose_number' => $data['dose'] ?? null,
                    'vaccine_id' => $vaccineId ?? null
                ]);
            }

            return response()-> json([
                'message' => 'updating information successfully'
            ]);

        }catch(ValidationException $e){
            return response()-> json([
                'errors' => $e->errors()
            ]);
        }
        


    }

    // vaccination
    public function vaccinationRecord()
    {
        $vaccinationRecord = medical_record_cases::with('patient')->where('type_of_case', 'vaccination')->get();
        return view('records.vaccination.vaccination', ['isActive' => true, 'page' => 'RECORD', 'vaccinationRecord' => $vaccinationRecord]);
    }
    public function viewDetails($id)
    {
        $info = patients::with('medical_record_case.vaccination_medical_record')->findOrFail($id);
        $address = patient_addresses::where('patient_id', $id)->firstorFail();
        $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;
        return view('records.vaccination.patientDetails', ['isActive' => true, 'page' => 'RECORD', 'info' => $info, 'fullAddress' => $fullAddress, 'address' => $address]);
    }
    public function vaccinationEditDetails($id)
    {
        $info = patients::with('medical_record_case.vaccination_medical_record')->findOrFail($id);
        $address = patient_addresses::where('patient_id', $id)->firstorFail();
        $street = $address->house_number . ($address->street ? ', ' . $address->street : '');
        return view('records.vaccination.editPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'info' => $info, 'address' => $address, 'street' => $street]);
    }
    public function vaccinationUpdateDetails(Request $request, $id)
    {
        try {
            $patient = patients::findorFail($id);
            $medical_record_case = medical_record_cases::where('patient_id', $id)->firstOrFail();
            $vaccination_medical_record = vaccination_medical_records::where('medical_record_case_id', $medical_record_case->id);
            $patient_address = patient_addresses::where('patient_id', $id)->firstOrFail();
            // update the full name of vaccination case record
            $vaccination_case_record = vaccination_case_records::where('medical_record_case_id',$medical_record_case ->id)->get();
            
            $data = $request->validate([
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
                'street' => 'required',
                'brgy' => 'required',
                'vaccination_height' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'vaccination_weight' => ['required', 'regex:/^\d+(\.\d{1,2})?$/']
            ]);
            $patient->update([
                'first_name' => $data['first_name'] ?? $patient->first_name,
                'last_name' =>  $data['last_name'] ?? $patient->last_name,
                'middle_initial' => $data['middle_initial'] ?? $patient->middle_initial,
                'full_name' => trim(
                    ($data['first_name'] ?? $patient->first_name) . ' ' .
                    ($data['middle_initial'] ?? $patient->middle_initial) . ' ' .
                    ($data['last_name'] ?? $patient->last_name)
                ),
                'date_of_birth' => $data['date_of_birth'] ?? $patient->date_of_birth,
                'place_of_birth' => $data['place_of_birth'] ?? $patient->place_of_birth,
                'age' => $data['age'] ?? $patient->age,
                'sex' => $data['sex'] ?? $patient->sex,
                'contact_number' => $data['contact_number'] ?? $patient->contact_number,
                'nationality' => $data['nationality'] ?? $patient->nationality,

            ]);
            // update each record associate to patient vaccination case the vaccination case record
            foreach ($vaccination_case_record as $record) {
                $record->update([
                    'patient_name' => trim(
                        ($data['first_name'] ?? $patient->first_name) . ' ' .
                            ($data['middle_initial'] ?? $patient->middle_initial) . ' ' .
                            ($data['last_name'] ?? $patient->last_name)
                    )
                ]);
            }
            
            $vaccination_medical_record->update([
                'date_of_registration' => $data['date_of_registration'] ?? $medical_record_case->date_of_registration,
                'mother_name' => $data['mother_name'] ?? $medical_record_case->mother_name,
                'father_name' => $data['father_name'] ?? $medical_record_case->father_name,
                'birth_height' => $data['vaccination_height'] ?? $medical_record_case->birth_height,
                'birth_weight' => $data['vaccination_weight'] ?? $medical_record_case->birth_weight,
            ]);
            $blk_n_street = explode(',', $data['street']);
            $patient_address->update([
                'house_number' => $blk_n_street[0] ?? $data['blk_n_street'],
                'street' => $blk_n_street[1] ?? null,
                'purok' => $data['brgy'] ?? $patient_address->purok
            ]);

            return response()->json([
                'message' => 'Patient information is successfully updated'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Patient information is not successfully updated',
                'error' => $e->errors()
            ]);
        }
    }
    public function vaccinationCase($id)
    {
        $medical_record_case = medical_record_cases::with('patient')->where('patient_id', $id)->where('type_of_case', 'vaccination')->firstOrFail();
        $vaccination_case_record = vaccination_case_records::where('medical_record_case_id', $medical_record_case->id)->get();
        // dd($vaccination_case_record);


        // $vaccine_administered = vaccineAdministered::where('vaccination_case_record_id', $vaccination_case_record[0]->id)->get();
        // dd($medical_record_case, $vaccination_case_record, $vaccine_administered);
        return view('records.vaccination.patientCase', ['isActive' => true, 'page' => 'RECORD', 'vaccination_case_record' => $vaccination_case_record, 'medical_record_case'=> $medical_record_case]);
    }
    public function vaccinationViewCase($id)
    {

        try {
            $vaccinationCase = vaccination_case_records::findOrFail($id);

            $vaccineAdministered = vaccineAdministered::where(
                'vaccination_case_record_id',
                $vaccinationCase->id
            )->get();

            return response()->json([
                'vaccinationCase'     => $vaccinationCase,
                'vaccineAdministered' => $vaccineAdministered
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Vaccination case not found.'
            ], 404);
        } catch (\Exception $e) {
            // For any other kind of exception
            return response()->json([
                'error' => 'An unexpected error occurred.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function vaccinationDelete($id)
    {
        try {
            $patient = patients::where('id', $id)->firstOrFail();

            if ($patient->delete()) {
                return response()->json(['message' => 'Patient Record has been deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete patient record'], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => "There's an error deleting patient record",
                'error' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Unexpected error occurred",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // add vaccination case record
    public function addVaccinationCaseRecord(Request $request, $id){

        try{
            $data = $request->validate([
                'add_patient_full_name' => 'required',
                'add_handled_by' => 'required',
                'add_date_of_vaccination' => 'required',
                'add_time_of_vaccination' => 'sometimes|nullable|string',
                'selected_vaccine_type' => 'required',
                'add_record_dose' => 'required',
                'add_case_remarks' => 'sometimes|nullable|string'
            ]);

            // get the vaccine types
            $vaccines = explode(',', $data['selected_vaccine_type']);
            $selectedVaccinesArray = [];

            foreach ($vaccines as $key => $vaccineId) {
                $vaccineText = vaccines::find($vaccineId);

                $selectedVaccinesArray[] = $vaccineText->vaccine_acronym;
            }

            $selectedVaccines = implode(', ', $selectedVaccinesArray);

            $newCaseRecord = vaccination_case_records::create([
                'medical_record_case_id' => $id,
                'patient_name' => $data['add_patient_full_name'],
                'date_of_vaccination' => $data['add_date_of_vaccination'],
                'time' => $data['add_time_of_vaccination'] ?? null,
                'vaccine_type' => $selectedVaccines,
                'dose_number' => (int) $data['add_record_dose'],
                'remarks' => $data['add_case_remarks'] ?? null,
                'type_of_record' => 'Case Record',
                'health_worker_id' => (int) $data['add_handled_by']
            ]);

            // id of medical case record
            $medicalCaseRecordId = $newCaseRecord->id;

            foreach ($vaccines as $vaccineId) {
                $vaccine = vaccines::find($vaccineId);

                $vaccineAdministered = vaccineAdministered::create([
                    'vaccination_case_record_id' => $medicalCaseRecordId,
                    'vaccine_type' => $vaccine->type_of_vaccine,
                    'dose_number' => $data['add_record_dose'] ?? null,
                    'vaccine_id' => $vaccineId ?? null
                ]);
            }

            return response()->json(['message' => 'Patient has been added'], 201);
        }catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
       
    }
    public function deleteVaccinationCase($id){
        try {
            $vaccination_case_record = vaccination_case_records::findOrFail($id);
            $vaccination_case_record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vaccination case deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete vaccination case.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    // prenatal
    public function prenatalRecord()
    {
        $prenatalRecord = medical_record_cases::with('patient')->where('type_of_case', 'prenatal')->get();
        return view('records.prenatal.prenatal', ['isActive' => true, 'page' => 'RECORD','prenatalRecord'=> $prenatalRecord]);
    }
    public function viewPrenatalDetail($id)
    {
        $prenatalRecord = medical_record_cases::with(['patient', 'prenatal_case_record.pregnancy_history_questions', 'prenatal_medical_record',])->where('id',$id)->firstOrFail();
        // $caseInfo = prenatal_case_records::with('pregnancy_history_questions')->where('medical_record_case_id',$id)->firstOrFail();
        $prenatalCaseRecord = prenatal_case_records::with('pregnancy_history_questions')->where('medical_record_case_id', $prenatalRecord->id)->firstOrFail();
        // address
        $address = patient_addresses::where('patient_id', $prenatalRecord->patient->id)->firstorFail();
        $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;
        return view('records.prenatal.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD','prenatalRecord'=>$prenatalRecord, 'prenatalCaseRecord' => $prenatalCaseRecord,'fullAddress'=> $fullAddress ]);
    }

    public function editPrenatalDetail($id)
    {
        $prenatalRecord = medical_record_cases::with(['patient', 'prenatal_medical_record'])->where('id', $id)->firstOrFail();
        $caseRecord = prenatal_case_records::where('medical_record_case_id', $id)->firstOrFail();
       
        $address = patient_addresses::where('patient_id',$prenatalRecord->patient->id)-> firstOrFail();
        return view('records.prenatal.editPatientDetails', ['isActive' => true, 'page' => 'RECORD','prenatalRecord'=> $prenatalRecord,'address'=> $address, 'caseRecord' => $caseRecord]);
    }
    public function prenatalCase($caseId)
    {
        $prenatalCaseRecords = medical_record_cases::with('prenatal_case_record.pregnancy_timeline_records', 'pregnancy_plan', 'pregnancy_checkup')->where('id', $caseId)->firstOrFail();
        return view('records.prenatal.prenatalPatientCase', ['isActive' => true, 'page' => 'RECORD','prenatalCaseRecords'=>$prenatalCaseRecords]);
    }

    // senior Citizen

    public function seniorCitizenRecord()
    {
        $seniorCitizenRecords = medical_record_cases::with('patient')->where('type_of_case','senior-citizen')->get();
        return view('records.seniorCitizen.seniorCitizen', ['isActive' => true, 'page' => 'RECORD', 'seniorCitizenRecords'=> $seniorCitizenRecords]);
    }
    public function seniorCitizenDetail($id)
    {
        $seniorCitizenRecord = medical_record_cases::with(['patient', 'senior_citizen_medical_record'])->findOrFail($id);
        // address
        $address = patient_addresses::where('patient_id', $seniorCitizenRecord->patient->id)->firstorFail();
        $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;
        return view('records.seniorCitizen.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD','seniorCitizenRecord'=> $seniorCitizenRecord, 'fullAddress' => $fullAddress]);
    }
    public function editSeniorCitizenDetail($id)
    {
        $seniorCitizenRecord = medical_record_cases::with(['patient', 'senior_citizen_medical_record'])->findOrFail($id);
        // address
        $address = patient_addresses::where('patient_id', $seniorCitizenRecord->patient->id)->firstorFail();
        return view('records.seniorCitizen.editPatientDetails', ['isActive' => true, 'page' => 'RECORD', 'seniorCitizenRecord' => $seniorCitizenRecord, 'address' => $address]);
    }
    public function viewSeniorCitizenCases($id)
    {
        $seniorCaseRecords = senior_citizen_case_records::where('medical_record_case_id',$id)->get();
        $patientRecord = medical_record_cases::with('patient', 'senior_citizen_medical_record')->findOrFail($id);
        return view('records.seniorCitizen.seniorCitizenPatientCase', ['isActive' => true, 'page' => 'RECORD','seniorCaseRecords'=>  $seniorCaseRecords, 'patient_name'=> $patientRecord ->patient->full_name, 'healthWorkerId' => $patientRecord->senior_citizen_medical_record-> health_worker_id, 'medicalRecordId' => $id  ]);
    }
    public function viewSeniorCitizenCaseInfo()
    {
        return view('records.seniorCitizen.viewCase', ['isActive' => true, 'page' => 'RECORD']);
    }

    // -------------------------- family planning
    public function familyPlanningRecord()
    {   
        $familyPlanning = medical_record_cases::with('patient')->where('type_of_case','family-planning')->get();
        return view('records.familyPlanning.familyPlanning', ['isActive' => true, 'page' => 'RECORD', 'familyPlanningRecords' => $familyPlanning]);
    }
    public function familyPlanningDetail($id)
    {
        $familyPlanningRecords = medical_record_cases::with(['patient', 'family_planning_case_record','family_planning_medical_record'])->findOrFail($id);
        return view('records.familyPlanning.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD','familyPlanningRecord'=> $familyPlanningRecords]);
    }
    public function editFamilyPlanningDetail($id)
    {
        $familyPlanningRecords = medical_record_cases::with(['patient', 'family_planning_case_record', 'family_planning_medical_record'])->findOrFail($id);
        $address = patient_addresses::where('patient_id', $familyPlanningRecords->patient->id)->firstOrFail();
        return view('records.familyPlanning.editPatientDetails', ['isActive' => true, 'page' => 'RECORD' ,'familyPlanningRecord' => $familyPlanningRecords,'address'=> $address]);
    }
    public function viewFamilyPlanningCase($id)
    {
        $familyPlanningCases = family_planning_case_records::where('medical_record_case_id',$id)->get();
        $familyPlanningSideB = family_planning_side_b_records::where('medical_record_case_id', $id)->get();
        $patientInfo = medical_record_cases::with(['family_planning_medical_record','patient'])->findOrFail($id);
        return view('records.familyPlanning.familyPlanningCase', ['isActive' => true, 'page' => 'RECORD','familyPlanningCases'=> $familyPlanningCases,'patientInfo'=> $patientInfo, 'familyPlanningSideB'=> $familyPlanningSideB ]);
    }

    // --------------------------- tb dots ----------------------------------------
    public function tb_dotsRecord()
    {
        $tbRecords = medical_record_cases::with('patient')->where('type_of_case', 'tb-dots')->get();
        return view('records.tb-dots.tb-dots', ['isActive' => true, 'page' => 'RECORD', 'tbRecords'=> $tbRecords]);
    }
    public function tb_dotsDetail($id)
    {   
        try{
            $tbRecord = medical_record_cases::with(['patient', 'tb_dots_medical_record'])->findOrFail($id);

            $address = patient_addresses::where('patient_id', $tbRecord->patient->id)->firstorFail();
            $fullAddress = $address->house_number . ',' . $address->street . ', ' . $address->purok . ', ' . $address->barangay . ', ' . $address->city . ', ' . $address->province;
            return view('records.tb-dots.viewtb_dotsDetails', ['isActive' => true, 'page' => 'RECORD','tbDotsRecord' => $tbRecord, 'fullAddress' => $fullAddress]);
        }catch(\Exception $e){
            return  view('records.tb-dots.viewtb_dotsDetails', ['isActive' => true, 'page' => 'RECORD', 'error' => $e->getMessage()]);
        }
        
    }
    public function editTb_dotsDetail($id)
    {   
        try{
            $tbRecord = medical_record_cases::with(['patient', 'tb_dots_medical_record'])->findOrFail($id);

            $address = patient_addresses::where('patient_id', $tbRecord->patient->id)->firstorFail();
            return view('records.tb-dots.editTb_dotsDetails', ['isActive' => true, 'page' => 'RECORD', 'tbDotsRecord' => $tbRecord, 'address' => $address]);
        }catch(\Exception $e){
            return view('records.tb-dots.editTb_dotsDetails', ['isActive' => true, 'page' => 'RECORD', 'error'=> $e->getMessage()]);
        }
       
    }
    public function viewTb_dotsCase($id)
    {
        $tbDotsCaseRecords = tb_dots_case_records::where('medical_record_case_id', $id)->get();
        $patientRecord = medical_record_cases::with('patient', 'tb_dots_medical_record')->findOrFail($id);

        // check up 

        $checkUpRecords = tb_dots_check_ups::where('medical_record_case_id', $id)->get();
        return view('records.tb-dots.tb_dotsCase', ['isActive' => true, 'page' => 'RECORD', 'tbDotsRecords' =>  $tbDotsCaseRecords,'checkUpRecords' => $checkUpRecords, 'patient_name' => $patientRecord->patient->full_name, 'healthWorkerId' => $patientRecord->tb_dots_medical_record->health_worker_id, 'medicalRecordId' => $id]);
    }
}
