<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Student;
use App\Models\Chairmanvideo;
use App\Models\Announcement;
use App\Models\Examportion;
use App\Models\RevisionVideo;

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
        $student = Student::where('user_name', $request->username)->where('password_1', $request->password)->first();
        if ($student) {
            return response()->json(['message' => 'Login successful', 'student_id' => $student->id], 200);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    });
    Route::get('/student_profile/{student_id}',  function (Request $request, $student_id) {
        $student = Student::findorFail($student_id);
        return response()->json($student);
    });
    Route::get('/chairmanvideo',  function (Request $request,Student $student) {
        $chairmanvideo = $student->chairmanvideo();
        return response()->json($chairmanvideo);
    });
    Route::get('/announcements', function (Request $request,Student $student) {
        $announcements = $student->announcement();
        return response()->json($announcements);
    });
    Route::get('/examportion', function (Request $request,Student $student) {
        $examportion = $student->examportion();
        return response()->json($examportion);
    });
    Route::get('/examresult/{student_id}', function (Request $request, $student_id) {
        $results = DB::select("SELECT DATE_FORMAT(b.start_at, '%d-%m-%Y')exam_date,b.name,test_id,sum(mark)mark,(count(q_no)*4)total FROM `exam_answer` a join exam b on a.test_id=b.id where student_id=$sid and b.publish='Yes' group by test_id order by b.updated_at desc limit 5");
        return response()->json($results);
    });

    Route::get('/questionkey', function (Request $request,Student $student) {
        $questionkey = $student->questionkey();
        return response()->json($questionkey);
    });

    Route::get('/answerkey', function (Request $request,Student $student) {
        $answerkey = $student->answerkey();
        return response()->json($answerkey);
    });

    Route::get('/downloads', function (Request $request,Student $student) {
        $downloads = $student->downloads();
        return response()->json($downloads);
    });

    Route::get('/classvideos/{subject}/{period}', function (Student $student, $subject, $period) {
        $classvideos = $student->classvideo($subject)->where('period', $period);
        return response()->json($classvideos);
    });

    Route::get('/subjects', function (Student $student) {
        $subjects = $student->subjects();
        return response()->json($subjects);
    });

    Route::get('/discussionvideo/{subject}', function (Student $student, $subject) {
        $discussionvideos = $student->discussionvideos($subject);
        $discussionvideos = $discussionvideos->groupBy('part');
        return response()->json($discussionvideos);
    });


    Route::get('revisionvideos/', function () {
        $datetime = date('Y-m-d H:i:s');
        $revisionvideos = RevisionVideo::where('expire_at', '>=', $datetime)->get();
        return response()->json($revisionvideos);
    });
    
});
