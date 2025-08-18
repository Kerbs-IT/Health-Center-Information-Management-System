<?php

use App\Http\Controllers\addPatientController;
use App\Http\Controllers\addressController;
use App\Http\Controllers\authController;
use App\Http\Controllers\brgyUnit;
use App\Http\Controllers\brgyUnitController;
use App\Http\Controllers\colorPalleteController;
use App\Http\Controllers\forgotPassController;
use App\Http\Controllers\healthWorkerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\manageInterfaceController;
use App\Http\Controllers\manageUserController;
use App\Http\Controllers\masterListController;
use App\Http\Controllers\nurseDashboardController;
use App\Http\Controllers\nurseDeptController;
use App\Http\Controllers\patientController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\vaccineController;
use App\Models\color_pallete;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layout.app');
});

Route::get('/auth/login',[authController::class, 'login']) -> name('login');
Route::get('/change-pass',function (){
    return view('auth.changePass',['isActive' => true,]);
}) -> name('change-pass');

// logout
Route::post('/logout',[LoginController::class,'logout']) -> name('logout');

Route::get('/auth/register',[authController::class, 'register']) -> name('register');

// show the brgy_Unit
Route::get('/showBrgyUnit',[brgyUnitController::class,'showBrgyUnit']) -> name('showBrgyUnit');
// show the available department
Route::get('/nurseDept',[nurseDeptController::class,'show']) -> name('nurseDept');

// create new user

Route::post('/create',[authController::class,'store']) -> name('user.store');

//login
Route::post('/login',[LoginController::class,'authenticate']) -> name('auth.login');

// dashboard route
Route::get('/dashboard/admin',function (){
    return view('dashboard.admin');
}) -> name('dashboard.admin');

Route::get('/dashboard/nurse',[nurseDashboardController::class,'dashboard']) -> name('dashboard.nurse');

Route::get('/dashboard/staff',function (){
    return view('dashboard.staff',['isActive' => true,'page' => 'DASHBOARD']);
}) -> name('dashboard.staff');
// patient dashboard\
Route::get('/dashboard/patient',[patientController::class, 'dashboard']) -> name('dashboard.patient');

// menu bar blade
Route::get('/menuBar',function (){
    return view('layout.menuBar');
}) -> name('menubar');

Route::get('/profile',function (){
    return view('pages.profile', ['isActive' => true, 'page' => 'RECORD']);
}) -> name('page.profile');


// address route
Route::get('/get-regions', [addressController::class, 'getRegions']);
Route::get('/get-provinces/{regionCode}',[addressController::class,'getProvinces']);
Route::get('/get-cities/{provinceCode}',[addressController::class,'getCities']);
Route::get('/get-brgy/{cityCode}',[addressController::class,'getBrgy']);

// update profile

Route::post('/update-profile',[authController::class,'update']) -> name('user.update-profile');

// forgot password route
Route::get('/forgot-pass', function (){
    return view('forgot_password.forgot_pass');
}) -> name('forgot.pass');

// forgot pass verify email
Route::post('/forgot-pass/verify-email',[forgotPassController::class, 'verify']) -> name('forgot.pass.verify.email');

// recovery route
Route::get('/forgot-pass/recovery-questions', function () {
    return view('forgot_password.questions');
}) -> name('forgot.pass.questions');

// verify the recovery question answer
Route::post('/forgot-pass/verify-answer', [forgotPassController::class, 'verify_answer']) -> name('forgot.pass.verify.answer');
// change password
Route::post('/forgot-pass/change-password/process',[forgotPassController::class,'change_pass']) -> name('change.pass');

Route::get('/forgot-pass/change-password', function () {
    return view('forgot_password.change_pass');
}) -> name('/forgot.pass.change');

// ------------------------------------Health workers LIST---------------------------------------------------------

Route::get('/Dashboard/Health-workers', [healthWorkerController::class, 'dashboard']) -> name('health.worker');

// ------------------------------------DELETE n Update HEALTH WORKER RECORD-----------------------------------------
Route::delete('/health-worker/{id}', [healthWorkerController::class, 'destroy'])->name('health-worker.destroy');
Route::post('/health-worker/get-info/{id}', [healthWorkerController::class,'getInfo']) -> name('heath-worker.get-info');
Route::put('/health-worker/update/{id}',[healthWorkerController::class,'update']) -> name('health-worker.update');

// --------------------------------------------ADD PATIENT ROUTE SECTION-----------------------------------------------------
Route::get('/add-patients', [addPatientController::class, 'dashboard']) -> name('add-patient');

// --------------------------------------------VACCINATION RECORDS --------------------------------------------------------------------
Route::get('/patient-record/vaccination', [RecordsController::class, 'vaccinationRecord']) -> name('record.vaccination');
Route::get('/patient-record/vaccination/view-details/{id}',[RecordsController::class,'viewDetails']) -> name('view.details');
Route::get('/patient-record/vaccination/edit-details/{id}',[RecordsController::class,'vaccinationEditDetails']) -> name('record.vaccination.edit');
Route::get('/patient-record/vaccination/case/{id}',[RecordsController::class,'vaccinationCase']) -> name('record.vaccination.case');
Route::put('/patient-record/update/{id}',[RecordsController::class, 'vaccinationUpdateDetails'])-> name('record.vaccination.update');
Route::delete('/patient-record/vaccination/delete/{id}',[RecordsController::class, 'vaccinationDelete'])-> name('record.vaccination.delete');
Route::get('/vaccination-case/record/{id}',[RecordsController::class, 'vaccinationViewCase'])-> name('view.case.info');
// ADD VACCINATION CASE RECORD
Route::post('/add-vaccination-case/{id}',[RecordsController::class, 'addVaccinationCaseRecord']);
// ---------------- DELETE VACCINATION CASE ----------------------
Route::delete('/delete-vaccination-case/{id}',[RecordsController::class,'deleteVaccinationCase']);
// --- UPDATE CASE DETAIL
Route::put('/vaccine/update/case-record/{id}',[RecordsController::class,'updateVacciationCaseRecord'])-> name('update.case.record');
// ------- get vaccines
Route::get('/vaccines',[vaccineController::class, 'getVaccines']);
// -------------------------------------------- PRENATAL RECORD----------------------------------------------------------------
Route::get('/patient-record/prenatal/view-records',[RecordsController::class,'prenatalRecord']) -> name('records.prenatal');
Route::get('/patient-record/prenatal/view-details/id',[RecordsController::class, 'viewPrenatalDetail']) -> name('record.view.prenatal');
Route::get('/patient-record/prenatal/edit-details/id',[RecordsController::class, 'editPrenatalDetail']) -> name('record.prenatal.edit');
Route::get('/patient-record/prenatal/view-case/id', [RecordsController::class, 'editPrenatalCase']) -> name('record.prenatal.case');
// --------------------------------------------- SENIOR CITIZEN RECORD ------------------------------------------------------
Route::get('/patient-record/senior-citizen/view-records', [RecordsController::class, 'seniorCitizenRecord']) -> name('record.senior.citizen');
Route::get('/patient-record/senior-citizen/view-detail/id', [RecordsController::class, 'seniorCitizenDetail']) -> name('record.senior.citizen.view');
Route::get('/patient-record/senior-citizen/edit-details/id', [RecordsController::class, 'editSeniorCitizenDetail'])->name('record.senior.citizen.edit');
Route::get('/patient-record/senior-citizen/view-case/id', [RecordsController::class, 'editSeniorCitizenCase'])->name('record.senior.citizen.case');
// Route::get('/patient-record/senior-citizen/view-case-info/id', [RecordsController::class, 'viewSeniorCitizenCaseInfo']) -> name('record.case.view.Senior.citizen.info');

// --------------------------------------------- FAMILY PLANNING RECORD ----------------------------------------------------------------
Route::get('/patient-record/family-planning/view-records', [RecordsController::class, 'familyPlanningRecord']) -> name('record.family.planning');
Route::get('/patient-record/family-planning/view-detail/id', [RecordsController::class, 'familyPlanningDetail'])->name('record.family.planning.view');
Route::get('/patient-record/family-planning/edit-details/id', [RecordsController::class, 'editFamilyPlanningDetail'])->name('record.family.planning.edit');
Route::get('/patient-record/family-planning/view-case/id', [RecordsController::class, 'viewFamilyPlanningCase'])->name('record.family.planning.case');

// --------------------------------------------- TB DOTS -------------------------------------------------------------------------------
Route::get('/patient-record/tb-dots/view-records', [RecordsController::class, 'tb_dotsRecord']) -> name('record.tb-dots');
Route::get('/patient-record/tb-dots/view-detail/id', [RecordsController::class, 'tb_dotsDetail'])->name('record.tb-dots.view');
Route::get('/patient-record/tb-dots/edit-details/id', [RecordsController::class, 'editTb_dotsDetail'])->name('record.tb-dots.edit');
Route::get('/patient-record/tb-dots/view-case/id', [RecordsController::class, 'viewTb_dotsCase'])->name('record.tb-dots.case');

// -------------------------------------------- MASTER LIST ----------------------------------------------------------------------------
Route::get('/masterlist/vaccination',[masterListController::class, 'viewVaccinationMasterList']) -> name('masterlist.vaccination');
Route::get('/masterlist/women-of-reproductive-age', [masterListController::class, 'viewWRAMasterList'])->name('masterlist.wra');

// ------------------------------------------- Manage User -----------------------------------------------------
Route::get('/manager-users', [manageUserController::class,'viewUsers']) -> name('manager.users');

// ------------------------------------------- Manage Interface -----------------------------------------------
Route::get('/manage-interface',[manageInterfaceController::class,'manageInterface']) -> name('manage.interface');

// ------------------------------------------- Patient Account Record --------------------------------------------------------------
Route::get('/user-account/medical-record', [patientController::class,'medicalRecord']) -> name('view.medical.record');

// -------------------------------------------- ALL RECORDS ---------------------------------------------------
Route::get('/record/all-records',[RecordsController::class, 'allRecords']) -> name('all.record');
Route::get('/patient-record/all-record/view-detail/id', [RecordsController::class, 'allRecordPatientDetails'])-> name('record.allRecord.view');
Route::get('/patient-record/all-record/edit-case/id', [RecordsController::class, 'allRecordsCase'])->name('record.allRecords.case');
Route::get('/patient-record/all-record/edit-details/id',[RecordsController::class, 'allRecord_editPatientDetails']) -> name('allRecord.edit');

Route::get('/patient-record/all-record/case/id/vaccination', [RecordsController::class, 'viewVaccinationRecord'])->name('allRecord.vaccination.record');

Route::put('/update/status/{id}/{decision}', [authController::class, 'updateStatus']) -> name('update.status');


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
Route::post('/add-health-worker-account',[healthWorkerController::class, 'addHealthWorker']) -> name('managerHealthWorker.add-account');

// manager patient account
Route::post('/add-patient-account',[manageUserController::class, 'store']) -> name('manageUser.addPatientAccount');
Route::delete('/delete-patient-account/{id}',[manageUserController::class, 'remove']) -> name('manageUser.delete');
Route::post('/patient-account/get-patient-info/{id}',[manageUserController::class, 'info']) -> name('manageUser.info');
Route::put('/update-patient-account-information/{id}',[manageUserController::class,'updateInfo']) -> name('manageUser.update');

// patient profile

Route::post('/patient-profile-edit/{id}', [patientController::class, 'info']) -> name('patient-profile');
Route::put('/patient-profile/update/{id}',[patientController::class, 'updateInfo']) -> name('patient-profile.update');

// MAnage interface color pallete
Route::get('/color-pallete', [colorPalleteController::class, 'getInfo']) -> name('color-pallete');
Route::put('/update-color-pallete',[colorPalleteController::class,'updateInfo']) -> name('update-color-pallete');

// ADD VACCINATION PATIENT
Route::post('/add-patient/vaccination',[addPatientController::class,'addVaccinationPatient'])-> name('add-vaccination-patient');

// health worker list 

Route::get('/health-worker-list',[healthWorkerController::class,'healthWorkerList']);

?>