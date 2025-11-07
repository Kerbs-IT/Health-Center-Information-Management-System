<?php

use App\Http\Controllers\addPatientController;
use App\Http\Controllers\addressController;
use App\Http\Controllers\authController;
use App\Http\Controllers\brgyUnit;
use App\Http\Controllers\brgyUnitController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\colorPalleteController;
use App\Http\Controllers\FamilyPlanningController;
use App\Http\Controllers\forgotPassController;
use App\Http\Controllers\healthWorkerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\manageInterfaceController;
use App\Http\Controllers\manageUserController;
use App\Http\Controllers\masterListController;
use App\Http\Controllers\nurseDashboardController;
use App\Http\Controllers\nurseDeptController;
use App\Http\Controllers\patientController;
use App\Http\Controllers\PrenatalController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\SeniorCitizenController;
use App\Http\Controllers\TbDotsController;
use App\Http\Controllers\vaccineController;
use App\Models\color_pallete;
use Illuminate\Support\Facades\Route;
use LDAP\Result;

Route::get('/', function () {
    return view('layout.app');
});

Route::get('/auth/login', [authController::class, 'login'])->name('login');
Route::get('/change-pass', function () {
    return view('auth.changePass', ['isActive' => true,]);
})->name('change-pass');

// logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/auth/register', [authController::class, 'register'])->name('register');

// show the brgy_Unit
Route::get('/showBrgyUnit', [brgyUnitController::class, 'showBrgyUnit'])->name('showBrgyUnit');
// show the available department
Route::get('/nurseDept', [nurseDeptController::class, 'show'])->name('nurseDept');

// create new user

Route::post('/create', [authController::class, 'store'])->name('user.store');

//login
Route::post('/login', [LoginController::class, 'authenticate'])->name('auth.login');

// dashboard route
Route::get('/dashboard/admin', function () {
    return view('dashboard.admin');
})->name('dashboard.admin');

Route::get('/dashboard/nurse', [nurseDashboardController::class, 'dashboard'])->name('dashboard.nurse');

Route::get('/dashboard/staff', function () {
    return view('dashboard.staff', ['isActive' => true, 'page' => 'DASHBOARD']);
})->name('dashboard.staff');
// patient dashboard\
Route::get('/dashboard/patient', [patientController::class, 'dashboard'])->name('dashboard.patient');

// menu bar blade
Route::get('/menuBar', function () {
    return view('layout.menuBar');
})->name('menubar');

Route::get('/profile', function () {
    return view('pages.profile', ['isActive' => true, 'page' => 'RECORD']);
})->name('page.profile');


// address route
Route::get('/get-regions', [addressController::class, 'getRegions']);
Route::get('/get-provinces/{regionCode}', [addressController::class, 'getProvinces']);
Route::get('/get-cities/{provinceCode}', [addressController::class, 'getCities']);
Route::get('/get-brgy/{cityCode}', [addressController::class, 'getBrgy']);

// update profile

Route::post('/update-profile', [authController::class, 'update'])->name('user.update-profile');

// forgot password route
Route::get('/forgot-pass', function () {
    return view('forgot_password.forgot_pass');
})->name('forgot.pass');

// forgot pass verify email
Route::post('/forgot-pass/verify-email', [forgotPassController::class, 'verify'])->name('forgot.pass.verify.email');

// recovery route
Route::get('/forgot-pass/recovery-questions', function () {
    return view('forgot_password.questions');
})->name('forgot.pass.questions');

// verify the recovery question answer
Route::post('/forgot-pass/verify-answer', [forgotPassController::class, 'verify_answer'])->name('forgot.pass.verify.answer');
// change password
Route::post('/forgot-pass/change-password/process', [forgotPassController::class, 'change_pass'])->name('change.pass');

Route::get('/forgot-pass/change-password', function () {
    return view('forgot_password.change_pass');
})->name('/forgot.pass.change');

// ------------------------------------Health workers LIST---------------------------------------------------------

Route::get('/Dashboard/Health-workers', [healthWorkerController::class, 'dashboard'])->name('health.worker');

// ------------------------------------DELETE n Update HEALTH WORKER RECORD-----------------------------------------
Route::delete('/health-worker/{id}', [healthWorkerController::class, 'destroy'])->name('health-worker.destroy');
Route::post('/health-worker/get-info/{id}', [healthWorkerController::class, 'getInfo'])->name('heath-worker.get-info');
Route::put('/health-worker/update/{id}', [healthWorkerController::class, 'update'])->name('health-worker.update');

// --------------------------------------------ADD PATIENT ROUTE SECTION-----------------------------------------------------
Route::get('/add-patients', [addPatientController::class, 'dashboard'])->name('add-patient');

// --------------------------------------------VACCINATION RECORDS --------------------------------------------------------------------
Route::get('/patient-record/vaccination', [RecordsController::class, 'vaccinationRecord'])->name('record.vaccination');
Route::get('/patient-record/vaccination/view-details/{id}', [RecordsController::class, 'viewDetails'])->name('view.details');
Route::get('/patient-record/vaccination/edit-details/{id}', [RecordsController::class, 'vaccinationEditDetails'])->name('record.vaccination.edit');
Route::get('/patient-record/vaccination/case/{id}', [RecordsController::class, 'vaccinationCase'])->name('record.vaccination.case');
Route::put('/patient-record/update/{id}', [RecordsController::class, 'vaccinationUpdateDetails'])->name('record.vaccination.update');
Route::delete('/patient-record/vaccination/delete/{id}', [RecordsController::class, 'vaccinationDelete'])->name('record.vaccination.delete');
Route::get('/vaccination-case/record/{id}', [RecordsController::class, 'vaccinationViewCase'])->name('view.case.info');
// ADD VACCINATION CASE RECORD
Route::post('/add-vaccination-case/{id}', [RecordsController::class, 'addVaccinationCaseRecord']);
// ---------------- DELETE VACCINATION CASE ----------------------
Route::delete('/delete-vaccination-case/{id}', [RecordsController::class, 'deleteVaccinationCase']);
// --- UPDATE CASE DETAIL
Route::put('/vaccine/update/case-record/{id}', [RecordsController::class, 'updateVacciationCaseRecord'])->name('update.case.record');
// ------- get vaccines
Route::get('/vaccines', [vaccineController::class, 'getVaccines']);
// -------------------------------------------- PRENATAL RECORD----------------------------------------------------------------
Route::get('/patient-record/prenatal/view-records', [RecordsController::class, 'prenatalRecord'])->name('records.prenatal');
Route::get('/patient-record/prenatal/view-details/{id}', [RecordsController::class, 'viewPrenatalDetail'])->name('record.view.prenatal');
Route::get('/patient-record/prenatal/edit-details/{id}', [RecordsController::class, 'editPrenatalDetail'])->name('record.prenatal.edit');
Route::get('/patient-record/prenatal/view-case/{id}', [RecordsController::class, 'prenatalCase'])->name('record.prenatal.case');
Route::post('/add-prenatal-patient', [PrenatalController::class, 'addPatient']);
Route::put('/update/prenatal-patient-details/{id}', [PrenatalController::class, 'updateDetails']);
Route::get('/view-case/case-record/{typeOfRecord}/{id}', [CaseController::class, 'viewCase']); // fetches the case information
Route::get('/view-prenatal/pregnancy-plan/{id}', [PrenatalController::class, 'viewPregnancyPlan']);
Route::put('/update/pregnancy-plan-record/{id}', [PrenatalController::class, 'updatePregnancyPlan']); // route for updating the pregnancy plan record of the patient
Route::get('/patient-record/view-details/{id}', [PrenatalController::class, 'viewPrenatalDetail']);
// update the case information
Route::put('patient-record/update/prenatal-case/{id}', [PrenatalController::class, 'updateCase']);
Route::post('/prenatal/add-check-up-record/{id}', [PrenatalController::class, "uploadPregnancyCheckup"]);
// route for geting checkup info
Route::get('/prenatal/view-pregnancy-checkup-info/{id}', [PrenatalController::class, 'viewCheckupInfo']);
// update the checkup
Route::put('/update/prenatal-check-up/{id}', [PrenatalController::class, 'updatePregnancyCheckUp']);




// --------------------------------------------- SENIOR CITIZEN RECORD ------------------------------------------------------
Route::get('/patient-record/senior-citizen/view-records', [RecordsController::class, 'seniorCitizenRecord'])->name('record.senior.citizen');
Route::get('/patient-record/senior-citizen/view-detail/{id}', [RecordsController::class, 'seniorCitizenDetail'])->name('record.senior.citizen.view');
Route::get('/patient-record/senior-citizen/edit-details/{id}', [RecordsController::class, 'editSeniorCitizenDetail'])->name('record.senior.citizen.edit');
Route::get('/patient-record/senior-citizen/view-case/{id}', [RecordsController::class, 'viewSeniorCitizenCases'])->name('record.senior.citizen.case');
// Route::get('/patient-record/senior-citizen/view-case-info/id', [RecordsController::class, 'viewSeniorCitizenCaseInfo']) -> name('record.case.view.Senior.citizen.info');

// SENIOR CITIZEN ADD PATIENT
Route::post('/patient-record/add/senior-citizen-record', [SeniorCitizenController::class, "addPatient"]);
Route::put('/update/senior-citizen/details/{id}', [SeniorCitizenController::class, 'updateDetails']);
Route::get('/senior-citizen/case-details/{id}', [SeniorCitizenController::class, 'viewCaseDetails']);
Route::put('/patient-case/senior-citizen/{id}', [SeniorCitizenController::class, 'updateCase']); //update the case of senior citizen
Route::post('/patient-case/senior-citizen/new-case/{id}', [SeniorCitizenController::class, 'addCase']);

// --------------------------------------------- FAMILY PLANNING RECORD ----------------------------------------------------------------
Route::get('/patient-record/family-planning/view-records', [RecordsController::class, 'familyPlanningRecord'])->name('record.family.planning');
Route::get('/patient-record/family-planning/view-detail/{id}', [RecordsController::class, 'familyPlanningDetail'])->name('record.family.planning.view');
Route::get('/patient-record/family-planning/edit-details/{id}', [RecordsController::class, 'editFamilyPlanningDetail'])->name('record.family.planning.edit');
Route::get('/patient-record/family-planning/view-case/{id}', [RecordsController::class, 'viewFamilyPlanningCase'])->name('record.family.planning.case');

// add the family planning patient ecord
Route::post('/patient-record/family-planning/add-record',[FamilyPlanningController::class, 'addPatient']);
Route::put('/patient-record/family-planning/update-information/{id}', [FamilyPlanningController::class, 'editPatientDetails']); // update the patient details
Route::get('/patient-case/family-planning/viewCaseInfo/{id}', [FamilyPlanningController::class, 'viewCaseInfo']);
Route::put('/patient-case/family-planning/update-case-info/{id}',[FamilyPlanningController::class, 'updateCaseInfo']);
Route::post('/patient-record/family-planning/add/side-b-record', [FamilyPlanningController::class, 'addSideBrecord']);
Route::get('/patient-record/family-planning/view/side-b-record/{id}', [FamilyPlanningController::class, 'sideBrecords']);
// update side b
Route::put('/patient-record/family-planning/update/side-b-record/{id}',[FamilyPlanningController::class, 'updateSideBrecord']);
// add new side A if ever the record is deleted
Route::post('/patient-record/family-planning/add/side-a-record/{id}', [FamilyPlanningController::class, 'addSideAcaseInfo']);

// --------------------------------------------- TB DOTS -------------------------------------------------------------------------------
Route::get('/patient-record/tb-dots/view-records', [RecordsController::class, 'tb_dotsRecord'])->name('record.tb-dots');
Route::get('/patient-record/tb-dots/view-detail/{id}', [RecordsController::class, 'tb_dotsDetail'])->name('record.tb-dots.view');
Route::get('/patient-record/tb-dots/edit-details/{id}', [RecordsController::class, 'editTb_dotsDetail'])->name('record.tb-dots.edit');
Route::get('/patient-record/tb-dots/view-case/{id}', [RecordsController::class, 'viewTb_dotsCase'])->name('record.tb-dots.case');

// add patient
Route::post('/patient-record/add/tb-dots', [TbDotsController::class, 'addPatient']);
Route::post('/patient-record/tb-dots/update-details/{id}', [TbDotsController::class, 'updatePatientDetails']);
Route::get('/patient/tb-dots/get-case-info/{id}', [TbDotsController::class, 'caseInfo']);
Route::put('/patient-case/tb-dots/update/{id}', [TbDotsController::class, 'updateCase']);
Route::post('/patient-record/add/check-up/tb-dots/{id}', [TbDotsController::class, 'addPatientCheckUp']);
Route::get('/patient-record/view-check-up/tb-dots/{id}', [TbDotsController::class, 'viewPatientCheckUp']);
Route::put('/patient-record/tb-dots/update-checkup/{id}', [TbDotsController::class, 'updatePatientCheckUpInfo']);

// -------------------------------------------- MASTER LIST ----------------------------------------------------------------------------
Route::get('/masterlist/vaccination', [masterListController::class, 'viewVaccinationMasterList'])->name('masterlist.vaccination');
Route::get('/masterlist/women-of-reproductive-age', [masterListController::class, 'viewWRAMasterList'])->name('masterlist.wra');

// ------------------------------------------- Manage User -----------------------------------------------------
Route::get('/manager-users', [manageUserController::class, 'viewUsers'])->name('manager.users');

// ------------------------------------------- Manage Interface -----------------------------------------------
Route::get('/manage-interface', [manageInterfaceController::class, 'manageInterface'])->name('manage.interface');

// ------------------------------------------- Patient Account Record --------------------------------------------------------------
Route::get('/user-account/medical-record', [patientController::class, 'medicalRecord'])->name('view.medical.record');

// -------------------------------------------- ALL RECORDS ---------------------------------------------------
Route::get('/record/all-records', [RecordsController::class, 'allRecords'])->name('all.record');
Route::get('/patient-record/all-record/view-detail/id', [RecordsController::class, 'allRecordPatientDetails'])->name('record.allRecord.view');
Route::get('/patient-record/all-record/edit-case/id', [RecordsController::class, 'allRecordsCase'])->name('record.allRecords.case');
Route::get('/patient-record/all-record/edit-details/id', [RecordsController::class, 'allRecord_editPatientDetails'])->name('allRecord.edit');

Route::get('/patient-record/all-record/case/id/vaccination', [RecordsController::class, 'viewVaccinationRecord'])->name('allRecord.vaccination.record');

Route::put('/update/status/{id}/{decision}', [authController::class, 'updateStatus'])->name('update.status');


// ---------------------------- home page
// Route to homepage
Route::get('/', function () {
    return view('homepage');
})->name('homepage');

// Route to login page
// Route::get('/login', function () {
//     return view('login');
// })->name('login');

// // Route to Register page
// Route::get('/register', function () {
//     return view(view: 'register');
// })->name('register');


// Route to patient_register
Route::get('/patient-register', function () {
    return view('register_patient');
})->name('register_patient');

// manage health worker
Route::post('/add-health-worker-account', [healthWorkerController::class, 'addHealthWorker'])->name('managerHealthWorker.add-account');

// manager patient account
Route::post('/add-patient-account', [manageUserController::class, 'store'])->name('manageUser.addPatientAccount');
Route::delete('/delete-patient-account/{id}', [manageUserController::class, 'remove'])->name('manageUser.delete');
Route::post('/patient-account/get-patient-info/{id}', [manageUserController::class, 'info'])->name('manageUser.info');
Route::put('/update-patient-account-information/{id}', [manageUserController::class, 'updateInfo'])->name('manageUser.update');

// patient profile

Route::post('/patient-profile-edit/{id}', [patientController::class, 'info'])->name('patient-profile');
Route::put('/patient-profile/update/{id}', [patientController::class, 'updateInfo'])->name('patient-profile.update');

// MAnage interface color pallete
Route::get('/color-pallete', [colorPalleteController::class, 'getInfo'])->name('color-pallete');
Route::put('/update-color-pallete', [colorPalleteController::class, 'updateInfo'])->name('update-color-pallete');

// ADD VACCINATION PATIENT
Route::post('/add-patient/vaccination', [addPatientController::class, 'addVaccinationPatient'])->name('add-vaccination-patient');

// health worker list 

Route::get('/health-worker-list', [healthWorkerController::class, 'healthWorkerList']);
