<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Student;
use App\Models\Chairmanvideo;
use App\Models\Announcement;
use App\Models\Examportion;
use App\Models\RevisionVideo;
use App\Models\TimetableAssign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SickRoomEntry;
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
    Route::get('/student_profile/{student_id}',  function (Student $student, $student_id) {
        $student = Student::where('id', $student_id)->first();
        $student->branch_name = $student->branch->name;
        return response()->json($student);
    });

    Route::get('/chairmanvideo/{student_id}',  function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $chairmanvideo = $student->chairmanvideo();
        return response()->json($chairmanvideo);
    });

    Route::get('/announcement_titles/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $announcements = $student->announcement();
        return response()->json($announcements);
    });

    Route::get('/announcement/{id}', function (Request $request, $id) {
        $announcement = Announcement::find($id);
        if ($announcement) {
            $announcement->content = preg_replace('/<\/?p>/', '', $announcement->content);
            $announcement->attachment = "public/".$announcement->attachment;
        }
        return response()->json($announcement);
    });

     Route::get('/announcement/count/{student_id}', function (Request $request, $student_id) {
         $student = Student::where('student_id', $student_id)->first();
         $announcement = $student->announcement_count();
        return response()->json(['count' => $announcement]);
    });

     Route::post('/announcement', function (Request $request) {
       $announcement = Announcement::find($request->id);
       $student_ids = $announcement->student_ids ? $announcement->student_ids : [];
       $student_ids[] = $request->student_id;
       $announcement->student_ids = $student_ids;
       $announcement->save();
       return response()->json($announcement);
    });

    Route::get('/examportion/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $examportion = $student->examportion()->get()->map(function ($examportion) {
            $examportion->attachment = "public/".$examportion->attachment;
            return $examportion;
        });
        return response()->json($examportion);
    });
    
    Route::get('/examresult/{student_id}', function (Request $request, $student_id) {
        $results = DB::select("SELECT DATE_FORMAT(b.start_at, '%d-%m-%Y')exam_date,b.name,test_id,sum(mark)mark,(count(q_no)*4)total FROM `exam_answer` a join exam b on a.test_id=b.id where student_id=$student_id and b.publish='Yes' group by test_id order by b.updated_at desc limit 5");
        $results = count($results) > 0 ? $results : [];
        return response()->json($results);
    });

    Route::get('/questionkey/{student_id}', function (Request $request,$student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $questionkey = $student->questionkey();
        return response()->json($questionkey);
    });

    Route::get('/answerkey/{student_id}', function (Request $request,$student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $answerkey = $student->answerkey();
        return response()->json($answerkey);
    });

    Route::get('/downloads/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $downloads = $student->downloads();
        return response()->json($downloads);
    });

    Route::get('/classvideos/{student_id}/{subject}/{period}', function ($student_id, $subject, $period) {
        $student = Student::where('student_id', $student_id)->first();
        $classvideos = $student->classvideo($subject)->where('period', $period);
        return response()->json($classvideos);
    });

    Route::get('/classvideos/subject', function (Student $student) {
        $subjects = ['physics', 'chemistry', 'botany', 'zoology'];
        return response()->json($subjects);
    });


    Route::get('/discussionvideo/{student_id}/{subject}', function ($student_id, $subject) {
        $student = Student::where('student_id', $student_id)->first();
        $discussionvideos = $student->discussionvideos($subject);
        $discussionvideos = $discussionvideos->groupBy('part');
        return response()->json($discussionvideos);
    });


    Route::get('revisionvideos/{student_id}', function ($student_id) {
        $datetime = date('Y-m-d H:i:s');
        $student = Student::where('student_id', $student_id)->first();
        $revisionvideos = RevisionVideo::where('expire_at', '>=', $datetime)->where('academic_year', $student->academic_year)->get();
        return response()->json($revisionvideos);
    });

    Route::get('/worksheet/{student_id}', function ($student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $worksheet = $student->worksheet();
        return response()->json($worksheet);
    });

    Route::get('/achievements/{student_id}', function ($student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $achievements = $student->achievements();
        return response()->json($achievements);
    });


    Route::get('/timetable/{branch_id}/{section}', function ($branch_id, $section) {
      $periods = TimetableAssign::where('branch_id', $branch_id)->where('section', $section)->first();
      return response()->json($periods->periods ?? []);
    });

    Route::get('/attendance/{student_id}', function ($student_id) {
      $monthwise = DB::table('attendance')->where('student_id', $student_id)->whereMonth('attendance_date', date('m'))->get();
      $daywise = DB::table('attendance')->where('student_id', $student_id)->where('attendance_date', date('Y-m-d'))->get();
      return response()->json(['monthwise' => $monthwise, 'daywise' => $daywise]);
    });

    Route::post('/parent_concern', function (Request $request) {
        $data = $request->all();
        if ($request->has('attachment') && $request->attachment != null) {
            $fileName = time() . '-' . $request->attachment->getClientOriginalName();
            $request->attachment->move('uploads/concern', $fileName);
            $data['attachment'] = 'uploads/concern/'.$fileName;
        }
        $parent_concern = DB::table('parent_concern')->insert($data);
        return response()->json($parent_concern);
    });
    Route::get('/parent_concern/{student_id}', function (Request $request, $student_id) {
        $parent_concern = DB::table('parent_concern')->where('student_id', $student_id)->get();
        return response()->json($parent_concern ?? []);
    });
    // Route::get('/chat/messages/{user_id}', function (Request $request, $user_id) {
    //     $read = DB::table('chat')->where('sender_id', $user_id)->where('chat_read', 0)->update(['chat_read' => 1]);
    //     $messages = DB::table('chat')->where('sender_id', $user_id)->orWhere('receiver_id', $user_id)->selectRaw("type, message,sender_id,receiver_id,created_at")->orderBy('created_at', 'desc')->get();
    //     return response()->json($messages);
    // });
    Route::get('/sickroomentry/{student_id}', function ($student_id) {
        $sickroomentry = SickRoomEntry::where('student_id', $student_id)->get();
        return response()->json($sickroomentry);
    });
    Route::get('/hostelattendance/{student_id}', function ($student_id) {
        $monthwise = DB::table('hostel_attendance')->where('student_id', $student_id)->whereMonth('attendance_date', date('m'))->get();
        $daywise = DB::table('hostel_attendance')->where('student_id', $student_id)->where('attendance_date', date('Y-m-d'))->get();
        return response()->json(['monthwise' => $monthwise, 'daywise' => $daywise]);
      });

      Route::post('/document_upload', function (Request $request) {
        $data = $request->all();
        if ($request->has('attachment') && $request->attachment != null) {
            $fileName = time() . '-' . $request->attachment->getClientOriginalName();
            $request->attachment->move('documents', $fileName);
            $data['file'] = 'documents/'.$fileName;
        }
        $parent_concern = DB::table('documents')->insert($data);
        return response()->json($parent_concern);
    });

    Route::get('/device_token/{student_id}/{device_token}',  function ($student_id, $device_token) {
        $student = Student::where('student_id', $student_id)->update(['device_token' => $device_token]);
        return response()->json($student);
    });

});
