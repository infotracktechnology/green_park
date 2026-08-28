<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{Auth\LoginController, Auth\LogoutController, HomeController, ImportController, HostelController, StaffProfileController, StudentController, AnnouncementController, ExamPortionController, ExamController, ChairmanVideoController, QuestionKeyController, AnswerkeyController, DownloadController, WorksheetController, AchievementController, RevisionVideoController, ClassVideoController, DiscussionVideoController, SickRoomEntryController, StudentDocumentController, StudentActivityController, UsersController, ReportController, FinanceController, FinanceReportController, ReceiptCancellationController, SegmentController, ConcessionControllerReferencevideoController, MockTestController, BranchController, ExportController, AcademicYearController, HolidayController, TimetableController, WorkshiftController, NeetScorecardController, PhoneCardController};
use App\Models\{Student, Exam};

// ------------------------------------------------------
// 1. Guest / Public Routes
// ------------------------------------------------------
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('preventCache');
Route::get('/notify', [HomeController::class, 'notify']);
//Route::get('video/{id}', [ChairmanVideoController::class, 'video'])->name('video');

// ------------------------------------------------------
// 2. Admin Routes (Authenticated)
// ------------------------------------------------------

Route::prefix('admin')->middleware('auth:web')->group(function () {

    // Dashboard & Home
    Route::get('/home', [HomeController::class, 'index'])->name('admin.home');
    Route::controller(HomeController::class)->group(function () {
        Route::get('/filter', 'Filter')->name('filter');
        Route::get('examination/filter', 'ExaminationFilter')->name('examination.filter');
        Route::get('/dashboard/gender', 'dashboardGender')->name('dashboard.gender');
        Route::get('/dashboard/staff', 'dashboardStaff')->name('dashboard.staff');
        Route::get('/dashboard/announcement', 'dashboardAnnouncement')->name('dashboard.announcement');
        Route::match(['get', 'post'], '/parent_concern', 'parent_concern')->name('parent_concern');
        Route::match(['get', 'post'], '/setting', 'Setting')->name('admin.setting');
        Route::get('/chat', 'chat')->name('chat.index');
        Route::get('studentmenu/branch', 'studentmenu_branch')->name('studentmenu.branch');
        Route::get('studentmenu/type', 'studentmenu_type')->name('studentmenu.type');
        Route::get('studentmenu/student', 'studentmenu_student')->name('studentmenu.student');
    });
    Route::controller(NeetScorecardController::class)->group(function () {
       Route::match(['get', 'post'], '/neetscorecard', 'NeetScorecard')->name('neetscorecard');
       Route::match(['get', 'post'], '/neetscorecard/index', 'index')->name('neetscorecard.index');
       Route::get('/neetscorecard/edit/{student_id}', 'edit')->name('neetscorecard.edit');
       Route::post('/neetscorecard/update/{student_id}', 'update')->name('neetscorecard.update');
       Route::post('/neetscorecard/remark', 'saveRemark')->name('neetscorecard.remark');
    });

    // Staff & Branch Management
    Route::resource('branch', BranchController::class);
    Route::resource('staff', StaffProfileController::class);
    Route::controller(StaffProfileController::class)->group(function () {
        Route::match(['get', 'post'], '/staff-class', 'classAssign')->name('staff.class');
        Route::post('/staff-subject', 'subjectAssign')->name('staff.subjectAssign');
        Route::post('staff/export', 'export')->name('staff.export');
        Route::post('staff/import', 'import')->name('staff.import');
        Route::get('biometric/report', 'biometric_report')->name('biometric.report');
        Route::match(['get', 'post'], 'staffs/restore', 'RestoreStaff')->name('staffs.restore');
    });

    // Student Management
    Route::resource('student', StudentController::class);
    Route::match(['get', 'post'], 'students/restore', [StudentController::class, 'RestoreStudent'])->name('students.restore');
    Route::post('students/permanentdelete', [StudentController::class, 'permanentdelete'])->name('students.permanentdelete');
    Route::post('getlogactivity', [StudentController::class, 'GetLogActivity'])->name('student.getlogactivity');
    Route::match(['post', 'delete'], 'option/document', [StudentController::class, 'DocumentOption'])->name('option.document');
    
    Route::controller(ImportController::class)->group(function () {
        Route::get('import/student', 'index')->name('import.student');
        Route::post('import/upload/student', 'upload')->name('import.student.upload');
        Route::match(['get', 'post'], 'import/studentupdate', 'StudentUpdate')->name('import.studentupdate');
    });
    Route::get('export/student', [ExportController::class, 'student_export'])->name('export.student');

    // Examination Module
    Route::resource('exam', ExamController::class);
    Route::get('examportion/export/excel', [ExamPortionController::class, 'export'])->name('examportion.export');
    Route::resource('examportion', ExamPortionController::class);
    Route::controller(ExamController::class)->group(function () {
        Route::get('examination/view/{examtype}', 'ViewExams')->name('exam.viewexams');
        Route::get('examination/instruction/{test_id}', 'instruction')->name('exam.instruction');
        Route::match(['get', 'post', 'delete'], 'examination/testcategory', 'TestCategory')->name('exam.testcategory');
        Route::get('examination/offlineexam', 'OfflineExam')->name('exam.offlineexam');
        Route::get('examination/enable', 'enable')->name('exam.enable');
        Route::post('examination/enable', 'enableExam')->name('exam.enableExam');
        Route::get('examination/onlineresponse', 'OnlineResponse')->name('exam.onlineresponse');
        Route::post('examination/onlineresponse/download', 'OnlineResponseDownload')->name('exam.onlineresponse.download');
        Route::get('examination/offline/{type}', 'offline')->name('exam.offline.index');
        Route::post('examination/offline', 'offlineUpload')->name('exam.offline.upload');
        Route::get('examination/answerkey/{type}', 'answerKey')->name('exam.answerkey');
        Route::post('examination/answerkey', 'uploadAnswerKey')->name('exam.answerkey.upload');
        Route::get('examination/report/dump', 'Dump_Report')->name('exam.report.dump');
        Route::delete('examination/answerkey/delete/{id}/{test_id}', 'deleteAnswerKey')->name('answerkey.delete');
        Route::delete('examination/offline/delete/{id}/{test_id}', 'deleteOfflineKey')->name('offline.delete');
        Route::get('examination/csv_download/{test_ids}', 'csv_download')->name('exam.csv_download');
        Route::match(['get', 'post'], 'examination/offlinepublish', 'OfflinePublish')->name('exam.offlinepublish');
        // Route::match(['get', 'post'], 'examination/onlinepublish', 'OnlinePublish')->name('exam.onlinepublish');
        Route::get('examination/onlinepublish', 'OnlinePublish')->name('exam.onlinepublish');
        Route::post('examination/onlinepublish', 'OnlinePublishStore')->name('exam.onlinepublish.store');
        Route::get('examination/perviousexamresult/', 'PerviousExamResult')->name('exam.perviousexamresult');
        Route::match(['get', 'post'], 'examination/previousexamupload', 'PreviousExamUpload')->name('exam.previousexamupload');
    });

    // Learning Content (Videos, Keys, Downloads)
    Route::resource('announcement', AnnouncementController::class);
    Route::resource('chairmanvideo', ChairmanVideoController::class);
    Route::resource('questionkey', QuestionKeyController::class);
    Route::get('questionkey/download/{id}', [QuestionKeyController::class, 'download'])->name('questionkey.download');
    Route::resource('answerkey', AnswerkeyController::class);
    Route::get('answerkey/download/{id}', [AnswerkeyController::class, 'download'])->name('answerkey.download');
    Route::resource('download', DownloadController::class);
    Route::get('download/download/{id}', [AnswerkeyController::class, 'download'])->name('download.download');
    Route::resource('worksheet', WorksheetController::class);
    Route::get('worksheet/download/{id}', [WorksheetController::class, 'download'])->name('worksheet.download');
    Route::resource('achievement', AchievementController::class);

    Route::controller(ClassVideoController::class)->group(function () {
        Route::resource('classvideo', ClassVideoController::class)->except(['show']);
        Route::get('classvideo/upload', 'showUploadForm')->name('classvideo.upload.form');
        Route::post('classvideo/upload', 'upload')->name('classvideo.upload.store');
        Route::post('classvideo/schedule', 'schedule')->name('classvideo.schedule');
        Route::post('classvideo/bulk-delete', 'bulkDelete')->name('classvideo.bulk-delete');
    });

    Route::controller(DiscussionVideoController::class)->group(function () {
        Route::resource('discussionvideo', DiscussionVideoController::class);
        Route::post('discussionvideo/bulk-delete', 'bulkDelete')->name('discussionvideo.bulkDelete');
    });

    Route::controller(RevisionVideoController::class)->group(function () {
        Route::resource('revisionvideo', RevisionVideoController::class)->except(['show']);
        Route::post('revisionvideo/bulk-delete', 'bulkDelete')->name('revisionvideo.bulkDelete');
    });
    Route::resource('referencevideo', ReferencevideoController::class)->except(['show']);

    Route::get('phoneturn/create', [PhoneCardController::class, 'create'])->name('phoneturn.create');
    Route::post('phoneturn', [PhoneCardController::class, 'store'])->name('phoneturn.store');
    Route::match(['get', 'post'],'hostel/topup',[HostelController::class, 'Topup'])->name('hostel.topup');
    
    // Hostel & Sickroom
    Route::resource('hostel', HostelController::class);
    Route::resource('sickroom', SickRoomEntryController::class)->except(['update']);
    Route::controller(HostelController::class)->group(function () {
        Route::put('sickroom/{sickroom}',[SickRoomEntryController::class, 'update'])->name('sickroom.update');
        Route::match(['get', 'post'], 'hostel/room/transfer', 'RoomTransfer')->name('room.transfer');
        Route::post('room/delete', 'deleteRoom')->name('room.delete');
        Route::get('allocation/hostel', 'allocation')->name('allocation.hostel');
        Route::post('allocation/hostel', 'storeAllocation')->name('allocation.store');
        Route::get('/hostel-attendance', 'attendanceEntry')->name('hostelattendance');
        Route::post('/hostel-attendance/store', 'storeAttendance')->name('hostelattendance.store');
        Route::match(['get', 'post'], 'hostel/room/reallocation', 'RoomReallocation')->name('room.reallocation');
        Route::match(['get', 'post'], 'hostel/room/inoutregister', 'InOutRegister')->name('hostel.inoutregister');
        Route::match(['get', 'post'], 'hostel/room/courier', 'HostelCourier')->name('hostel.courier');
    });

    // Finance Module
    Route::resource('feesplan', FinanceController::class);
    Route::controller(FinanceController::class)->group(function () {
        Route::match(['get', 'post'], '/fees/collection', 'collection')->name('fees.collection');
        Route::get('/fees', 'fees')->name('fees');
        Route::get('/feetype', 'feetype')->name('feetype');
        Route::get('/fees/receipt/{id}', 'receipt')->name('fees.receipt');
        Route::post('billtype/store', 'billTypestore')->name('billtype.store');
        Route::get('billtype/{id}/edit', 'billTypeedit')->name('billtype.edit');
        Route::put('billtype/{id}/update', 'billTypeupdate')->name('billtype.update');
        Route::get('bank/create', 'bankcreate')->name('bank.create');
        Route::post('bank/store', 'bankstore')->name('bank.store');
        Route::get('bank/{id}/edit', 'bankedit')->name('bank.edit');
        Route::put('bank/{id}/update', 'bankupdate')->name('bank.update');
        Route::match(['get', 'post'], '/fees/migration', 'FeesMigration')->name('fees.migration');
    });

    Route::controller(FinanceReportController::class)->group(function () {
        Route::get('dfc', 'dfc')->name('fees.report.dfc');
        Route::get('report/collection', 'collectionreport')->name('fees.report.collection');
        Route::get('report/due', 'dueReport')->name('fees.report.due');
    });

    Route::controller(ReceiptCancellationController::class)->group(function () {
        Route::get('/feereceiptlist', 'requestindex')->name('feereceiptlist');
        Route::get('/pendingfeereceiptlist', 'pendingindex')->name('pendingfeereceiptlist');
        Route::get('/receipt/cancel/requests', 'pendingRequests')->name('receipt.cancel.requests');
        Route::post('/receipt/request-cancel', 'requestCancel')->name('receipt.request.cancel');
        Route::get('/receipt/cancel/view/{id}', 'viewRequest')->name('receipt.cancel.view');
        Route::put('/receipt/cancel/approve/{id}', 'receiptcancelapprove')->name('receipt.cancel.approve');
        Route::put('/receipt/cancel/reject/{id}', 'receiptcancelreject')->name('receipt.cancel.reject');
    });

    Route::resource('segment', SegmentController::class);
    Route::get('assignsegment', [SegmentController::class, 'assign'])->name('assignsegment');
    Route::put('assignsegment', [SegmentController::class, 'assignSegment'])->name('assignsegment');
    Route::resource('concession', ConcessionController::class);

    // Academic & System
    Route::resources([
        'academicyear'    => AcademicYearController::class,
        'holiday'         => HolidayController::class,
        'timetable'       => TimetableController::class,
        'studentactivity' => StudentActivityController::class,
        'users'           => UsersController::class,
        'mocktest'        => MockTestController::class,
        'workshift'       => WorkshiftController::class,
    ]);

    Route::match(['get', 'post'], 'users/menuassign/{user}', [UsersController::class, 'MenuAssign'])->name('users.menuassign');
    Route::match(['get', 'post'], 'users/branchswitch/{user}', [UsersController::class, 'BranchSwitch'])->name('users.branchswitch');

    Route::post('attendance/store', [HolidayController::class, 'attendance_store'])->name('attendance.store');
    Route::get('/attendance', [HolidayController::class, 'attendance'])->name('attendance');
    Route::match(['get', 'post'], '/shiftwork/assign', [WorkshiftController::class, 'assign'])->name('workshift.assign');

    // Reports
    Route::prefix('report')->as('report.')->group(function () {
        Route::get('/log', [ReportController::class, 'LogReport'])->name('log');
        Route::get('/examination_log', [ReportController::class, 'ExaminationLogReport'])->name('examination_log');
        Route::get('/examination_log/students', [ReportController::class, 'ExaminationLogStudents'])->name('examination_log.students');
        Route::get('/student_response/download', [ReportController::class, 'StudentResponseDownload'])->name('student_response.download');
        Route::get('/attendance', [ReportController::class, 'AttendanceReport'])->name('attendance');
        Route::get('/monthlyattendance', [ReportController::class, 'MonthlyAttendanceReport'])->name('monthlyattendance');
        Route::get('/section_exam', [ReportController::class, 'section_exam'])->name('section_exam');
        Route::get('/batchlist', [ReportController::class, 'BatchList'])->name('batchlist');
        Route::match(['get', 'post'], '/sectionlist', [ReportController::class, 'SectionList'])->name('sectionlist');
        Route::get('/examination/analysis', [ReportController::class, 'ExaminationAnalysis'])->name('exam_analyisis');
        Route::post('/examination/leastattempted', [ReportController::class, 'LeastAttempted'])->name('leastattempted');
        Route::post('/examination/commontracktopper', [ReportController::class, 'CommonTrackTopper'])->name('commontracktopper');
        Route::post('/examination/errorlist', [ReportController::class, 'ErrorList'])->name('errorlist');
        Route::post('/examination/branchwisemarks', [ReportController::class, 'BranchWiseMarks'])->name('branchwisemarks');
        Route::post('/examination/sectionwisemarks', [ReportController::class, 'SectionWiseMarks'])->name('sectionwisemarks');
        Route::post('/examination/sectionwisetopper', [ReportController::class, 'SectionWiseTopper'])->name('sectionwisetopper');
        Route::post('/examination/subjectwisemarks', [ReportController::class, 'SubjectWiseMarks'])->name('subjectwisemarks');
        Route::post('/examination/overallmarkanalysis', [ReportController::class, 'OverallMarkAnalysis'])->name('overallmarkanalysis');
        Route::get('/hostel/roomallocation', [ReportController::class, 'RoomAllocation'])->name('roomallocation');
        Route::get('/hostel/vacate', [ReportController::class, 'HostelVacate'])->name('hostelvacate');
        Route::get('/hostel/inoutregister', [ReportController::class, 'InOutRegister'])->name('inoutregister');
        Route::get('/hostel/sickroom', [ReportController::class, 'Sickroom'])->name('sickroom');
        Route::get('/hostel/attendance', [ReportController::class, 'HostelAttendance'])->name('hostelattendance');
        Route::get('/hostel/courier', [ReportController::class, 'HostelCourier'])->name('hostelcourier');
        Route::get('/hostel/roomlist', [ReportController::class, 'HostelRoomList'])->name('hostelroomlist');
        Route::get('/hostel/sectionlist', [ReportController::class, 'HostelSectionList'])->name('hostelsectionlist');
        Route::get('/hostel-list', [ReportController::class, 'HostelList'])->name('hostellist');
        Route::post('/hostel-list/download-section-pdf', [ReportController::class, 'downloadHostelSectionPdf'])->name('hostellist.sectionpdf');
        Route::post('/hostel-list/download-room-pdf', [ReportController::class, 'downloadHostelRoomPdf'])->name('hostellist.roompdf');
        Route::get('/hostel/hostelvacancy', [ReportController::class, 'HostelVacancy'])->name('hostelvacancy');
        Route::get('/userlogin', [ReportController::class, 'UserLoginReport'])->name('userlogin');
        Route::match(['get','post'], '/report/examlog', [ReportController::class, 'examLogReport'])->name('examlog');
        Route::match(['get', 'post'], '/report/individualstudent', [ReportController::class, 'individualStudentReport'])->name('individualstudent');
        Route::match(['get', 'post'], '/report/studentReport', [ReportController::class, 'studentReport'])->name('studentreport');
        Route::get('/studentexpense', [ReportController::class, 'StudentExpense'])->name('studentexpense');
        Route::post('examination/rangereport',[ReportController::class, 'RangeReport'])->name('rangereport');
        Route::get('examination/dump', [ReportController::class, 'Dump_Report'])->name('dump');
    });
});

// ------------------------------------------------------
// 3. Student Routes (Authenticated)
// ------------------------------------------------------
Route::prefix('student')->middleware('auth:student')->group(function () {
    Route::controller(StudentController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('studentdashboard');
        Route::get('profile', 'profile')->name('student.profile');
        Route::get('marksheet', 'marksheet')->name('student.marksheet');
        Route::get('mark/subject/{test_id}', 'mark_subject')->name('student.mark_subject');
        Route::get('mark/download/{test_id}', 'mark_download')->name('student.mark_download');
        Route::get('attendance', 'attendance')->name('student.attendance');
        Route::post('logActivity', 'logActivity')->name('student.logActivity');
        Route::match(['get', 'post'], 'neetdocument', 'NeetDocument')->name('student.neetdocument');
        Route::match(['get', 'post'], 'neetscorecard', 'NeetScorecard')->name('student.neetscorecard');
        Route::get('studentdownload', 'StudentDownload')->name('student.studentdownload');
        Route::get('courierentry', 'CourierEntry')->name('student.courierentry');
    });

    Route::controller(AnnouncementController::class)->group(function () {
        Route::get('notification', 'notification')->name('student.notification');
    });

    // Student Academic/Exam Content
    Route::get('discussionvideo', [DiscussionVideoController::class, 'discussionvideo'])->name('student.discussionvideo');

    Route::get('chairmanvideo', [ChairmanVideoController::class, 'chairmanvideo'])->name('student.chairmanvideo');
    Route::get('examportion', [ExamPortionController::class, 'examportion'])->name('student.examportion');
    Route::get('answerkey', [AnswerkeyController::class, 'answerkey'])->name('student.answerkey');
    Route::get('questionkey', [QuestionKeyController::class, 'questionkey'])->name('student.questionKey');
    Route::get('download', [DownloadController::class, 'download'])->name('student.download');
    Route::get('worksheet', [WorksheetController::class, 'worksheet'])->name('student.worksheet');
    Route::get('classvideo', [ClassVideoController::class, 'classvideo'])->name('student.classvideo');
    Route::get('revisionvideo', [RevisionVideoController::class, 'revisionvideo'])->name('student.revisionvideo');
    Route::get('achievement', [AchievementController::class, 'achievement'])->name('student.achievement');
    Route::get('timetable', [TimetableController::class, 'timetable'])->name('student.timetable');

    // Student Exam Interface
    Route::controller(ExamController::class)->group(function () {
        Route::get('instruction/{test_id}', 'student_instruction')->name('student.instruction');
        Route::get('exam/{test_id}', 'student_exam')->name('student.exam');
        Route::post('exam/clearlog', 'clearlog')->name('exam.clearlog');
        Route::post('exam/save', 'Save')->name('exam.save');
        Route::get('downloadresponse', 'DownloadResponse')->name('student.downloadresponse');
    });

    Route::resource('document', StudentDocumentController::class)->only(['index', 'store', 'destroy']);
    Route::match(['get', 'post'], 'mocktest', [MockTestController::class, 'MockTest'])->name('student.mock');
    Route::get('mocktestpdf/{testname}', [MockTestController::class, 'downloadMockTestPdf'])->name('student.mocktestpdf');
    Route::get('sickroom', [SickRoomEntryController::class, 'sickroom'])->name('student.sickroom');
    Route::get('video/{id}', [ChairmanVideoController::class, 'video'])->name('video');
});

// ------------------------------------------------------
// 4. Special Handlers / Dynamic Auth
// ------------------------------------------------------

Route::post('/exam/submit', [ExamController::class, 'submit'])->name('exam.submit');

Route::get('/student/login/{user_name}/{password}/{test_id}', function ($user_name, $password, $test_id) {
    if ($student = Student::where('user_name', $user_name)->where('password', $password)->first()) {
        Auth::guard('student')->login($student);
        return redirect()->route('student.instruction', ['test_id' => base64_encode($test_id)]);
    }
    return back()->with('error', 'Invalid username or password.');
});

Route::get('/student/mocktest/login/{user_name}/{password}/{testname}', function ($user_name, $password, $testname) {
    if ($student = Student::where('user_name', $user_name)->where('password', $password)->first()) {
        Auth::guard('student')->login($student);
        return redirect()->route('student.mock', ['exam_name' => $testname]);
    }
    return back()->with('error', 'Invalid username or password.');
});
