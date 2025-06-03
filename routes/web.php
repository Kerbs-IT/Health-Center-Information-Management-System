<?php

use App\Http\Controllers\addressController;
use App\Http\Controllers\authController;
use App\Http\Controllers\brgyUnit;
use App\Http\Controllers\brgyUnitController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\nurseDeptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/auth/login',[authController::class, 'login']) -> name('login');

// logout
Route::get('/logout',[LoginController::class,'logout']) -> name('logout');

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

Route::get('/dashboard/nurse',function (){
    return view('dashboard.nurse');
}) -> name('dashboard.nurse');

Route::get('/dashboard/staff',function (){
    return view('dashboard.staff');
}) -> name('dashboard.staff');


// menu bar blade
Route::get('/menuBar',function (){
    return view('layout.menuBar');
}) -> name('menubar');

// profile route
Route::get('/profile',function (){
    return view('pages.profile', ['isProfile' => true]);
}) -> name('page.profile');


// address route
Route::get('/get-provinces/{regionCode}',[addressController::class,'getProvinces']);
Route::get('/get-cities/{provinceCode}',[addressController::class,'getCities']);
Route::get('/get-brgy/{cityCode}',[addressController::class,'getBrgy']);
?>