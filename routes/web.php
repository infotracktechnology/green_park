<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\{
    Auth\LoginController,
    Auth\LogoutController,
    HomeController,
    ImportController,
    HostelController,
    StaffProfileController,
    StudentController,
    AnnouncementController,
    ExamPortionController,
    ExamController,
    ChairmanVideoController,
    QuestionKeyController,
    AnswerKeyController,
    DownloadController,
    WorksheetController,
    AchievementController,
    RevisionVideoController,
    ClassVideoController,
    DiscussionVideoController,
    SickRoomEntryController,
    StudentDocumentController,
    StudentActivityController,
    UsersController,
    ReportController
};

use App\Models\{Student, Exam};

// ------------------------------------------------------
// Guest routes
// ------------------------------------------------------
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('preventCache');
Route::get('/notify', [HomeController::class, 'notify']);

// ------------------------------------------------------
// Admin routes
// ------------------------------------------------------
Route::prefix('admin')->middleware('auth:web')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('admin.home');
    Route::controller(HomeController::class)->group(function () {
        Route::get('/filter', 'Filter')->name('filter');
        Route::get('/dashboard/gender', 'dashboardGender')->name('dashboard.gender');
        Route::get('/dashboard/staff', 'dashboardStaff')->name('dashboard.staff');
        Route::get('/dashboard/announcement', 'dashboardAnnouncement')->name('dashboard.announcement');
        Route::match(['get', 'post'], '/parent_concern', 'parent_concern')->name('parent_concern');
        Route::get('/chat', 'chat')->name('chat.index');
        Route::get('studentmenu/branch', 'studentmenu_branch')->name('studentmenu.branch');
        Route::get('studentmenu/type', 'studentmenu_type')->name('studentmenu.type');
        Route::get('studentmenu/student', 'studentmenu_student')->name('studentmenu.student');
    });

    Route::resource('branch', \App\Http\Controllers\BranchController::class);
    Route::controller(StaffProfileController::class)->group(function () {
        Route::match(['get', 'post'], '/staff-class', 'classAssign')->name('staff.class');
        Route::post('/staff-subject', 'subjectAssign')->name('staff.subjectAssign');
        Route::post('staff/export', 'export')->name('staff.export');
        Route::post('staff/import', 'import')->name('staff.import');
        Route::get('biometric/report', 'biometric_report')->name('biometric.report');
    });
    Route::resource('staff', StaffProfileController::class);

    Route::resource('student', StudentController::class);
    Route::resource('announcement', AnnouncementController::class);
    Route::resource('hostel', HostelController::class);
    Route::post('room/delete', [HostelController::class, 'deleteRoom'])->name('room.delete');
    Route::get('import/student', [ImportController::class, 'index'])->name('import.student');
    Route::post('import/upload/student', [ImportController::class, 'upload'])->name('import.student.upload');
    Route::get('export/student', [\App\Http\Controllers\ExportController::class, 'student_export'])->name('export.student');
    Route::get('allocation/hostel', [HostelController::class, 'allocation'])->name('allocation.hostel');
    Route::post('allocation/hostel', [HostelController::class, 'storeAllocation'])->name('allocation.store');
    Route::get('/hostel-attendance', [HostelController::class, 'attendanceEntry'])->name('hostelattendance');
    Route::post('/hostel-attendance/store', [HostelController::class, 'storeAttendance'])->name('hostelattendance.store');

    Route::resource('sickroom', SickRoomEntryController::class)->except(['update']);
    Route::put('sickroom/{id}', [SickRoomEntryController::class, 'update'])->name('sickroom.update');

    // Exams
    Route::resource('exam', ExamController::class);
    Route::controller(ExamController::class)->group(function () {
        Route::get('examination/instruction/{test_id}', 'instruction')->name('exam.instruction');
        Route::post('examination/testcategory', 'TestCategory')->name('exam.testcategory');
        Route::get('examination/enable', 'enable')->name('exam.enable');
        Route::post('examination/enable', 'enableExam')->name('exam.enableExam');
        Route::get('examination/test', 'test')->name('exam.test');
        Route::post('examination/test/download', 'downloadTestReport')->name('exam.test.download');
        Route::get('examination/offline', 'offline')->name('exam.offline.index');
        Route::post('examination/offline', 'offlineUpload')->name('exam.offline.upload');
        Route::get('examination/answerkey', 'answerKey')->name('exam.answerkey');
        Route::post('examination/answerkey', 'uploadAnswerKey')->name('exam.answerkey.upload');
        Route::get('examination/report/dump', 'Dump_Report')->name('exam.report.dump');
        Route::delete('examination/answerkey/delete/{id}/{test_id}', 'deleteAnswerKey')->name('answerkey.delete');
        Route::delete('examination/offline/delete/{id}/{test_id}', 'deleteOfflineKey')->name('offline.delete');
        Route::get('examination/csv_download/{test_ids}', 'csv_download')->name('exam.csv_download');
        Route::match(['get', 'post'],'examination/publish','Publish')->name('exam.publish');
    });
    
    Route::resource('examportion', ExamPortionController::class);

    Route::resource('chairmanvideo', ChairmanVideoController::class);
    Route::resource('questionkey', QuestionKeyController::class);
    Route::get('questionkey/download/{id}', [QuestionKeyController::class, 'download'])->name('questionkey.download');

    Route::resource('answerkey', AnswerKeyController::class);
    Route::get('answerkey/download/{id}', [AnswerKeyController::class, 'download'])->name('answerkey.download');

    Route::resource('download', DownloadController::class);
    Route::get('download/download/{id}', [AnswerKeyController::class, 'download'])->name('download.download');

    Route::resource('worksheet', WorksheetController::class);
    Route::get('worksheet/download/{id}', [WorksheetController::class, 'download'])->name('worksheet.download');

    Route::resource('achievement', AchievementController::class);

    // Videos
    Route::resource('classvideo', ClassVideoController::class)->except(['show']);
    Route::get('classvideo/upload', [ClassVideoController::class, 'showUploadForm'])->name('classvideo.upload.form');
    Route::post('classvideo/upload', [ClassVideoController::class, 'upload'])->name('classvideo.upload.store');
    Route::post('classvideo/schedule', [ClassVideoController::class, 'schedule'])->name('classvideo.schedule');
    Route::post('classvideo/bulk-delete', [ClassVideoController::class, 'bulkDelete'])->name('classvideo.bulk-delete');

    Route::resource('discussionvideo', DiscussionVideoController::class);
    Route::post('discussionvideo/bulk-delete', [DiscussionVideoController::class, 'bulkDelete'])->name('discussionvideo.bulkDelete');

    Route::resource('revisionvideo', RevisionVideoController::class);
    Route::post('revisionvideo/bulk-delete', [RevisionVideoController::class, 'bulkDelete'])->name('revisionvideo.bulkDelete');

    // Other
    Route::resources([
        'academicyear' => \App\Http\Controllers\AcademicYearController::class,
        'holiday'      => \App\Http\Controllers\HolidayController::class,
        'timetable'    => \App\Http\Controllers\TimetableController::class,
        'studentactivity' => StudentActivityController::class,
        'users'        => UsersController::class,
        'workshift'    => \App\Http\Controllers\WorkshiftController::class,
    ]);

    Route::post('attendance/store', [\App\Http\Controllers\HolidayController::class, 'attendance_store'])->name('attendance.store');
    Route::get('/attendance', [\App\Http\Controllers\HolidayController::class, 'attendance'])->name('attendance');

    Route::match(['get', 'post'], '/fees/collection', [\App\Http\Controllers\FinanceController::class, 'collection'])->name('fees.collection');
    Route::get('/feetype', [\App\Http\Controllers\FinanceController::class, 'feetype'])->name('feetype');
    Route::resource('feesplan', \App\Http\Controllers\FinanceController::class);

    Route::match(['get', 'post'], '/shiftwork/assign', [\App\Http\Controllers\WorkshiftController::class, 'assign'])->name('workshift.assign');

    // Reports
    Route::prefix('report')->as('report.')->group(function () {
        Route::get('/log', [ReportController::class, 'LogReport'])->name('log');
        Route::get('/attendance', [ReportController::class, 'AttendanceReport'])->name('attendance');
        Route::get('/section_exam', [ReportController::class, 'section_exam'])->name('section_exam');
        Route::get('/batchlist', [ReportController::class, 'BatchList'])->name('batchlist');
        Route::match(['get', 'post'],'/sectionlist', [ReportController::class, 'SectionList'])->name('sectionlist');
    });
});


// ------------------------------------------------------
// Students routes
// ------------------------------------------------------

Route::prefix('student')->middleware('auth:student')->group(function () {
    Route::controller(StudentController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('studentdashboard');
        Route::get('profile', 'profile')->name('student.profile');
        Route::get('marksheet', 'marksheet')->name('student.marksheet');
        Route::get('mark/subject/{test_id}', 'mark_subject')->name('student.mark_subject');
        Route::get('mark/download/{test_id}', 'mark_download')->name('student.mark_download');
        Route::get('attendance', 'attendance')->name('student.attendance');
    });

    Route::controller(AnnouncementController::class)->group(function () {
        Route::get('notification', 'notification')->name('student.notification');
    });

    Route::get('discussionvideo', [DiscussionVideoController::class, 'discussionvideo'])->name('student.discussionvideo');

    Route::controller(ChairmanVideoController::class)->group(function () {
        Route::get('chairmanvideo', 'chairmanvideo')->name('student.chairmanvideo');
    });

    Route::controller(ExamPortionController::class)->group(function () {
        Route::get('examportion', 'examportion')->name('student.examportion');
    });

    Route::controller(AnswerKeyController::class)->group(function () {
        Route::get('answerkey', 'answerkey')->name('student.answerkey');
    });

    Route::controller(QuestionKeyController::class)->group(function () {
        Route::get('questionkey', 'questionkey')->name('student.questionKey');
    });

    Route::controller(DownloadController::class)->group(function () {
        Route::get('download', 'download')->name('student.download');
    });

    Route::controller(WorksheetController::class)->group(function () {
        Route::get('worksheet', 'worksheet')->name('student.worksheet');
    });

    Route::controller(ClassVideoController::class)->group(function () {
        Route::get('classvideo', 'classvideo')->name('student.classvideo');
    });

    Route::controller(RevisionVideoController::class)->group(function () {
        Route::get('revisionvideo', 'revisionvideo')->name('student.revisionvideo');
    });

    Route::controller(ExamController::class)->group(function () {
        Route::get('instruction/{test_id}', 'student_instruction')->name('student.instruction');
        Route::get('exam/{test_id}', 'student_exam')->name('student.exam');
        Route::post('exam/clearlog', 'clearlog')->name('exam.clearlog');
        Route::post('exam/save', 'Save')->name('exam.save');
    });

    Route::resource('document', StudentDocumentController::class)->only(['index', 'store', 'destroy']);
    Route::get('mock', [\App\Http\Controllers\StudentMockTestController::class, 'index'])->name('student.mock');
    Route::get('timetable', [\App\Http\Controllers\TimetableController::class, 'timetable'])->name('student.timetable');
});

Route::post('/exam/submit', [ExamController::class, 'submit'])->name('exam.submit');
Route::get('video/{id}', [ChairmanVideoController::class, 'video'])->name('video');


Route::get('/student/login/{user_name}/{password}/{test_id}', function ($user_name, $password, $test_id) {
    if (Auth::guard('student')->attempt(compact('user_name', 'password'))) {
        return redirect()->route('student.instruction', ['test_id' => $test_id]);
    }
    return back()->with('error', 'Invalid username or password.');
});

Route::get('/test/{id}', function ($student_id) {
    $student = Student::find($student_id);
    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }
    $exam = Exam::getOngoingExams($student->coaching_type, $student->campus);
    return response()->json(['test_id' => base64_encode($exam->id ?? '')]);
});
