<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ExamPortionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ChairmanVideoController;
use App\Http\Controllers\QuestionKeyController;
use App\Http\Controllers\AnswerkeyController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\WorksheetController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\RevisionVideoController;
use App\Http\Controllers\ClassVideoController;
use App\Http\Controllers\DiscussionVideoController;
use App\Http\Controllers\SickRoomEntryController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\StudentActivityController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Exam;

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
Route::get('/notify', [App\Http\Controllers\HomeController::class, 'notify']);

#admin routes
Route::group(['middleware' => ['auth:web'], 'prefix' => 'admin'], function () {
    Route::resource('branch', 'App\Http\Controllers\BranchController');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.home');
    Route::get('/dashboard/gender', [App\Http\Controllers\HomeController::class, 'dashboardGender'])->name('dashboard.gender');
    Route::get('/dashboard/staff', [App\Http\Controllers\HomeController::class, 'dashboardStaff'])->name('dashboard.staff');
    Route::get('/dashboard/announcement', [App\Http\Controllers\HomeController::class, 'dashboardAnnouncement'])->name('dashboard.announcement');
    Route::match(['get', 'post'], '/staff-class', [StaffProfileController::class, 'classAssign'])->name('staff.class');
    Route::post('/staff-subject', [StaffProfileController::class, 'subjectAssign'])->name('staff.subjectAssign');

    Route::resource('staff', App\Http\Controllers\StaffProfileController::class);
    Route::post('staff/export', [App\Http\Controllers\StaffProfileController::class, 'export'])->name('staff.export');
    Route::post('staff/import', [App\Http\Controllers\StaffProfileController::class, 'import'])->name('staff.import');


    Route::resource('student', 'App\Http\Controllers\StudentController');
    Route::resource('announcement', 'App\Http\Controllers\AnnouncementController');

    Route::get('import/student', [ImportController::class, 'index'])->name('import.student');
    Route::post('import/upload/student', [ImportController::class, 'upload'])->name('import.student.upload');
    Route::resource('hostel', App\Http\Controllers\HostelController::class);
    Route::post('room/delete/', [HostelController::class, 'deleteRoom'])->name('room.delete');
    Route::get('export/student', 'App\Http\Controllers\ExportController@student_export')->name('export.student');


    Route::get('/hostel-attendance', [HostelController::class, 'attendanceEntry'])->name('hostelattendance');
    Route::post('/hostel-attendance/store', [HostelController::class, 'storeAttendance'])->name('hostelattendance.store');

    Route::get('section/student', 'App\Http\Controllers\StudentController@section')->name('section.student');
    Route::post('section/student', 'App\Http\Controllers\StudentController@update_section')->name('section.update');
    Route::get('allocation/hostel', 'App\Http\Controllers\HostelController@allocation')->name('allocation.hostel');
    Route::post('allocation/hostel', 'App\Http\Controllers\HostelController@storeAllocation')->name('allocation.store');
    Route::resource('sickroom', SickRoomEntryController::class);
    Route::put('sickroom/{id}', [SickRoomEntryController::class, 'update'])->name('sickroom.update');

    Route::resource('exam', 'App\Http\Controllers\ExamController');

    Route::resource('chairmanvideo', 'App\Http\Controllers\ChairmanVideoController');
    Route::get('exam/instruction/{test_id}', 'App\Http\Controllers\ExamController@instruction')->name('exam.instruction');
    Route::resource('examportion', 'App\Http\Controllers\ExamPortionController');
    Route::get('enable/exam', [App\Http\Controllers\ExamController::class, 'enable'])->name('exam.enable');
    Route::post('enable/exam', [App\Http\Controllers\ExamController::class, 'enableExam'])->name('exam.enableExam');

    Route::get('test/exam', [ExamController::class, 'test'])->name('exam.test');
    Route::post('exam/test/download', [ExamController::class, 'downloadTestReport'])->name('exam.test.download');
    Route::get('offline/exam', [ExamController::class, 'offline'])->name('exam.offline.index');
    Route::post('offline/exam', [ExamController::class, 'offlineUpload'])->name('exam.offline.upload');
    Route::get('answerkey/exam', [ExamController::class, 'answerKey'])->name('exam.answerkey');
    Route::post('answerkey/exam', [ExamController::class, 'uploadAnswerKey'])->name('exam.answerkey.upload');
    Route::get('exam/report/dump', 'App\Http\Controllers\ExamController@Dump_Report')->name('exam.report.dump');

    Route::delete('answerkey/delete/{id}/{test_id}', [App\Http\Controllers\ExamController::class, 'deleteAnswerKey'])->name('answerkey.delete');

    Route::delete('offline/delete/{id}/{test_id}', [App\Http\Controllers\ExamController::class, 'deleteOfflineKey'])->name('offline.delete');

    Route::resource('questionkey', QuestionKeyController::class);
    Route::get('/questionkey/download/{id}', [QuestionKeyController::class, 'download'])->name('questionkey.download');

    Route::resource('answerkey', AnswerKeyController::class);
    Route::get('/answerkey/download/{id}', [AnswerKeyController::class, 'download'])->name('answerkey.download');

    Route::resource('download', DownloadController::class);
    Route::get('/download/download/{id}', [AnswerKeyController::class, 'download'])->name('download.download');
    Route::resource('worksheet', WorksheetController::class);
    Route::get('/worksheet/download/{id}', [WorksheetController::class, 'download'])->name('worksheet.download');

    Route::resource('achievement', AchievementController::class);
    Route::get('/exam/csv_download/{test_ids}', [App\Http\Controllers\ExamController::class, 'csv_download'])->name('exam.csv_download');
    Route::post('/classvideo/bulk-delete', [ClassVideoController::class, 'bulkDelete'])->name('classvideo.bulk-delete');
    Route::resource('classvideo', ClassVideoController::class)->except(['show']);
    Route::get('classvideo/upload', [ClassVideoController::class, 'showUploadForm'])->name('classvideo.upload.form');
    Route::post('classvideo/upload', [ClassVideoController::class, 'upload'])->name('classvideo.upload.store');
  
    Route::delete('/discussionvideo/bulk-delete', [DiscussionVideoController::class, 'bulkDelete'])->name('discussionvideo.bulkDelete');

    Route::resource('discussionvideo', DiscussionVideoController::class);

    Route::post('classvideo/schedule', [ClassVideoController::class, 'schedule'])->name('classvideo.schedule');
   
    Route::delete('/revisionvideo/bulk-delete', [RevisionVideoController::class, 'bulkDelete'])->name('revisionvideo.bulkDelete');
    Route::resource('revisionvideo', RevisionVideoController::class);
    Route::resource('academicyear', App\Http\Controllers\AcademicYearController::class);

    Route::get('/report/section_exam/', [App\Http\Controllers\ReportController::class, 'section_exam'])->name('report.section_exam');
    Route::resource('holiday', App\Http\Controllers\HolidayController::class);
    Route::post('attendance/store', [App\Http\Controllers\HolidayController::class, 'attendance_store'])->name('attendance.store');
    Route::get('/attendance', [App\Http\Controllers\HolidayController::class, 'attendance'])->name('attendance');
    Route::resource('timetable', App\Http\Controllers\TimetableController::class);
    Route::resource('studentactivity', StudentActivityController::class);
    Route::match(['get', 'post'], '/parent_concern', [App\Http\Controllers\HomeController::class, 'parent_concern'])->name('parent_concern');
    Route::get('/chat', [App\Http\Controllers\HomeController::class, 'chat'])->name('chat.index');
    Route::match(['get', 'post'],'/fees/collection', [ App\Http\Controllers\FinanceController::class, 'collection'])->name('fees.collection');
    Route::get('/feetype', [App\Http\Controllers\FinanceController::class, 'feetype'])->name('feetype');
    Route::resource('feesplan', App\Http\Controllers\FinanceController::class);
    Route::get('studentmenu/branch', [App\Http\Controllers\HomeController::class, 'studentmenu_branch'])->name('studentmenu.branch');
    Route::get('studentmenu/type', [App\Http\Controllers\HomeController::class, 'studentmenu_type'])->name('studentmenu.type');
    Route::get('studentmenu/student', [App\Http\Controllers\HomeController::class, 'studentmenu_student'])->name('studentmenu.student');
  
    Route::resource('users', App\Http\Controllers\UsersController::class);
    Route::resource('workshift', App\Http\Controllers\WorkshiftController::class);
    Route::match(['get', 'post'],'/shiftwork/assign', [ App\Http\Controllers\WorkshiftController::class, 'assign'])->name('workshift.assign');
    Route::get('biometric/report', [App\Http\Controllers\StaffProfileController::class, 'biometric_report'])->name('biometric.report');
    Route::group(['prefix' => 'report','as' => 'report.'], function () {
        Route::get('/log', [App\Http\Controllers\ReportController::class, 'LogReport'])->name('log');
    });
});

#students routes

Route::group(['middleware' => ['auth:student'], 'prefix' => 'student'], function () {
    Route::get('dashboard', [StudentController::class, 'dashboard'])->name('studentdashboard');
    Route::get('profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::get('home', [StudentController::class, 'home'])->name('student.home');
    Route::get('notification', [AnnouncementController::class, 'notification'])->name('student.notification');
    Route::get('chairmanvideo', [ChairmanVideoController::class, 'chairmanvideo'])->name('student.chairmanvideo');
    Route::get('examportion', [ExamPortionController::class, 'examportion'])->name('student.examportion');
    Route::get('answerkey', [AnswerkeyController::class, 'answerkey'])->name('student.answerkey');
    Route::get('questionkey', [QuestionKeyController::class, 'questionkey'])->name('student.questionKey');
    Route::get('download', [DownloadController::class, 'download'])->name('student.download');
    Route::get('worksheet', [WorksheetController::class, 'worksheet'])->name('student.worksheet');
    Route::get('classvideo', [ClassVideoController::class, 'classvideo'])->name('student.classvideo');
    Route::get('revisionvideo', [RevisionVideoController::class, 'revisionvideo'])->name('student.revisionvideo');
    Route::get('discussionvideo', [StudentController::class, 'discussionvideo'])->name('student.discussionvideo');
    Route::get('instruction/{test_id}', 'App\Http\Controllers\ExamController@student_instruction')->name('student.instruction');
    Route::get('exam/{test_id}', 'App\Http\Controllers\ExamController@student_exam')->name('student.exam');
    Route::post('/exam/clearlog', 'App\Http\Controllers\ExamController@clearlog')->name('exam.clearlog');
    Route::post('/exam/save', 'App\Http\Controllers\ExamController@Save')->name('exam.save');
    Route::get('marksheet', [StudentController::class, 'marksheet'])->name('student.marksheet');
    Route::get('mark/subject/{test_id}', [StudentController::class, 'mark_subject'])->name('student.mark_subject');
    Route::get('mark/download/{test_id}', [StudentController::class, 'mark_download'])->name('student.mark_download');

    Route::get('student/documentupload', [StudentDocumentController::class, 'index'])->name('document.upload');
    Route::post('student/documentupload', [StudentDocumentController::class, 'store'])->name('document.store');
    Route::delete('/document/{id}', [App\Http\Controllers\StudentDocumentController::class, 'destroy'])->name('document.destroy');
    Route::get('/student/mock', [App\Http\Controllers\StudentMockTestController::class, 'index'])->name('student.mock');
    Route::get('/student/timetable', [App\Http\Controllers\TimetableController::class, 'timetable'])->name('student.timetable');
    
    Route::get('/student/attendance', [StudentController::class, 'attendance'])->name('student.attendance');


    



});

Route::post('/exam/submit', 'App\Http\Controllers\ExamController@submit')->name('exam.submit');
Route::get('video/{id}', 'App\Http\Controllers\ChairmanVideoController@video')->name('video');


Route::get('/student/login/{user_name}/{password}/{test_id}', function ($user_name, $password, $test_id) {
    if (Auth::guard('student')->attempt(['user_name' => $user_name, 'password' => $password])) {
        return redirect()->route('student.instruction', ['test_id' => $test_id]);
    } else {
        return redirect()->back()->with('error', 'Invalid username or password.');
    }
});

Route::get('/test/{id}', function ($student_id) {
    $student = Student::where('id', $student_id)->first();
    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }
    $exam = Exam::getOngoingExams($student->coaching_type, $student->campus);
    return response()->json(['test_id' => base64_encode($exam->id ?? '')]);
});



