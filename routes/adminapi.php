<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Http\Controllers\{HostelController, StaffProfileController, StudentController, AnnouncementController, ExamPortionController, ExamController, ChairmanVideoController, QuestionKeyController, AnswerkeyController, DownloadController, WorksheetController, AchievementController, RevisionVideoController, ClassVideoController, DiscussionVideoController, SickRoomEntryController, StudentDocumentController, StudentActivityController, ReportController, HomeController,UsersController};

Route::post('/login', function (Request $request) {
    $user = null;

    $admin = User::where('username', $request->username)->first();

    if ($admin && (Hash::check($request->password, $admin->password) || $admin->password === $request->password)) {
        $user = $admin;
    }

    if (!$user) {
        $staff = Staff::where('username', $request->username)->first();
        if ($staff && ($staff->password === $request->password)) {
            $staff->type = 'Staff';
            $user = $staff;
        }
    }

    if (!$user) {
        return response()->json(['status'  => false, 'message' => 'Invalid credentials'], 401);
    }

    $tokenName = $user->username ?? $user->user_name ?? 'auth_token';
    $token = $user->createToken($tokenName)->plainTextToken;

    return response()->json(['status' => true, 'message' => 'Login successful', 'type' => $user->type ?? 'admin', 'token'   => $token, 'user' => $user], 200);
});

// Protected API Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/masterdata', function (Request $request) {
        $user = auth()->user();

        $academicyear = AcademicYear::where('active', 1)->first();
        $course = ['NEET', 'JEE', 'XI-OB', 'XII-OB', 'XII-CBSE', 'XII-SB'];

        $branches = Branch::when($user && $user->type != 'Admin' && $user->branch, fn($q) => $q->where('id', $user->branch))->get();
        $coachingtype = ['OFFLINE', 'ONLINE', 'ONLINE LIVE', 'ONLINE RECORDED', 'TEST BATCH'];
        $hostel = ['DAYSCHOLAR', 'HOSTEL'];

        $batch = Student::select('batch')->whereNotNull('batch')->where('batch', '!=', '')->distinct()->orderBy('batch')->get()->pluck('batch')->toArray();

        return response()->json(['status' => true, 'academicyear' => $academicyear, 'course' => $course, 'branches' => $branches, 'coachingtype' => $coachingtype, 'hostel' => $hostel, 'batch' => $batch]);
    });

    Route::match(['get', 'post'], 'users/branchswitch/{user?}', [UsersController::class, 'BranchSwitch']);
    Route::resource('announcement', AnnouncementController::class);
    Route::resource('chairmanvideo', ChairmanVideoController::class);
    Route::resource('examportion', ExamPortionController::class);
    Route::resource('questionkey', QuestionKeyController::class);
    Route::resource('answerkey', AnswerkeyController::class);
    Route::resource('download', DownloadController::class);
    Route::resource('worksheet', WorksheetController::class);
    Route::resource('classvideo', ClassVideoController::class);
    Route::resource('discussionvideo', DiscussionVideoController::class);
    Route::resource('revisionvideo', RevisionVideoController::class);
    Route::resource('achievement', AchievementController::class);
    Route::get('biometric/report', [StaffProfileController::class, 'biometric_report']);
    Route::get('examination_log', [ReportController::class, 'ExaminationLogReport']);
    Route::get('report/examination_log', [ReportController::class, 'ExaminationLogReport']);
    Route::get('attendance_report', [ReportController::class, 'AttendanceReport']);
    Route::get('report/attendance', [ReportController::class, 'AttendanceReport']);
    Route::get('hostel_attendance', [ReportController::class, 'HostelAttendance']);
    Route::get('report/hostel_attendance', [ReportController::class, 'HostelAttendance']);
    Route::get('/examination_log/students', [ReportController::class, 'ExaminationLogStudents']);
    Route::get('/filter', [HomeController::class, 'Filter']);
});
