<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Student;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v2'], function () {
    Route::post('/login',  function (Request $request) {
        $student = Student::where('user_name', $request->username)->where('password', $request->password)->first();
        if ($student) {
            return response()->json(['message' => 'Login successful', 'student_id' => $student->id], 200);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    });
    Route::get('/student_profile/{student_id}',  function (Request $request, $student_id) {
        $student = Student::findorFail($student_id);
        return response()->json($student);
    });
});