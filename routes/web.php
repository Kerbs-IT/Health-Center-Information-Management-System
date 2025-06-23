<?php

use App\Http\Controllers\addPatientController;
use App\Http\Controllers\addressController;
use App\Http\Controllers\authController;
use App\Http\Controllers\brgyUnit;
use App\Http\Controllers\brgyUnitController;
use App\Http\Controllers\forgotPassController;
use App\Http\Controllers\healthWorkerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\nurseDashboardController;
use App\Http\Controllers\nurseDeptController;
use App\Http\Controllers\RecordsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/auth/login',[authController::class, 'login']) -> name('login');

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
    return view('dashboard.staff');
}) -> name('dashboard.staff');


// menu bar blade
Route::get('/menuBar',function (){
    return view('layout.menuBar');
}) -> name('menubar');

// profile route
Route::get('/profile',function (){
    return view('pages.profile', ['isActive' => true]);
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
Route::post('/heath-worker/update/{id}',[healthWorkerController::class,'update']) -> name('health-worker.update');

// --------------------------------------------ADD PATIENT ROUTE SECTION-----------------------------------------------------
Route::get('/add-patients', [addPatientController::class, 'dashboard']) -> name('add-patient');

// --------------------------------------------RECORDS --------------------------------------------------------------------
Route::get('/patient-record/vaccination', [RecordsController::class, 'vaccinationRecord']) -> name('record.vaccination');
Route::get('/patient-record/vaccination/view-details',[RecordsController::class,'viewDetails']) -> name('view.details');
Route::get('/patient-record/vaccination/edit-details/id',[RecordsController::class,'vaccinationEditDetails']) -> name('record.vaccination.edit');
Route::get('/patient-record/vaccination/case/id',[RecordsController::class,'vaccinationCase']) -> name('record.vaccination.case');
// -------------------------------------------- PRENATAL RECORD----------------------------------------------------------------
Route::get('/patient-record/prenatal/view-records',[RecordsController::class,'prenatalRecord']) -> name('records.prenatal');
Route::get('/patient-record/prenatal/view-details/id',[RecordsController::class, 'viewPrenatalDetail']) -> name('record.view.prenatal');
?>