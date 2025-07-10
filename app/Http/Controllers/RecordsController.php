<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return view('records.vaccination.vaccination', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function viewDetails(){

        return view('records.vaccination.patientDetails', ['isActive' => true, 'page' => 'RECORD']);
    }
    public function vaccinationEditDetails(){

        return view('records.vaccination.editPatientDetails',['isActive' => true, 'page' => 'RECORD']);
    }
    public function vaccinationCase(){
        return view('records.vaccination.patientCase', ['isActive' => true, 'page' => 'RECORD']);
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
