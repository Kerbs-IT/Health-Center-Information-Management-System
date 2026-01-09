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
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HealthCenterDashboard;
use App\Http\Controllers\healthWorkerController;
use App\Http\Controllers\HeatMapController;
use App\Http\Controllers\ImmunizationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\manageInterfaceController;
use App\Http\Controllers\manageUserController;
use App\Http\Controllers\masterListController;
use App\Http\Controllers\nurseDashboardController;
use App\Http\Controllers\nurseDeptController;
use App\Http\Controllers\patientController;
use App\Http\Controllers\PatientList;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PrenatalController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SeniorCitizenController;
use App\Http\Controllers\TbDotsController;
use App\Http\Controllers\vaccineController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\wraMasterlistController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Models\color_pallete;
use Hamcrest\Core\Set;
use Illuminate\Support\Facades\Route;
use LDAP\Result;

// livewireCOmponent
use App\Livewire\CategoriesTable;
use App\Livewire\Medicines;
use App\Livewire\InventoryReport;
use App\Livewire\ManageMedicineRequests;
use App\Livewire\MedicineRequestComponent;
use App\Livewire\MedicineRequestLogComponent;

use Knp\Snappy\Pdf;

Route::get('/', function () {
    return view('layout.app');
});

Route::get('/color-pallete', [colorPalleteController::class, 'getInfo'])->name('color-pallete');
Route::put('/update-color-pallete', [colorPalleteController::class, 'updateInfo'])->name('update-color-pallete');
// MAnage interface color pallete
Route::get('/color-pallete', [colorPalleteController::class, 'getInfo'])->name('color-pallete');
Route::put('/update-color-pallete', [colorPalleteController::class, 'updateInfo'])->name('update-color-pallete');



Route::middleware(['redirect.loggedin'])->group(function () {
    // Show login form
    Route::get('/login', [AuthController::class, 'login'])->name('login');

    // Handle login form submit
    Route::post('/login', [LoginController::class, 'authenticate'])->name('auth.login');
});

Route::get('/auth/register', [authController::class, 'register'])->name('register');



Route::get('/change-pass', function () {
    return view('auth.changePass', ['isActive' => true,'page'=>'profile']);
})->name('change-pass');
Route::post('/change-pass/submit',[authController::class,'changePassword'])->name('submit-new-password');
// logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



// show the brgy_Unit
Route::get('/showBrgyUnit', [brgyUnitController::class, 'showBrgyUnit'])->name('showBrgyUnit');
// show the available department
Route::get('/nurseDept', [nurseDeptController::class, 'show'])->name('nurseDept');

// create new user

Route::post('/create', [authController::class, 'store'])->name('user.store');

//login
// =============== Nurse only routes
Route::middleware(['role:nurse'])->group(function(){
    Route::get('/dashboard/nurse', [nurseDashboardController::class, 'dashboard'])->name('dashboard.nurse');


    // ------------------------------------Health workers LIST---------------------------------------------------------

    Route::get('/Dashboard/Health-workers', [healthWorkerController::class, 'dashboard'])->name('health.worker');

    // ------------------------------------DELETE n Update HEALTH WORKER RECORD-----------------------------------------
    Route::post('/health-worker/{id}', [healthWorkerController::class, 'destroy'])->name('health-worker.destroy');
    Route::post('/health-worker/get-info/{id}', [healthWorkerController::class, 'getInfo'])->name('heath-worker.get-info');
    Route::put('/health-worker/update/{id}', [healthWorkerController::class, 'update'])->name('health-worker.update');

    // manage health worker
    Route::post('/add-health-worker-account', [healthWorkerController::class, 'addHealthWorker'])->name('managerHealthWorker.add-account');
    // MAnage interface color pallete

});

// =============== health worker only

Route::get('/dashboard/staff', function () {
    return view('dashboard.staff', ['isActive' => true, 'page' => 'DASHBOARD']);
})->name('dashboard.staff')->middleware(['role:staff']);

// =============== patients

// patient dashboard and check if verified
Route::middleware(['auth','verified','role:patient'])->group(function (){
    Route::get('/dashboard/patient', [patientController::class, 'dashboard'])->name('dashboard.patient');
    // ------------------------------------------- Patient Account Record --------------------------------------------------------------
    Route::get('/user-account/medical-record/{userId}', [patientController::class, 'renderData'])->name('view.medical.record');
});


// update profile

Route::post('/update-profile', [authController::class, 'update'])->name('user.update-profile');

// forgot password route
Route::get('/forgot-pass', function () {
    return view('auth.forgot-password');
})->name('forgot.pass');

Route::middleware(['role:nurse,staff,patient'])->group(function (){
    // get user info
    Route::get('/user/profile/{id}',[authController::class,'info']);
    // edit patient profile
    Route::post('/patient-profile-edit/{id}', [patientController::class, 'info'])->name('patient-profile');
    Route::put('/patient-profile/update/{id}', [patientController::class, 'updateInfo'])->name('patient-profile.update');

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

    // vaccination view
    Route::get('/vaccination-case/record/{id}', [RecordsController::class, 'vaccinationViewCase'])->name('view.case.info');
    // prenatal view
    Route::get('/view-case/case-record/{typeOfRecord}/{id}', [CaseController::class, 'viewCase']);
    Route::get('/view-prenatal/pregnancy-plan/{id}', [PrenatalController::class, 'viewPregnancyPlan']);
    Route::get('/prenatal/view-pregnancy-checkup-info/{id}', [PrenatalController::class, 'viewCheckupInfo']);//view checkup
    // family plan view
    Route::get('/patient-case/family-planning/viewCaseInfo/{id}', [FamilyPlanningController::class, 'viewCaseInfo']);
    Route::get('/patient-record/family-planning/view/side-b-record/{id}', [FamilyPlanningController::class, 'sideBrecords']);
    // senior citizen
    Route::get('/senior-citizen/case-details/{id}', [SeniorCitizenController::class, 'viewCaseDetails']);
    // tb-dots
    Route::get('/patient/tb-dots/get-case-info/{id}', [TbDotsController::class, 'caseInfo']);
    Route::get('/patient-record/view-check-up/tb-dots/{id}', [TbDotsController::class, 'viewPatientCheckUp']);


    Route::get('/profile', function () {
        return view('pages.profile', ['isActive' => true, 'page' => 'PROFILE']);
    })->name('page.profile');
});



// ================ Nurse and health worker
Route::middleware(['role:nurse,staff'])->group(function(){

    // dashboard route
    Route::get('/dashboard/admin', function () {
        return view('dashboard.admin');
    })->name('dashboard.admin');
    Route::get('/dashboard/info',[HealthCenterDashboard::class,'info']);


    // for the bar chart
    Route::get('/dashboard/monthly-stats', [HealthCenterDashboard::class, 'monthlyPatientStats']);
    // count per area
    Route::get('/dashboard/patient-count-per-area',[HealthCenterDashboard::class, 'patientCountPerArea']);
    // added today
    Route::get('/dashboard/today/added-patient', [HealthCenterDashboard::class, 'patientAddedToday']);


    // menu bar blade
    Route::get('/menuBar', function () {
        return view('layout.menuBar');
    })->name('menubar');



    // address route
    Route::get('/get-regions', [addressController::class, 'getRegions']);
    Route::get('/get-provinces/{regionCode}', [addressController::class, 'getProvinces']);
    Route::get('/get-cities/{provinceCode}', [addressController::class, 'getCities']);
    Route::get('/get-brgy/{cityCode}', [addressController::class, 'getBrgy']);




    // --------------------------------------------ADD PATIENT ROUTE SECTION-----------------------------------------------------
    Route::get('/add-patients', [addPatientController::class, 'dashboard'])->name('add-patient');

    // --------------------------------------------VACCINATION RECORDS --------------------------------------------------------------------
    Route::get('/patient-record/vaccination', [RecordsController::class, 'vaccinationRecord'])->name('record.vaccination');
    Route::get('/patient-record/vaccination/view-details/{id}', [RecordsController::class, 'viewDetails'])->name('view.details');
    Route::get('/patient-record/vaccination/edit-details/{id}', [RecordsController::class, 'vaccinationEditDetails'])->name('record.vaccination.edit');
    Route::get('/patient-record/vaccination/case/{id}', [RecordsController::class, 'vaccinationCase'])->name('record.vaccination.case');
    Route::put('/patient-record/update/{id}', [RecordsController::class, 'vaccinationUpdateDetails'])->name('record.vaccination.update');
    Route::post('/patient-record/{typeOfPatient}/delete/{id}', [RecordsController::class, 'deletePatient'])->name('record.vaccination.delete');

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
    Route::put('/update/pregnancy-plan-record/{id}', [PrenatalController::class, 'updatePregnancyPlan']); // route for updating the pregnancy plan record of the patient
    Route::get('/patient-record/view-details/{id}', [PrenatalController::class, 'viewPrenatalDetail']);
    // update the case information
    Route::put('patient-record/update/prenatal-case/{id}', [PrenatalController::class, 'updateCase']);
    Route::post('/prenatal/add-check-up-record/{id}', [PrenatalController::class, "uploadPregnancyCheckup"]);

    // update the checkup
    Route::put('/update/prenatal-check-up/{id}', [PrenatalController::class, 'updatePregnancyCheckUp']);

    // archived the record
    Route::post('prenatal/check-up/delete/{id}', [PrenatalController::class,'archive']);
    // add case record
    Route::post('/prenatal/add-prenatal-case-record',[PrenatalController::class,'addCase']);
    Route::post('/prenatal/add-pregnancy-plan/{medicalRecordCaseId}',[PrenatalController::class,'addPregnancyPlan']);

    // delete case record or pregnancy record
    Route::post('/patient-record/prenatal/{typeOfRecord}/{id}',[PrenatalController::class,'removeRecord']);




    // --------------------------------------------- SENIOR CITIZEN RECORD ------------------------------------------------------
    Route::get('/patient-record/senior-citizen/view-records', [RecordsController::class, 'seniorCitizenRecord'])->name('record.senior.citizen');
    Route::get('/patient-record/senior-citizen/view-detail/{id}', [RecordsController::class, 'seniorCitizenDetail'])->name('record.senior.citizen.view');
    Route::get('/patient-record/senior-citizen/edit-details/{id}', [RecordsController::class, 'editSeniorCitizenDetail'])->name('record.senior.citizen.edit');
    Route::get('/patient-record/senior-citizen/view-case/{id}', [RecordsController::class, 'viewSeniorCitizenCases'])->name('record.senior.citizen.case');
    // Route::get('/patient-record/senior-citizen/view-case-info/id', [RecordsController::class, 'viewSeniorCitizenCaseInfo']) -> name('record.case.view.Senior.citizen.info');
    Route::post('/patient-record/senior-citizen/case/delete/{id}',[SeniorCitizenController::class,'removeCase']);

    // SENIOR CITIZEN ADD PATIENT
    Route::post('/patient-record/add/senior-citizen-record', [SeniorCitizenController::class, "addPatient"]);
    Route::put('/update/senior-citizen/details/{id}', [SeniorCitizenController::class, 'updateDetails']);
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
    Route::put('/patient-case/family-planning/update-case-info/{id}',[FamilyPlanningController::class, 'updateCaseInfo']);
    Route::post('/patient-record/family-planning/add/side-b-record', [FamilyPlanningController::class, 'addSideBrecord']);
    // update side b
    Route::put('/patient-record/family-planning/update/side-b-record/{id}',[FamilyPlanningController::class, 'updateSideBrecord']);
    // add new side A if ever the record is deleted
    Route::post('/patient-record/family-planning/add/side-a-record/{id}', [FamilyPlanningController::class, 'addSideAcaseInfo']);
    // delete side b or side a records
    Route::post('/patient-record/family-planning/case-record/delete/{type_of_record}/{id}',[FamilyPlanningController::class,'removeRecord']);



    // --------------------------------------------- TB DOTS -------------------------------------------------------------------------------
    Route::get('/patient-record/tb-dots/view-records', [RecordsController::class, 'tb_dotsRecord'])->name('record.tb-dots');
    Route::get('/patient-record/tb-dots/view-detail/{id}', [RecordsController::class, 'tb_dotsDetail'])->name('record.tb-dots.view');
    Route::get('/patient-record/tb-dots/edit-details/{id}', [RecordsController::class, 'editTb_dotsDetail'])->name('record.tb-dots.edit');
    Route::get('/patient-record/tb-dots/view-case/{id}', [RecordsController::class, 'viewTb_dotsCase'])->name('record.tb-dots.case');

    // delete a checkup record
    Route::post('/patient-record/tb-dots/checkup/delete/{id}',[TbDotsController::class,'removeCheckup']);

    // add patient
    Route::post('/patient-record/add/tb-dots', [TbDotsController::class, 'addPatient']);
    Route::post('/patient-record/tb-dots/update-details/{id}', [TbDotsController::class, 'updatePatientDetails']);
    Route::put('/patient-case/tb-dots/update/{id}', [TbDotsController::class, 'updateCase']);
    Route::post('/patient-record/add/check-up/tb-dots/{id}', [TbDotsController::class, 'addPatientCheckUp']);
    Route::put('/patient-record/tb-dots/update-checkup/{id}', [TbDotsController::class, 'updatePatientCheckUpInfo']);
    // add case
    Route::post("/patient-record/tb-dots/add/case-record/{medicalRecordId}",[TbDotsController::class,'addCase']);
    // archive case
    Route::post("/patient-record/tb-dots/case-record/delete/{caseId}",[TbDotsController::class,'removeCase']);

    // -------------------------------------------- MASTER LIST ----------------------------------------------------------------------------
    Route::get('/masterlist/vaccination', [masterListController::class, 'viewVaccinationMasterList'])->name('masterlist.vaccination');
    Route::get('/masterlist/women-of-reproductive-age', [masterListController::class, 'viewWRAMasterList'])->name('masterlist.wra');

    // get the vaccination masterlist information
    Route::get('/masterist/{typeOfMasterlist}/{id}', [masterListController::class, 'getInfo']);
    Route::put('/masterlist/update/vaccination/{id}', [masterListController::class, 'updateVaccinationMasterlist']);
    // -----------------------------WRA MASTERLIST------------------------------------
    Route::put('/masterlist/update/wra/{id}', [wraMasterlistController::class,'update']);

    // ------------------------------------------- Manage User -----------------------------------------------------
    Route::get('/manager-users', [manageUserController::class, 'viewUsers'])->name('manager.users');

    // ------------------------------------------- Manage Interface -----------------------------------------------
    Route::get('/manage-interface', [manageInterfaceController::class, 'manageInterface'])->name('manage.interface');


    // -------------------------------------------- ALL RECORDS ---------------------------------------------------
    Route::get('/record/all-records', [RecordsController::class, 'allRecords'])->name('all.record');
    Route::get('/patient-record/all-record/view-detail/id', [RecordsController::class, 'allRecordPatientDetails'])->name('record.allRecord.view');
    Route::get('/patient-record/all-record/edit-case/id', [RecordsController::class, 'allRecordsCase'])->name('record.allRecords.case');
    Route::get('/patient-record/all-record/edit-details/id', [RecordsController::class, 'allRecord_editPatientDetails'])->name('allRecord.edit');

    Route::get('/patient-record/all-record/case/id/vaccination', [RecordsController::class, 'viewVaccinationRecord'])->name('allRecord.vaccination.record');

    Route::put('/update/status/{id}/{decision}', [authController::class, 'updateStatus'])->name('update.status');



    // ADD VACCINATION PATIENT
    Route::post('/add-patient/vaccination', [addPatientController::class, 'addVaccinationPatient'])->name('add-vaccination-patient');

    // health worker list

    Route::get('/health-worker-list', [healthWorkerController::class, 'healthWorkerList']);

    // ================== HEATMAP SECTION ===========================================
    Route::get('/test-geocoding', function () {
        $geocodingService = app(\App\Services\GeocodingService::class);

        // Test with a simple address in Imus, Cavite
        $result = $geocodingService->geocodeAddress([
            'house_number' => 'blk 9 lot 17',
            'street' => 'Greenbelt Street',
            'purok' => 'Gawad Kalinga',
            'city' => 'Trece Martires City',
            'province' => 'Cavite'
        ]);

        return response()->json($result);
    });

    Route::get('/health-map', [HeatMapController::class, 'index'])->name('health-map.index');
    Route::get('/api/heatmap-data', [HeatMapController::class, 'getHeatmapData'])->name('health-map.data');

    // patient profile


});
// ---------------------------- home page
// Route to homepage
Route::get('/', function () {
    return view('homepage');
})->name('homepage');



// Route to patient_register
Route::get('/patient-register', function () {
    return view('register_patient');
})->name('register_patient');



// manager patient account
Route::post('/add-patient-account', [manageUserController::class, 'store'])->name('manageUser.addPatientAccount');
Route::delete('/delete-patient-account/{id}', [manageUserController::class, 'remove'])->name('manageUser.delete');
Route::post('/patient-account/get-patient-info/{id}', [manageUserController::class, 'info'])->name('manageUser.info');
Route::put('/update-patient-account-information/{id}', [manageUserController::class, 'updateInfo'])->name('manageUser.update');

// EMAIL RESET PASSWORD
// Show form to request password reset
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

// Handle sending reset link
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

// Show form to reset password
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

// Handle password reset
Route::post('reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// addtional for homepage
// Homepage Routes:
Route::get('/about-full', function () {
    return view('about-full');
})->name('about.full');

Route::get('/Vaccine-Service', function () {
    return view('service-pages.vaccine-service-page');
})->name('vaccine-service');

Route::get('/prenatal-service', function () {
    return view('service-pages.prenatal-service-page');
})->name('prenatal-service');
Route::get('/familyPlanning-service', function () {
    return view('service-pages.familyPlanning-service-page');
})->name('familyPlanning-service');

Route::get('/senior-citizen-service', function () {
    return view('service-pages.SeniorCitizen-service-page');
})->name('seniorCitizen-service');
Route::get('/TB-Dots-service', function () {
    return view('service-pages.tbDots-service-page');
})->name('tbDots-service');

Route::get('/General-Consultation-Service', function () {
    return view('service-pages.general-consultation-service-page');
})->name('generalConsultation-service');



// health worker list

Route::get('/health-worker-list', [healthWorkerController::class, 'healthWorkerList']);


// Homepage Routes:
Route::get('/about-full', function () {
    return view('about-full');
})->name('about.full');

Route::get('/Vaccine-Service', function(){
    return view('service-pages.vaccine-service-page');
})->name('vaccine-service');

Route::get('/prenatal-service', function(){
    return view('service-pages.prenatal-service-page');
})->name('prenatal-service');
Route::get('/familyPlanning-service', function(){
    return view('service-pages.familyPlanning-service-page');
})->name('familyPlanning-service');

Route::get('/senior-citizen-service', function(){
    return view('service-pages.SeniorCitizen-service-page');
})->name('seniorCitizen-service');
Route::get('/TB-Dots-service', function(){
    return view('service-pages.tbDots-service-page');
})->name('tbDots-service');

Route::get('/General-Consultation-Service', function(){
    return view('service-pages.general-consultation-service-page');
})->name('generalConsultation-service');

Route::get( '/inventory', function(){
    return view('inventory_system.inventory');
}) -> name('inventory');

// Inventory Route
Route::get('inventory/categories', CategoriesTable::class)->name('categories');
Route::get('inventory/medicines', Medicines::class)->name('medicines');

Route::get('inventory/report',InventoryReport::class)->name('inventory-report');

// GENERATE THE RECORD
Route::get('/vaccination/records/pdf', [PdfController::class, 'generateVaccinationPdf'])
    ->name('vaccination.pdf');
Route::get('/prenatal/records/pdf', [PdfController::class, 'generatePrenatalPdf'])
    ->name('prenatal.pdf');
Route::get('/seior-citizen/records/pdf', [PdfController::class, 'generateSeniorCitizenPdf'])
    ->name('senior-citizen.pdf');
Route::get('/tb-dots/records/pdf', [PdfController::class, 'generateTbDotsPdf'])
    ->name('tb-dots.pdf');
Route::get('/family-planning/records/pdf', [PdfController::class, 'generateFamilyPlanningPdf'])
    ->name('family-planning.pdf');


    // LOUIE'S CHANGES

Route::get('/medicineRequest', MedicineRequestComponent::class)->name('medicineRequest');


Route::get('inventory/manage-medicine-requests', ManageMedicineRequests::class)->name('manageMedicineRequests');


Route::get('inventory/medicine-request-logs', MedicineRequestLogComponent::class)->name('medicineRequestLog');
// family planning side a
Route::get("/family-planning/side-a/pdf",[PdfController::class, 'generateFamilyPlanningSideAPdf'])->name('family-planning-side-a.pdf');
Route::get("/family-planning/side-b/pdf", [PdfController::class, 'generateFamilyPlanningSideBPdf'])->name('family-planning-side-b.pdf');

Route::get('/immunization/patient/{patientId}', [ImmunizationController::class, 'showByPatient'])
    ->name('immunization.patient');

Route::get('/immunization/card-content/{patientId}', [ImmunizationController::class, 'getCardContent'])
    ->name('immunization.card-content');

Route::get('/immunization/pdf/{patientId}', [ImmunizationController::class, 'generatePDF'])
    ->name('immunization.pdf');
Route::get('/vaccination/case/pdf',[PdfController::class,'generateVaccinationCasePdf'])->name("vaccination-case.pdf");

Route::get('/prenatal/case-record/pdf',[PdfController::class, 'generatePrenatalCasePdf'])->name('prenatal-case.pdf');
Route::get('/prenatal/pregnancy-plan/pdf', [PdfController::class, 'generatePregnancyPdf'])->name('pregnancy-plan.pdf');
Route::get('/prenatal/check-up/pdf',[PdfController::class, 'generatePrenatalCheckupPdf'])->name('prenatal-checkup.pdf');

Route::get('/senior-citizen/case-record/pdf',[PdfController::class, 'generateSeniorCitizenCasePdf'])->name('senior-citizen-case.pdf');
Route::get('/tb-dots/case-record/pdf',[PdfController::class, 'generateTbDotsCasePdf'])->name('tb-dots-case.pdf');
Route::get('/tb-dots/check-up/pdf', [PdfController::class, 'generateTbDotsCheckupPdf'])->name('tb-dots-checkup.pdf');

// masterlist pdf
Route::get('/masterlist/vaccination/pdf',[PdfController::class, 'generateVaccinationMasterlist'])->name('vaccination-masterlist.pdf');
Route::get('/masterlist/wra/pdf', [PdfController::class, 'generateWraMasterlist'])->name('wra-masterlist.pdf');

// testing area
Route::get('/test-prenatal', function (){
    return view('pdf.prenatal.prenatal-case');
});

// verification email
Route::get('/verify-email', [VerificationController::class, 'show'])->name('verification.show');
Route::post('/verify-email', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/verify-email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
// patient list

Route::get('/patient-list',[PatientList::class,'index'])->name('patient-list');


// pdf route:

Route::get('/download-medicine-report', [InventoryController::class, 'downloadMedicineReport'])->name('download.medicine.report');
Route::get('/download-request-report', [InventoryController::class, 'downloadRequestReport'])->name('download.request.report');
Route::get('/download-distributed-report', [InventoryController::class, 'downloadDistributedReport'])->name('download.distributed.report');
Route::get('/download-low-stock-report', [InventoryController::class, 'downloadLowStockReport'])->name('download.lowstock.report');
Route::get('/download-expiring-soon-report', [InventoryController::class, 'downloadExpiringSoonReport'])->name('download.expSoon.report');

// testing area
Route::get('/pdf/generate/dashbord',[PdfController::class, 'generateDashboardTable'])->name('generate-dashboad.pdf');
Route::get('/pdf/generate/graph',[PdfController::class, 'generateDashboardGraph'])->name("generate-dashboard-graph.pdf");

// NOTIFICATION SECTION
Route::middleware(['auth'])->group(function () {
    // Notifications routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent']);
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/delete-all-read', [NotificationController::class, 'deleteAllRead'])->name('notifications.delete-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

});

