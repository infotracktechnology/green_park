<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\StaffProfileController;


/*
|-------------------------------------------------------------------------- 
| Web Routes
|-------------------------------------------------------------------------- 
|
| Here is where you can register web routes for your application. These 
| routes are loaded by the RouteServiceProvider within a group which 
| contains the "web" middleware group. Now create something great! 
| 
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('preventCache');
Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

#admin routes
Route::group(['middleware' => ['auth:web'], 'prefix' => 'admin'], function () {
    Route::resource('branch', 'App\Http\Controllers\BranchController');

    Route::get('studentdashboard', function () {
        return view('dashboards.studentdashboard');
    })->name('studentdashboard');
    Route::get('teacherdashboard', function () {
        return view('dashboards.teacherdashboard');
    })->name('teacherdashboard');

    Route::resource('staff', App\Http\Controllers\StaffProfileController::class);


    Route::get('/get-districts/{state}', [StaffProfileController::class, 'getDistricts']);

    Route::resource('student', 'App\Http\Controllers\StudentController');
    Route::get('import/student', [ImportController::class, 'index'])->name('import.student');
    Route::post('import/upload/student', [ImportController::class, 'upload'])->name('import.student.upload');
});
