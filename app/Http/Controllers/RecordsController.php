<?php

namespace App\Http\Controllers;

use App\Models\medical_record_cases;
use App\Models\patient_addresses;
use App\Models\patients;
use App\Models\vaccination_case_records;
use App\Models\vaccination_medical_records;
use App\Models\vaccineAdministered;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RecordsController extends Controller
{
    public function allRecords(){
        return view('records.allRecords.allRecord', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecordPatientDetails(){
        return view('records.allRecords.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecord_editPatientDetails(){
        return view('records.allRecords.editPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function allRecordsCase(){
        return view('records.allRecords.patientCase', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function viewVaccinationRecord(){
        return view('records.allRecords.viewVaccinationRecord', ['isActive' => true, 'page' => 'RECORD']);
    }

    // vaccination
    public function vaccinationRecord(){
        $vaccinationRecord = medical_record_cases::with('patient')->where('type_of_case','vaccination')-> get();
        return view('records.vaccination.vaccination', ['isActive' => true, 'page' => 'RECORD', 'vaccinationRecord'=> $vaccinationRecord]);
    }
    public function viewDetails($id){
        $info = patients::with('medical_record_case.vaccination_medical_record')->findOrFail($id);
        $address = patient_addresses::where('patient_id',$id)->firstorFail();
        $fullAddress = $address->house_number.','. $address->street.', '.$address-> purok . ', '.$address-> barangay.', '.$address->city.', ' .$address->province;
        return view('records.vaccination.patientDetails', ['isActive' => true, 'page' => 'RECORD', 'info' => $info,'fullAddress'=> $fullAddress,'address'=> $address]);
    }
    public function vaccinationEditDetails($id){
        $info = patients::with('medical_record_case.vaccination_medical_record')->findOrFail($id);
        $address = patient_addresses::where('patient_id', $id)->firstorFail();
        $street = $address->house_number . ($address->street ? ', ' . $address->street : '');
        return view('records.vaccination.editPatientDetails',['isActive' => true, 'page' => 'RECORD', 'info' => $info, 'address' => $address, 'street'=>$street]);
    }
    public function vaccinationUpdateDetails(Request $request,$id){
        try {
            $patient = patients::findorFail($id);
            $medical_record_case = medical_record_cases::where('patient_id',$id)->firstOrFail();
            $vaccination_medical_record = vaccination_medical_records::where('medical_record_case_id',$medical_record_case->id);
            $patient_address = patient_addresses::where('patient_id',$id)-> firstOrFail();
            $data = $request -> validate([
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
            $patient-> update([
                'first_name'=> $data['first_name']?? $patient->first_name,
                'last_name'=>  $data['last_name']?? $patient->last_name,
                'middle_initial'=> $data['middle_initial']?? $patient->middle_initial,
                'date_of_birth'=> $data['date_of_birth']?? $patient-> date_of_birth,
                'place_of_birth'=> $data['place_of_birth']?? $patient-> place_of_birth,
                'age'=> $data['age']?? $patient->age,
                'sex'=> $data['sex']?? $patient-> sex,
                'contact_number'=> $data['contact_number']?? $patient->contact_number,
                'nationality'=> $data['nationality']?? $patient->nationality,

            ]);
            $vaccination_medical_record -> update([
                'date_of_registration' => $data['date_of_registration']?? $medical_record_case-> date_of_registration,
                'mother_name'=> $data['mother_name']?? $medical_record_case->mother_name,
                'father_name'=> $data['father_name']?? $medical_record_case->father_name,
                'birth_height' => $data['vaccination_height']?? $medical_record_case->birth_height,
                'birth_weight' => $data['vaccination_weight'] ?? $medical_record_case->birth_weight,
            ]);
            $blk_n_street = explode(',', $data['street']);
            $patient_address->update([
                'house_number' => $blk_n_street[0] ?? $data['blk_n_street'],
                'street' => $blk_n_street[1] ?? null,
                'purok' => $data['brgy']?? $patient_address-> purok
            ]);

            return response()-> json([
                'message'=> 'Patient information is successfully updated'
            ]);
        } catch (ValidationException $e) {
            return response()-> json([
                'message' => 'Patient information is not successfully updated',
                'error' => $e-> errors()
            ]);
        }
    }
    public function vaccinationCase($id){
        $medical_record_case = medical_record_cases::where('patient_id',$id)-> where('type_of_case','vaccination')-> firstOrFail();
        $vaccination_case_record = vaccination_case_records::where('medical_record_case_id',$medical_record_case->id)->get();
        // dd($vaccination_case_record);

       
        // $vaccine_administered = vaccineAdministered::where('vaccination_case_record_id', $vaccination_case_record->id)->get();
        // dd($medical_record_case, $vaccination_case_record, $vaccine_administered);
        return view('records.vaccination.patientCase', ['isActive' => true, 'page' => 'RECORD','vaccination_case_record'=> $vaccination_case_record]);
    }
    public function vaccinationViewCase($id){

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


    // prenatal
    public function prenatalRecord(){
        return view('records.prenatal.prenatal', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function viewPrenatalDetail(){
        return view('records.prenatal.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
  
    public function editPrenatalDetail(){
        return view('records.prenatal.editPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function editPrenatalCase(){
        return view('records.prenatal.prenatalPatientCase', ['isActive' => true, 'page' => 'RECORD']);
    }

    // senior Citizen

    public function seniorCitizenRecord(){
        return view('records.seniorCitizen.seniorCitizen', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function seniorCitizenDetail()
    {
        return view('records.seniorCitizen.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function editSeniorCitizenDetail()
    {
        return view('records.seniorCitizen.editPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function editSeniorCitizenCase()
    {
        return view('records.seniorCitizen.seniorCitizenPatientCase', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function viewSeniorCitizenCaseInfo(){
        return view('records.seniorCitizen.viewCase', ['isActive' => true, 'page' => 'RECORD']);
    }

    // -------------------------- family planning
    public function familyPlanningRecord(){
        return view('records.familyPlanning.familyPlanning',['isActive' => true, 'page' => 'RECORD']);
    }
    public function familyPlanningDetail()
    {
        return view('records.familyPlanning.viewPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function editFamilyPlanningDetail(){
        return view('records.familyPlanning.editPatientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function viewFamilyPlanningCase(){
        return view('records.familyPlanning.familyPlanningCase', ['isActive' => true, 'page' => 'RECORD']);
    }

    // --------------------------- tb dots ----------------------------------------
    public function tb_dotsRecord(){
        return view('records.tb-dots.tb-dots', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function tb_dotsDetail()
    {
        return view('records.tb-dots.viewtb_dotsDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function editTb_dotsDetail()
    {
        return view('records.tb-dots.editTb_dotsDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function viewTb_dotsCase()
    {
        return view('records.tb-dots.tb_dotsCase', ['isActive' => true, 'page' => 'RECORD']);
    }
}
