<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\{Student, Chairmanvideo, Announcement, Examportion, RevisionVideo, TimetableAssign, SickRoomEntry, Exam, ClassVideo, QuestionKey, AnswerKey, DiscussionVideo, Download, Worksheet, Achievement, ExamSubjectReport, HostelAttendance, InOutRegister, ExamAnswer, MockTest,Attendance};
use App\Http\Controllers\StudentController;
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

    Route::post('/login', function (Request $request) {
        $student = Student::where('user_name', $request->username)->where('password', $request->password)->first();
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

    Route::get('/chairmanvideo/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $chairmanvideo = Chairmanvideo::ForStudent($student);
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

    Route::get('/announcement/count/{student_id}/', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $announcement = Announcement::ForStudent($student)->get()->filter(function ($row) use ($student_id) {
            return !in_array($student_id, $row->student_ids ?? []);
        })->count();

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
        $student = Student::where('student_id', $student_id)->first();
        $sid = $student->student_id;
        $batch = $student->batch;
        $type = $student->coaching_type;
        $course = $student->course;
        $results = Exam::from("exam as b")->join('exam_answer as a', 'a.test_id', '=', 'b.testid')->where('a.student_id', $sid)->where('b.publish', 'Yes')->selectRaw("exam_date,b.name,test_id,sum(mark)mark,(count(mark)*4)total,markrange_file")->groupBy('test_id')->orderBy('b.updated_at', 'desc')->limit(5)->get()->map(function ($test) use ($batch, $type,$course) {
            if($type === "OFFLINE" && ($course === "NEET" || $course === "JEE")){
                $markrange = isset($test->markrange_file[$batch]) ? $test->markrange_file[$batch] : null;
            }else{
                $markrange = isset($test->markrange_file['online']) ? $test->markrange_file['online'] : null;
            }
            return ['exam_date' => $test->exam_date, 'name' => $test->name, 'test_id' => $test->test_id, 'mark' => $test->mark, 'total' => $test->total,'first_mark' => ExamAnswer::where('testname', $test->name)->selectRaw('SUM(mark) as mark')->groupBy('student_id')->orderBy('mark', 'desc')->first()?->mark,'markrange' => $markrange];
        });
        
        $testgroup = ExamSubjectReport::where([['category', '!=', ''], ['stuid', $student_id]])->pluck('category')->unique();
        $results = count($results) > 0 ? $results : [];
        return response()->json(['results' => $results, 'testgroup' => $testgroup]);
    });

    Route::get('/perviousexamresult/{student_id}/{subject}', function (Request $request, $student_id, $subject) {
        $subjectexam = ExamSubjectReport::where("subject", "like", "%$subject%")->where("stuid", $student_id)->orderByRaw("STR_TO_DATE(exdate, '%d-%m-%Y') desc")->get();
        return response()->json(['results' => $subjectexam]);
    });

    Route::get('/marksheet/{student_id}/{testid}', function (Request $request, $student_id, $testid) {
        $student = Student::where('student_id', $student_id)->select('student_name', 'user_name', 'academic_year')->first();
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

    Route::get('/mark_subject/{student_id}/{testid}', function (Request $request, $student_id, $testid) {
        $subjects = ExamAnswer::selectRaw("sum(mark=4)r,sum(mark=-1)w,sum(mark=0)l,sum(mark)tot,(count(q_no)*4)total,subject")->where('test_id', $testid)->where('student_id', $student_id)->groupBy('subject')->orderByRaw("FIELD(subject, 'Physics', 'Chemistry', 'Botany', 'Zoology')")->get();
        return response()->json($subjects);
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

    Route::get('/classvideos/{student_id}/', function ($student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $classvideos = ClassVideo::ForStudent($student);
        $classvideos = $classvideos->groupBy('date');
        return response()->json($classvideos);
    });

    Route::get('/classvideos/subject', function (Student $student) {
        $subjects = ['PHYSICS', 'CHEMISTRY', 'BOTANY', 'ZOOLOGY'];
        return response()->json($subjects);
    });


    Route::get('/discussionvideo/{student_id}', function ($student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $discussionvideos = DiscussionVideo::ForStudent($student);
        $discussionvideos = $discussionvideos->groupBy('date');
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
        $monthwise = Attendance::where('student_id', $student_id)->get();
        $daywise = Attendance::where('student_id', $student_id)->where('attendance_date', date('Y-m-d'))->get();
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

    Route::get('/device_token/{student_id}/{device_token}', function ($student_id, $device_token) {
        $student = Student::where('student_id', $student_id)->update(['device_token' => $device_token]);
        return response()->json($student);
    });

    #Hostel API
    Route::get('/sickroomentry/{student_id}', function ($student_id) {
        $sickroomentry = SickRoomEntry::where('student_id', $student_id)->latest()->get();
        return response()->json($sickroomentry);
    });

    Route::get('hostel/inoutregister/{student_id}/', function ($student_id) {
        $student = InOutRegister::where('student_id', $student_id)->latest()->get();
        return response()->json($student);
    });

    Route::get('/mocktest/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        return response()->json($student->GetMockTest());
    });

    Route::get('/onlineexam/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        return response()->json($student->GetExams());
    });

      Route::get('/studentdownload/{student_id}', function (Request $request, $student_id) {
        $student = Student::where('student_id', $student_id)->first();
        $files = File::glob("uploads/Student Download/$student->student_id.*");
        $files = array_map(function($file) {
            return str_replace('public/', '', $file);
        }, $files);
        return view('student.studentdownload', compact('files'));
    });

    Route::post('/logactivity',[StudentController::class,'logActivity']);
});
