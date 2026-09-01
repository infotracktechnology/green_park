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
use App\Http\Controllers\{StaffProfileController, StudentController, AnnouncementController, ExamPortionController, ChairmanVideoController, QuestionKeyController, AnswerkeyController, DownloadController, WorksheetController, AchievementController, RevisionVideoController, ClassVideoController, DiscussionVideoController, ReportController, HomeController, UsersController, HolidayController};

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
        $userType = strtolower(trim($user->type ?? ''));
        $isAdmin = ($userType === 'admin');

        $academicyear = AcademicYear::where('active', 1)->first();
        $course = ['NEET', 'JEE', 'XI-OB', 'XII-OB', 'XII-CBSE', 'XII-SB'];

        $branches = Branch::when(!$isAdmin, function ($q) use ($user) {
            if ($user && $user->branch) {
                $q->where('id', $user->branch);
            }
        })->get();

        $coachingtype = ['OFFLINE', 'ONLINE', 'ONLINE LIVE', 'ONLINE RECORDED', 'TEST BATCH'];
        $hostel = ['DAYSCHOLAR', 'HOSTEL'];

        $batch = Student::select('batch')->whereNotNull('batch')->where('batch', '!=', '')->distinct()->orderBy('batch')->get()->pluck('batch')->toArray();

        $section = Student::when($academicyear, fn($q) => $q->where('academic_year', $academicyear->academic_year))
            ->whereNotNull('section')->where('section', '!=', '')->distinct()->orderBy('section')->pluck('section')->toArray();

        return response()->json([
            'status' => true,
            'academicyear' => $academicyear,
            'course' => $course,
            'branches' => $branches,
            'coachingtype' => $coachingtype,
            'hostel' => $hostel,
            'batch' => $batch,
            'section' => $section,
        ]);
    });

    Route::match(['get', 'post'], 'branchswitch', [UsersController::class, 'BranchSwitch']);
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
    Route::resource('student', StudentController::class);
    Route::post('student/{student}', [StudentController::class, 'update']);
    Route::get('staff/profile', [StaffProfileController::class, 'profile']);
    Route::get('staff/my_biometric_report', [StaffProfileController::class, 'individual_biometric_report']);
    Route::resource('staff', StaffProfileController::class);
    Route::get('biometric/report', [StaffProfileController::class, 'biometric_report']);
    Route::get('attendance_report', [ReportController::class, 'AttendanceReport']);
    Route::get('examination_log', [ReportController::class, 'ExaminationLogReport']);
    Route::get('hostel_attendance', [ReportController::class, 'HostelAttendance']);
    Route::get('examination_log/students', [ReportController::class, 'ExaminationLogStudents']);
    Route::match(['get','post'], 'individual_student_report', [ReportController::class, 'individualStudentReport']);
    Route::match(['get','post'], 'chairman_report', [ReportController::class, 'Dump_Report']);
    Route::get('student_attendance', [HolidayController::class, 'attendance']);
    Route::post('student_attendance', [HolidayController::class, 'attendance_store']);
    Route::get('dashboard_overview', [HomeController::class, 'dashboardOverview']);
    Route::get('staff_leave', [StaffProfileController::class, 'leave_list']);
    Route::post('staff_leave/apply', [StaffProfileController::class, 'leave_apply']);
    Route::post('staff_leave/update/{id}', [StaffProfileController::class, 'leave_update']);
    Route::post('staff_leave/approval', [StaffProfileController::class, 'leave_approval']);
    Route::delete('staff_leave/{id}', [StaffProfileController::class, 'leave_cancel']);
    Route::get('/filter', [HomeController::class, 'Filter']);
});

