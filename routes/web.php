<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\HomeController;

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
Route::group(['middleware' => ['auth'], 'prefix' => 'admin'],function(){
    Route::resource('branch', 'App\Http\Controllers\BranchController');
    Route::get('studentdashboard', function () {
    return view('dashboards.studentdashboard');
})->name('studentdashboard');
});
