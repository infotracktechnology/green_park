<?php

use App\Http\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\HostelController;

use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentController;
use App\Models\Announcement;
use App\Models\Student;
use App\Http\Controllers\ChairmanVideoController;

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

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('preventCache');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login');

#admin routes
Route::group(['middleware' => ['auth:web'], 'prefix' => 'admin'], function () {
    Route::resource('branch', 'App\Http\Controllers\BranchController');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.home');

    Route::resource('staff', App\Http\Controllers\StaffProfileController::class);
    Route::resource('student', 'App\Http\Controllers\StudentController');

    Route::resource('announcement', 'App\Http\Controllers\AnnouncementController');

    Route::get('import/student', [ImportController::class, 'index'])->name('import.student');
    Route::post('import/upload/student', [ImportController::class, 'upload'])->name('import.student.upload');

    Route::resource('hostel', App\Http\Controllers\HostelController::class);
    Route::delete('room/delete/{id}', [HostelController::class, 'deleteRoom'])->name('room.delete');
    Route::get('export/student', 'App\Http\Controllers\ExportController@student_export')->name('export.student');


    Route::get('section/student', 'App\Http\Controllers\StudentController@section')->name('section.student');
    Route::post('section/student', 'App\Http\Controllers\StudentController@update_section')->name('section.update');
    Route::get('allocation/hostel', 'App\Http\Controllers\HostelController@allocation')->name('allocation.hostel');

    Route::post('allocation/hostel', 'App\Http\Controllers\HostelController@storeAllocation')->name('allocation.store');
    Route::resource('exam', 'App\Http\Controllers\ExamController');
    Route::resource('chairmanvideo', 'App\Http\Controllers\ChairmanVideoController');
    Route::get('exam/instruction/{test_id}', 'App\Http\Controllers\ExamController@instruction')->name('exam.instruction');

});

#students routes

Route::group(['middleware' => ['auth:student'], 'prefix' => 'student'], function () {
Route::get('dashboard', [StudentController::class, 'dashboard'])->name('studentdashboard');
Route::get('profile', [StudentController::class, 'profile'])->name('student.profile');
Route::get('home',[StudentController::class, 'home'])->name('student.home');
Route::get('notification',[AnnouncementController::class, 'notification'])->name('student.notification');
Route::get('chairmanvideo',[ChairmanVideoController::class, 'chairmanvideo'])->name('student.chairmanvideo');
#Route::view('/teacher/dashboard', 'dashboards.teacherdashboard')->name('teacherdashboard');
});

