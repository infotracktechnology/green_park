<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{Student, Chairmanvideo, Announcement, Examportion, RevisionVideo, TimetableAssign, SickRoomEntry, Exam, ClassVideo, QuestionKey, AnswerKey, DiscussionVideo, Download, Worksheet, Achievement, ExamSubjectReport,HostelAttendance,InOutRegister,ExamAnswer};
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

Route::group(['prefix' => 'v2'], function () {

    Route::post('/login',  function (Request $request) {
        $student = Student::where('user_name', $request->username)->where('password_1', $request->password)->first();
        if ($student) {
            $student->active = 1;
            $student->save();
            return response()->json(['message' => 'Login successful', 'student_id' => $student->id], 200);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    });

    Route::get('/student_profile/{student_id}', function ($student_id, Student $attendanceService) {
        $student = Student::findOrFail($student_id);
        $attendanceStats = $attendanceService->calculateCurrentMonthStats($student->student_id);
        $student->total_attendance_days = $attendanceStats->total_days;
        $student->present_attendance_days = $attendanceStats->present_days;
        $student->branch_name = $student->branch->name ?? 'N/A';
        return response()->json($student);
    });

    Route::get('/chairmanvideo/{student_id}',  function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $chairmanvideo = $student->chairmanvideo();
        return response()->json($chairmanvideo);
    });

    Route::get('/announcement_titles/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $announcements = Announcement::ForStudent($student)->latest()->get();
        return response()->json($announcements);
    });

    Route::get('/announcement/{id}', function (Request $request, $id) {
        $announcement = Announcement::find($id);
        if ($announcement) {
            $announcement->content = preg_replace('/<\/?p>/', '', $announcement->content);
            $announcement->attachment = "public/" . $announcement->attachment;
        }
        return response()->json($announcement);
    });

    Route::get('/announcement/count/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $announcement = Announcement::ForStudent($student)->whereJsonDoesntContain('student_ids', $this->student_id)->count();
        return response()->json(['count' => $announcement]);
    });

    Route::post('/announcement', function (Request $request) {
        $announcement = Announcement::find($request->id);
        $student_ids = $announcement->student_ids ? $announcement->student_ids : [];
        if (!in_array($request->student_id, $student_ids)) {
            $student_ids[] = $request->student_id;
            $announcement->student_ids = $student_ids;
            $announcement->save();
        }
        return response()->json($announcement);
    });

    Route::get('/examportion/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $examportion = Examportion::ForStudent($student);
        return response()->json($examportion);
    });

    Route::get('/examresult/{student_id}', function (Request $request, $student_id) {
        $results = DB::select("SELECT DATE_FORMAT(b.start_at, '%d-%m-%Y')exam_date,b.name,b.testid,sum(mark)mark,(count(q_no)*4)total FROM `exam_answer` a join exam b on a.test_id=b.id where student_id=$student_id and b.publish='Yes' group by test_id order by b.updated_at desc limit 5");
        $testgroup = ['CUMULATIVE (CHEBOT)', 'CUMULATIVE (PHYZOO)', 'GRAND TEST', 'WEEKEND (BOTANY)', 'WEEKEND (CHEMISTRY)', 'WEEKEND (PHYSICS)', 'WEEKEND (ZOOLOGY)'];
        $results = count($results) > 0 ? $results : [];
        return response()->json(['results' => $results, 'testgroup' => $testgroup]);
    });

    Route::get('/perviousexamresult/{student_id}/{subject}', function (Request $request, $student_id, $subject) {
        $subjectexam = ExamSubjectReport::where("subject", "like", "%$subject%")->where("stuid", $student_id)->orderByRaw("STR_TO_DATE(exdate, '%d-%m-%Y') desc")->get();
        $header = $subjectexam->first()?->Header($subject);
        $subjectexam = $subjectexam->map(function ($subjectexam) use ($subject) {
            return [
                'exam_date' => $subjectexam->exdate,
                'subject' => $subjectexam->subject,
                'scores' => $subjectexam->getScoresForHeader($subject),
            ];
        });
        return response()->json(['header' => $header, 'results' => $subjectexam]);
    });

    Route::get('/marksheet/{student_id}/{testid}', function (Request $request, $student_id, $testid) {
        $student = Student::where('student_id', $student_id)->select('student_name', 'user_name','academic_year')->first();
        $exam = Exam::where(['testid' => $testid, 'academic_year' => $student->academic_year])->first();
        $answers = ExamAnswer::where(['test_id' => $testid, 'student_id' => $student_id])->orderBy('q_no')->get()->map(function ($answer) {
            return [
                'q_no' => $answer->q_no,
                'answer_key' => $answer->answer_key,
                'answer' => $answer->answer,
                'mark' => $answer->answer_key == 'DEL' ? 'DEL' : ($answer->mark == 4 ? 'C' : ($answer->mark == -1 ? 'W' : 'L'))
            ];
        })->chunk(45);

        return response()->json(['answers' => $answers, 'subject' => $exam->name, 'exam_date' => $exam->exam_date, 'test_id' => $testid, 'student' => $student]);
    });

    Route::get('/questionkey/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $questionkey = QuestionKey::ForStudent($student);
        return response()->json($questionkey);
    });

    Route::get('/answerkey/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $answerkey = AnswerKey::ForStudent($student);
        return response()->json($answerkey);
    });

    Route::get('/downloads/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $downloads = Download::ForStudent($student);
        return response()->json($downloads);
    });

    Route::get('/classvideos/{student_id}/{subject}/{period}', function ($student_id, $subject, $period) {
        $student = Student::where('student_id', $student_id)->first();
        $classvideos = ClassVideo::ForStudent($student, $subject)->where('period', $period);
        return response()->json($classvideos);
    });

    Route::get('/classvideos/subject', function (Student $student) {
        $subjects = ['PHYSICS', 'CHEMISTRY', 'BOTANY', 'ZOOLOGY'];
        return response()->json($subjects);
    });


    Route::get('/discussionvideo/{student_id}/{subject}', function ($student_id, $subject) {
        $student = Student::where('student_id', $student_id)->first();
        $discussionvideos = DiscussionVideo::ForStudent($student, $subject);
        $discussionvideos = $discussionvideos->groupBy('part');
        return response()->json($discussionvideos);
    });


    Route::get('revisionvideos/{student_id}', function ($student_id) {
        $datetime = date('Y-m-d H:i:s');
        $student = Student::where('student_id', $student_id)->first();
        $revisionvideos = RevisionVideo::ForStudent($student);
        return response()->json($revisionvideos);
    });

    Route::get('/worksheet/{student_id}', function ($student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $worksheet = Worksheet::ForStudent($student);
        return response()->json($worksheet);
    });

    Route::get('/achievements/{student_id}', function ($student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $achievements = Achievement::ForStudent($student);
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
            $data['attachment'] = 'uploads/concern/' . $fileName;
        }
        $parent_concern = DB::table('parent_concern')->insert($data);
        return response()->json($parent_concern);
    });
    Route::get('/parent_concern/{student_id}', function (Request $request, $student_id) {
        $parent_concern = DB::table('parent_concern')->where('student_id', $student_id)->get();
        return response()->json($parent_concern ?? []);
    });

    Route::post('/document_upload', function (Request $request) {
        $data = $request->all();
        if ($request->has('attachment') && $request->attachment != null) {
            $fileName = time() . '-' . $request->attachment->getClientOriginalName();
            $request->attachment->move('documents', $fileName);
            $data['file'] = 'documents/' . $fileName;
        }
        $parent_concern = DB::table('documents')->insert($data);
        return response()->json($parent_concern);
    });

    Route::get('/device_token/{student_id}/{device_token}',  function ($student_id, $device_token) {
        $student = Student::where('student_id', $student_id)->update(['device_token' => $device_token]);
        return response()->json($student);
    });

    #Hostel API
   Route::get('/sickroomentry/{student_id}', function ($student_id) {
        $sickroomentry = SickRoomEntry::where('student_id', $student_id)->latest()->get();
        return response()->json($sickroomentry);
    });

    Route::get('/hostelattendance/{student_id}', function ($student_id) {
        $monthwise = HostelAttendance::where('student_id', $student_id)->whereMonth('attendance_date', date('m'))->get();
        $daywise = HostelAttendance::where('student_id', $student_id)->where('attendance_date', date('Y-m-d'))->get();
        return response()->json(['monthwise' => $monthwise, 'daywise' => $daywise]);
    });

     Route::get('hostel/inoutregister/{student_id}/',  function ($student_id) {
        $student = InOutRegister::where('student_id', $student_id)->latest()->get();
        return response()->json($student);
    });

});
