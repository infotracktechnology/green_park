<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\{AcademicYear, Exam, ExamAnswer, Student, Announcement, Attendance, Branch, Options, Hostel, HostelRoom, InOutRegister, SickRoomEntry, HostelAttendance, HostelCourier,StudentLog,ExamSubjectReport, PhoneCard};
use Barryvdh\DomPDF\Facade\Pdf;
use App\Providers\CsvServiceProvider;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function section_exam(Request $request)
    {
        $category = Options::where('type', 'testcategory')->first()->value ?? [];
        $exams = [];

        if ($request->has('testcategory')) {
            $exams = Exam::where('testcategory', $request->testcategory)->where("academic_year", $this->academic_year)->select('name')->distinct()->orderByDesc('exam_date')->get()->pluck('name');
        }

        $test_name = $request->test_name ?? 0;

        $sections = Student::join('exam_answer as a', 'student.student_id', '=', 'a.student_id')->where([['a.testname', $test_name], ['section', '!=', ''], ['section', '!=', null]])->when(auth()->user()->branch, fn($q) => $q->where('student.campus', auth()->user()->branch))->select('section')->distinct()->orderBy('section')->get();

        if ($request->query('type') == 'overall') {
            $section = $request->section;

            $answers = ExamAnswer::selectRaw("exam_answer.*,a.student_name")->join('student as a', 'exam_answer.student_id', '=', 'a.student_id')->where([['testname', $test_name], ['section', $section], ['coaching_type', 'OFFLINE']])->when(auth()->user()->branch, fn($q) => $q->where('a.campus', auth()->user()->branch))->orderBy('student_name')->get();

            $subjects = $answers->pluck('subject')->unique()->values()->toArray();
            $results = $answers->groupBy('student_id')->map(function ($logs) use ($subjects) {
                $student = $logs->first()->student;
                $subjectStats = collect($subjects)->mapWithKeys(function ($subject) use ($logs) {
                    $sub = $logs->where('subject', $subject);
                    return [$subject => ['right' => $sub->where('mark', 4)->count(), 'wrong' => $sub->where('mark', -1)->count(), 'left'  => $sub->where('mark', 0)->count(), 'total' => $sub->sum('mark')]];
                });
                return ['student_id' => $student->student_id, 'student_name' => $student->student_name, 'test_id' => $logs->first()->test_id, 'subjects' => $subjectStats, 'total' => $subjectStats->sum('total')];
            })->values();
            return view('report.overall_print', compact('results', 'subjects', 'test_name', 'section'));
        }

        if ($request->query('type') == 'omr') {
            $section = $request->section;
            $answers = ExamAnswer::selectRaw("q_no,answer,answer_key,mark,exam_answer.student_id,a.student_name,subject")->join('student as a', 'exam_answer.student_id', '=', 'a.student_id')->where([['testname', $test_name], ['section', $section], ['coaching_type', 'OFFLINE']])->when(auth()->user()->branch, fn($q) => $q->where('a.campus', auth()->user()->branch))->orderBy('test_id')->orderBy('student_name')->get();
            $exam = Exam::where('name', $test_name)->where('academic_year', $this->academic_year)->first();
            $key_correction = $exam->key_correction ;
            return view('report.omr_print', compact('answers', 'test_name','key_correction'));
        }

        return view('report.section_exam', compact('sections', 'test_name', 'category', 'exams'));
    }

    public function LogReport(Request $request)
    {
        $students = [];
        $announcements = [];
        $exams = [];
        if ($request->has('branch')) {
            $students = Student::where('campus', $request->branch)->where('coaching_type', $request->coaching_type)->get();
            $announcements = Announcement::where('branch', 'like', '%' . $request->branch . '%')->where('coaching_type', 'like', '%' . $request->coaching_type . '%')->get()->map(function ($announcement) use ($students) {
                return ['title' => $announcement->title, 'seen' => count($announcement->student_ids), 'unseen' => $students->count() - count($announcement->student_ids)];
            });
            $exams = Exam::where('branch_id', 'like', '%' . $request->branch . '%')->where('coaching_type', 'like', '%' . $request->coaching_type . '%')->get()->map(function ($exam) use ($students) {
                $seen = DB::table('exam_answer')->where('test_id', $exam->id)->whereIn('student_id', $students->pluck('student_id')->toArray())->distinct('student_id')->count();
                return ['title' => $exam->name, 'seen' => $seen, 'unseen' => $students->count() - $seen];
            });
        }
        return view('report.logreport', compact('students', 'announcements', 'exams'));
    }

    public function ExaminationLogReport(Request $request)
    {
        $category = Options::where('type', 'testcategory')->first()->value ?? [];
        $exams = [];
        $stats = null;
        $students = [];
        $studentDetails = [];

        if ($request->has('testcategory')) {
            $exams = Exam::where('testcategory', $request->testcategory)
                ->where("academic_year", $this->academic_year)
                ->groupBy('name')->get();
        }

        $test_name = $request->examname;

        if ($test_name) {
            $exam = Exam::where('name', $test_name)->where('academic_year', $this->academic_year)->first();

            if ($exam) {
                $eligibleQuery = Student::where('academic_year', $this->academic_year);

                if ($exam->course) $eligibleQuery->where('course', $exam->course);
                if ($exam->branch) $eligibleQuery->whereIn('campus', explode(',', $exam->branch));
                if ($exam->coaching_type) $eligibleQuery->whereIn('coaching_type', explode(',', $exam->coaching_type));
                if ($exam->batch) $eligibleQuery->whereIn('batch', explode(',', $exam->batch));
                if ($exam->category) $eligibleQuery->whereIn('hostel_dayscholar', explode(',', $exam->category));
                if ($exam->gender && $exam->gender != 'All') $eligibleQuery->where('gender', $exam->gender);

                $eligibleStudentIds = $eligibleQuery->pluck('student_id')->toArray();

                $totalOnlineIds = ExamAnswer::whereIn('student_id', $eligibleStudentIds)->where('testname', $test_name)->groupBy('student_id')->pluck('student_id')->toArray();

                $startedStudentIds = ExamAnswer::where('testname', $test_name)
                    ->whereIn('student_id', $totalOnlineIds)
                    ->groupBy('student_id')->havingRaw('count(*) < ?', [$exam->total_questions])->pluck('student_id')->toArray();

                $finishedStudentIds = ExamAnswer::where('testname', $test_name)
                    ->whereIn('student_id', $totalOnlineIds)
                    ->groupBy('student_id')
                    ->havingRaw('count(*) = ?', [$exam->total_questions])
                    ->pluck('student_id')->toArray();
                
                $writingStudentIds = array_diff($startedStudentIds, $finishedStudentIds);
                $absentStudentIds = array_diff($eligibleStudentIds, $totalOnlineIds);

                $stats = [
                    'total_eligible' => count($eligibleStudentIds),
                    'total_online' => count($totalOnlineIds),
                    'total_writing' => count($writingStudentIds),
                    'total_finished' => count($finishedStudentIds),
                    'total_not_finished' => count($writingStudentIds),
                    'total_absent' => count($absentStudentIds)
                ];

                $studentDetails = [
                    'online' => Student::whereIn('student_id', $totalOnlineIds)->get(),
                    'writing' => Student::whereIn('student_id', $writingStudentIds)->get(),
                    'not_finished' => Student::whereIn('student_id', $writingStudentIds)->get(),
                    'finished' => Student::whereIn('student_id', $finishedStudentIds)->get(),
                    'absent' => Student::whereIn('student_id', $absentStudentIds)->get(),
                ];

                if ($request->filled('search')) {

                $students = Student::whereIn('student_id', $eligibleStudentIds)
                    ->where(function ($query) use ($request) {
                        $query->where('student_id', 'like', '%' . $request->search . '%')
                            ->orWhere('student_name', 'like', '%' . $request->search . '%');
                    })
                    ->get();
                }
            }
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => true,
                'category' => $category,
                'exams' => $exams,
                'stats' => $stats,
                'studentDetails' => $studentDetails,
                'test_name' => $test_name
            ], 200);
        }

        return view('report.examination_log_report', compact('category', 'exams', 'stats', 'students', 'test_name', 'studentDetails'));
    }

    public function examLogReport(Request $request)
    {
        $exams = Exam::select('id', 'name')->orderBy('id','desc')->get();
        
        if ($request->isMethod('post')) {
            $student = Student::where('student_id', $request->student_id)->first();
            if (!$student) {
                return back()->with('error','Student not found');
            }
            $exam = Exam::find($request->exam_id);
            $logs = StudentLog::where('student_id', $student->student_id)->whereRaw("action LIKE ?", ['%' . $exam->name . '%'])->orderBy('created_at')->get();

            if ($logs->isEmpty()) {
                return back()->with('error', 'This student did not attend the selected exam.');
            }
            
            $pdf = PDF::loadView('pdf.examlog', compact('student', 'logs', 'exam'));
            return $pdf->download('examlog.pdf');
        }
       return view('report.examlogreport', compact('exams'));
    }
    public function StudentResponseDownload(Request $request)
    {
        $examname = $request->examname;
        $student_id = $request->student_id;

        $exam = Exam::where('name', $examname)->where('academic_year', $this->academic_year)->first();
        if (!$exam) return back()->with('error', 'Exam not found.');

        $student = Student::where('student_id', $student_id)->first();
        if (!$student) return back()->with('error', 'Student not found.');

        $answers = ExamAnswer::where('testname', $examname)->where('student_id', $student_id)->get();

        $headers = [
            'Coaching Type',
            'Username',
            'Student Name',
            'Section',
            'Student ID',
            'Test ID',
            'Exam Name',
            'Exam Date',
        ];

        $maxQuestions = $exam->total_questions;

        for ($i = 1; $i <= $maxQuestions; $i++) {
            $headers[] = "A{$i}";
        }

        $row = [
            $student->coaching_type,
            $student->user_name,
            $student->student_name,
            $student->section,
            $student->student_id,
            $exam->id,
            $exam->name,
            $exam->exam_date,
        ];

        $answersByKey = $answers->keyBy('q_no');
        for ($i = 1; $i <= $maxQuestions; $i++) {
            $row[] = $answersByKey->get($i)->answer ?? 0;
        }

        $csvData = [$headers, $row];
        $filename = "Response_" . $student_id . "_" . $examname . ".csv";

        return response()->stream(function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $line) {
                fputcsv($file, $line);
            }
            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function AttendanceReport(Request $request)
    {
        $courses = Student::select('course')->where('academic_year', $this->academic_year)->whereNotNull('course')->where('course', '!=', '')->distinct()->orderBy('course')->get();

        $sections = Attendance::select('section')->where('academic_year', $this->academic_year)->whereNotNull('section')->where('section', '!=', '')->distinct()->orderBy('section')->get();

        $attendances = collect([]);
        if ($request->has('branch_id') && $request->filled('branch_id')) {
            $studentIds = Student::where('academic_year', $this->academic_year)
                ->where('campus', $request->branch_id)
                ->when($request->filled('course'), function ($q) use ($request) {
                    $q->where('course', $request->course);
                })
                ->when($request->filled('section'), function ($q) use ($request) {
                    $q->where('section', $request->section);
                })
                ->pluck('student_id');

            $attendanceQuery = Attendance::where('branch_id', $request->branch_id)
                ->where('academic_year', $this->academic_year);

            if ($request->filled('date')) {
                $attendanceQuery->where('attendance_date', $request->date);
            }
            if ($studentIds->isNotEmpty()) {
                $attendanceQuery->whereIn('student_id', $studentIds);
            }

            $attendancesGrouped = $attendanceQuery->get()->groupBy('section');

            $attendances = $attendancesGrouped->map(function ($attendance, $section) use ($request) {
                $present = $attendance->where('status', 'P')->unique('student_id')->count();
                $absent = $attendance->where('status', 'A')->unique('student_id')->count();

                $studentQuery = Student::where('section', $section)
                    ->where('academic_year', $this->academic_year)
                    ->where('campus', $request->branch_id);

                if ($request->filled('date')) {
                    $studentQuery->whereDate('admission_date', '<=', $request->date);
                }

                if ($request->filled('course')) {
                    $studentQuery->where('course', $request->course);
                }

                $boys = (clone $studentQuery)->where('gender', 'Male')->count();
                $girls = (clone $studentQuery)->where('gender', 'Female')->count();

                $studentNames = Student::whereIn('student_id', $attendance->where('status', 'P')->pluck('student_id')->unique())->pluck('student_name');

                $absentStudentNames = Student::whereIn('student_id', $attendance->where('status', 'A')->pluck('student_id')->unique())->pluck('student_name');

                return [
                    'section' => $section,
                    'boys' => $boys,
                    'girls' => $girls,
                    'total' => $boys + $girls,
                    'present' => $present,
                    'absent' => $absent,
                    'present_students' => $studentNames->values()->toArray(),
                    'absent_students' => $absentStudentNames->values()->toArray()
                ];
            });
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            $attendancesList = collect($attendances)->values()->map(function ($item) {
                if (is_string($item['present_students'] ?? null)) {
                    $item['present_students'] = json_decode($item['present_students'], true) ?? [];
                }
                if (is_string($item['absent_students'] ?? null)) {
                    $item['absent_students'] = json_decode($item['absent_students'], true) ?? [];
                }
                return $item;
            });

            return response()->json([
                'status' => true,
                'courses' => $courses->pluck('course'),
                'sections' => $sections->pluck('section'),
                'attendances' => $attendancesList
            ], 200);
        }

        return view('report.attendancereport', compact('attendances', 'sections', 'courses'));
    }

    public function MonthlyAttendanceReport(Request $request)
    {
    $branches = Branch::all();
    
    $sections = Student::when($request->branch_id, function($q) use ($request) { 
        return $q->where('campus', $request->branch_id); 
    })->whereNotNull('section')->where('section', '!=', '')->distinct()->pluck('section');

    $courses = Student::where('academic_year', $this->academic_year)->where('course', '!=', '')->distinct()->pluck('course');

    $report_type = $request->input('report_type');
    $data = [];
    $summary = null;

    if ($request->has('branch_id') && $request->filled('start_date') && $request->filled('end_date') && $report_type) {
        
        $query = Attendance::where('branch_id', $request->branch_id)
            ->where('academic_year', $this->academic_year)
            ->whereBetween('attendance_date', [$request->start_date, $request->end_date]);

        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }

        if ($request->filled('course')) {
            $studentIds = Student::where('course', $request->course)
                ->where('campus', $request->branch_id)
                ->where('academic_year', $this->academic_year)
                ->wherein('coaching_type', ['OFFLINE', 'ONLINE LIVE'])
                ->pluck('student_id'); 
            $query->whereIn('student_id', $studentIds);
        }

     
        // 1. Section Wise
        if ($report_type == 'section') {
            $attendances = $query->get()->groupBy('section');
            $data = $attendances->map(function ($attendance, $section) use ($request) {
                $present = $attendance->where('status', 'P')->count() * 0.5;
                $absent = $attendance->where('status', 'A')->count() * 0.5;

                $boysQuery = Student::where('section', $section)
                    ->where('academic_year', $this->academic_year)
                    ->where('campus', $request->branch_id)
                    ->wherein('coaching_type', ['OFFLINE', 'ONLINE LIVE'])
                    ->where('gender', 'Male')
                    ->whereDate('admission_date', '<=', $request->end_date);
                if ($request->filled('course')) $boysQuery->where('course', $request->course);
                $boys = $boysQuery->count();

                $girlsQuery = Student::where('section', $section)
                    ->where('academic_year', $this->academic_year)
                    ->where('campus', $request->branch_id)
                    ->wherein('coaching_type', ['OFFLINE', 'ONLINE LIVE'])
                    ->where('gender', 'Female')
                    ->whereDate('admission_date', '<=', $request->end_date);
                if ($request->filled('course')) $girlsQuery->where('course', $request->course);
                $girls = $girlsQuery->count();

                $totalMarked = $present + $absent;
                $present_percentage = $totalMarked > 0 ? round(($present * 100) / $totalMarked, 2) : 0;
                $absent_percentage = $totalMarked > 0 ? round(($absent * 100) / $totalMarked, 2) : 0;

                return [
                    'section' => $section, 'boys' => $boys, 'girls' => $girls, 'total' => $boys + $girls,
                    'present' => $present, 'absent' => $absent,
                    'present_percentage' => $present_percentage, 'absent_percentage' => $absent_percentage
                ];
            });
        }

        // 2. Student Wise
        elseif ($report_type == 'student') {
            $attendances = $query->get()->groupBy('student_id');
            $studentIds = $attendances->keys();
            $students = Student::withTrashed()->whereIn('student_id', $studentIds)->get()->keyBy('student_id');

            $data = $attendances->map(function ($attendance, $student_id) use ($students) {
                $student = $students->get($student_id);
                $present = $attendance->where('status', 'P')->count() * 0.5;
                $absent = $attendance->where('status', 'A')->count() * 0.5;
                $holidays = $attendance->where('status', 'H')->count() * 0.5;
                $totalMarked = $present + $absent ;

                return [
                    'student_id' => $student_id,
                    'student_name' => $student->student_name ?? 'N/A',
                    'course' => $student->course ?? 'N/A',
                    'section' => $student->section ?? 'N/A',
                    'working_days' => $totalMarked ,
                    'present' => $present, 'absent' => $absent, 'holidays' => $holidays,
                    'attendance_percentage' => $totalMarked > 0 ? round(($present / $totalMarked) * 100, 2) : 0
                ];
            });
        }

        // 3. Course Wise
        elseif ($report_type == 'course') {
            $attendances = $query->get();
            $student_ids = $attendances->pluck('student_id')->unique();
            $students = Student::whereIn('student_id', $student_ids)->where('academic_year', $this->academic_year)->get()->keyBy('student_id');
            

            $grouped = $attendances->filter(function ($item) use ($students) {
                return $students->has($item->student_id);
            })->groupBy(function ($item) use ($students) {
                return $students[$item->student_id]->course;
            });

            $data = $grouped->map(function ($attendance, $course) use ($request) {
                $present = $attendance->where('status', 'P')->count() * 0.5;
                $absent = $attendance->where('status', 'A')->count() * 0.5;
                $totalMarked = $present + $absent;

                $total_students = Student::where('course', $course)
                    ->where('campus', $request->branch_id)
                    ->wherein('coaching_type', ['OFFLINE', 'ONLINE LIVE'])
                    ->where('academic_year', $this->academic_year)
                    ->count();

                return [
                    'course' => $course, 'total_students' => $total_students,
                    'present' => $present, 'absent' => $absent,
                    'attendance_percentage' => $totalMarked > 0 ? round(($present / $totalMarked) * 100, 2) : 0
                ];
            });
        }

        // 4. Branch Wise
        elseif ($report_type == 'branch') {
            $attendances = $query->get()->groupBy('branch_id');
            $branchesMap = Branch::pluck('name', 'id');

            $data = $attendances->map(function ($attendance, $branch_id) use ($branchesMap, $request) {
                $present = $attendance->where('status', 'P')->count() * 0.5;
                $absent = $attendance->where('status', 'A')->count() * 0.5;
                $totalMarked = $present + $absent;

                $totalStudentQuery = Student::where('campus', $branch_id)
                    ->where('academic_year', $this->academic_year)
                    ->whereIn('coaching_type', ['OFFLINE', 'ONLINE LIVE']);

                if ($request->filled('course')) {
                    $totalStudentQuery->where('course', $request->course);
                }
                if ($request->filled('section')) {
                    $totalStudentQuery->where('section', $request->section);
                }

                $total_students = $totalStudentQuery->count();

                return [
                    'branch' => $branchesMap->get($branch_id) ?? 'N/A',
                    'total_students' => $total_students,
                    'present' => $present, 'absent' => $absent,
                    'attendance_percentage' => $totalMarked > 0 ? round(($present / $totalMarked) * 100, 2) : 0
                ];
            });
        }

        // 5. Month Wise
        elseif ($report_type == 'month') {
            $attendances = $query->get()->groupBy('attendance_date');
            $student_ids = $query->get()->pluck('student_id')->unique();
            $studentNames = Student::whereIn('student_id', $student_ids)->pluck('student_name', 'student_id');

            $data = $attendances->map(function ($attendance, $date) use ($studentNames) {
                $presentStudents = $attendance->where('status', 'P')->map(fn($a) => $studentNames->get($a->student_id) ?? $a->student_id)->unique()->values();
                $absentStudents = $attendance->where('status', 'A')->map(fn($a) => $studentNames->get($a->student_id) ?? $a->student_id)->unique()->values();

                return [
                    'date' => $date,
                    'present_count' => $attendance->where('status', 'P')->count() * 0.5,
                    'absent_count' => $attendance->where('status', 'A')->count() * 0.5,
                    'present_students' => json_encode($presentStudents),
                    'absent_students' => json_encode($absentStudents)
                ];
            });
        }
    }

    return view('report.monthlyattendancereport', compact('branches', 'sections', 'courses', 'report_type','summary', 'data' ));
}

    public function BatchList(Request $request)
    {
        $report = Student::join('branch as b', 'student.campus', '=', 'b.id')->selectRaw("b.name as campus,hostel_dayscholar,batch,section,COUNT(*) as strength,b.id")->where('section', '!=', '')->where('hostel_dayscholar', '!=', '')->where('academic_year', $this->academic_year)->groupBy('b.name', 'hostel_dayscholar', 'batch', 'section')->orderBy('b.id')->get();
        return view('report.batchlist', compact('report'));
    }
    public function SectionList(Request $request)
    {
        $branch = $request->branch ?? 0;
        $course = $request->course ?? 0;
        $data = Student::join('branch as b', 'student.campus', '=', 'b.id')->selectRaw("b.name as campus,batch,section,COUNT(*) as total,b.id,concat(gender,'-',hostel_dayscholar)gender,sum(ac_nonac='AC')ac,sum(ac_nonac='NON AC')nonac,sum(board_of_study_XII_std='SB')sb,sum(board_of_study_XII_std='CBSE')cbse,hostel_dayscholar, coaching_type")->where('academic_year', $this->academic_year)->where('b.id', $branch)->where('course', $course)->groupBy('section', 
        'coaching_type')->orderBy('hostel_dayscholar')->orderByRaw("FIELD(gender, 'Male', 'Female')")->orderByRaw("REGEXP_SUBSTR(section, '^[^0-9]+')")->orderByRaw("CAST(REGEXP_SUBSTR(section, '[0-9]+$') AS UNSIGNED)")->get();

        $offline = $data->where('coaching_type', 'OFFLINE')->groupBy('gender');
        $online = $data->whereIn('coaching_type', [
        'ONLINE',
        'ONLINE LIVE',
        'ONLINE RECORDED',
        'TEST BATCH'
    ])
    ->groupBy('gender');

        $grouped = $data->groupBy(['gender']);

        if ($request->isMethod('post')) {
            $section = $request->section;
            $students = Student::where('section', $section)->where('academic_year', $this->academic_year)->get();
            $branchname = $request->branchname;
            $pdf = Pdf::loadView("pdf.$request->view", compact('students', 'branchname', 'section'));
            return $pdf->download("$section-$request->view.pdf");
        }

        return view('report.sectionlist', compact('grouped', 'offline', 'online'));
    }
    public function ExaminationAnalysis(Request $request)
    {
        $category = Options::where('type', 'testcategory')->first()->value ?? [];
        $exams = [];

        if ($request->has('testcategory')) {
            $exams = Exam::where('testcategory', $request->testcategory)->where("academic_year", $this->academic_year)->select('name')->distinct()->get()->pluck('name');
        }

        return view('report.examinationanalysis', compact('category', 'exams'));
    }
    public function LeastAttempted(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();

        if (!$exam) return back()->with('error', 'Exam not found.');

        $answers = ExamAnswer::where('testname', $request->test_name)->selectRaw("q_no, COUNT(student_id) AS total, SUM(answer > 0) AS least")->groupBy('q_no')->get();

        $csvData = [['Title', 'Least Attempted Questions'], ['Exam Name', $exam->name], [], ['S.NO', 'Q.NO', 'PERCENTAGE']];

        foreach ($answers as $key => $a) $csvData[] = [$key + 1, $a->q_no, round(($a->least / $a->total) * 100, 2)];

        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="Least_Attempted_Questions.csv"']);
    }

    public function CommonTrackTopper(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();

        if (!$exam) return back()->with('error', 'Exam not found.');

        $subjects = array_map('trim', explode(',', strtoupper($exam->subject_name)));

        $sumExp = collect($subjects)->map(fn($s) => "SUM(IF(subject='$s', mark, 0)) AS `$s`")->implode(',');

        $answers = ExamAnswer::where('testname', $request->test_name)->selectRaw("student_id, SUM(mark) AS total, $sumExp")->groupBy('student_id')->orderByDesc('total')->get();

        $csvData = [['Title', 'Common Track Topper'], ['Exam Name', $exam->name], [], array_merge(['Student ID', 'Student Name', 'Branch', 'Exam Date', 'Batch'], $subjects, ['Total'])];
        foreach ($answers as $a) {
            $row = [$a->student_id, $a->student?->student_name, $a->student?->branch?->name, $exam->exam_date, $a->student?->batch];
            foreach ($subjects as $s) $row[] = $a->$s ?? 0;
            $row[] = $a->total;
            $csvData[] = $row;
        }
        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="SUbject_Wise_Marks.csv"']);
    }

    public function ErrorList(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();

        if (!$exam) return back()->with('error', 'Exam not found.');

        $subjects = array_map('trim', explode(',', strtoupper($exam->subject_name)));

        $expr = collect($subjects)->map(fn($s) => "GROUP_CONCAT(IF(mark=-1 AND subject='$s', q_no, NULL) SEPARATOR '|') AS `{$s}WRONGLY`, GROUP_CONCAT(IF(mark=0 AND subject='$s', q_no, NULL) SEPARATOR '|') AS `{$s}NOT ATTEMPTED`")->implode(',');
        $answers = ExamAnswer::where('testname', $request->test_name)->selectRaw("student_id, SUM(mark) AS total, $expr")->groupBy('student_id')->orderByDesc('total')->get();

        $headers = [];
        foreach ($subjects as $s) {
            $headers[] = "{$s} WRONGLY";
            $headers[] = "{$s} NOT ATTEMPTED";
        }
        $csvData = [['Title', 'Error List'], ['Exam Name', $exam->name], [], array_merge(['Student ID', 'Student Name', 'Branch', 'Exam Date', 'Batch', 'Total'], $headers)];

        foreach ($answers as $a) {
            $row = [$a->student_id, $a->student?->student_name, $a->student?->branch?->name, $exam->exam_date, $a->student?->batch, $a->total];
            foreach ($subjects as $s) {
                $row[] = $a->{"{$s}WRONGLY"} ?? '';
                $row[] = $a->{"{$s}NOT ATTEMPTED"} ?? '';
            }
            $csvData[] = $row;
        }


        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="Error_List.csv"']);
    }

    public function SectionWiseTopper(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();

        if (!$exam) return back()->with('error', 'Exam not found.');

        $subjects = array_map('trim', explode(',', strtoupper($exam->subject_name)));

        $expr = collect($subjects)->map(fn($s) => "SUM(IF(subject='$s' AND mark=4,1,0)) AS `{$s}_CORRECT`,SUM(IF(subject='$s' AND mark=-1,1,0)) AS `{$s}_WRONG`,SUM(IF(subject='$s' AND mark=0,1,0)) AS `{$s}_UNATTEMPTED`,COUNT(IF(subject='$s',1,NULL)) AS `{$s}_TOTAL`,SUM(IF(subject='$s',mark,0)) AS `{$s}_MARK`")->implode(',');


        $answers = ExamAnswer::join('student as s', 'exam_answer.student_id', '=', 's.student_id')->where('testname', $request->test_name)->selectRaw("exam_answer.student_id, s.student_name, s.campus, s.batch, s.section,SUM(IF(mark=4,1,0)) AS overall_correct,SUM(IF(mark=-1,1,0)) AS overall_wrong,SUM(IF(mark=0,1,0)) AS overall_unattempted,COUNT(*) AS overall_total,SUM(mark) AS total,$expr")->where('s.section', '!=', '')->groupBy('exam_answer.student_id')->orderBy('s.section')->orderByDesc('total')->get();

        $csvHeaders = ['Section', 'Student ID', 'Student Name', 'Branch', 'Batch', 'Exam Date', 'Overall Correct', 'Overall Wrong', 'Overall UnAttempted', 'Overall Total', 'Overall Percentage'];

        foreach ($subjects as $s) {
            $csvHeaders = array_merge($csvHeaders, ["{$s} Correct", "{$s} Wrong", "{$s} UnAttempted", "{$s} Total", "{$s} Percentage"]);
        }

        $csvData = [
            ['Title', 'Section Wise Topper'],
            ['Exam Name', $exam->name],
            [],
            $csvHeaders
        ];

        $sectionToppers = $answers->groupBy('section')->map(fn($group) => $group->sortByDesc('total')->first());

        foreach ($sectionToppers as $a) {
            $branch = Branch::find($a->campus);
            $overallPct = round(($a->overall_correct / $a->overall_total) * 100, 2);
            $row = [$a->section, $a->student_id, $a->student_name, $branch?->name, $a->batch, $exam->exam_date, $a->overall_correct, $a->overall_wrong, $a->overall_unattempted, $a->total, $overallPct];
            foreach ($subjects as $s) {
                $mark = $a->{"{$s}_CORRECT"} ?? 0;
                $total = $a->{"{$s}_TOTAL"} ?: 0;
                $total_mark = $a->{"{$s}_MARK"} ?: 0;
                $per = round(($mark / $total) * 100, 2);
                array_push($row, $mark, $a->{"{$s}_WRONG"} ?? 0, $a->{"{$s}_UNATTEMPTED"} ?? 0, $total_mark, $per);
            }
            $csvData[] = $row;
        }

        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="Section_Wise_Topper.csv"']);
    }

    public function SubjectWiseMarks(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();

        if (!$exam) return back()->with('error', 'Exam not found.');

        $subjects = array_map('trim', explode(',', strtoupper($exam->subject_name)));

        $expr = collect($subjects)->map(fn($s) => "SUM(IF(subject='$s' AND mark=4,1,0)) AS `{$s}_CORRECT`,SUM(IF(subject='$s' AND mark=-1,1,0)) AS `{$s}_WRONG`,SUM(IF(subject='$s' AND mark=0,1,0)) AS `{$s}_UNATTEMPTED`,COUNT(IF(subject='$s',1,NULL)) AS `{$s}_TOTAL`,SUM(IF(subject='$s',mark,0)) AS `{$s}_MARK`")->implode(',');

        $answers = ExamAnswer::where('testname', $request->test_name)->selectRaw("student_id,SUM(IF(mark=4,1,0)) AS overall_correct,SUM(IF(mark=-1,1,0)) AS overall_wrong,SUM(IF(mark=0,1,0)) AS overall_unattempted,COUNT(*) AS overall_total,SUM(mark) AS total,$expr")->groupBy('student_id')->orderByDesc('total')->get();

        $overallRanks = $answers->sortByDesc('total')->values()->mapWithKeys(fn($a, $i) => [$a->student_id => $i + 1]);

        $subjectRanks = [];
        foreach ($subjects as $s) {
            $subjectRanks[$s] = $answers->sortByDesc("{$s}_MARK")->values()->mapWithKeys(fn($a, $i) => [$a->student_id => $i + 1]);
        }

        $csvHeaders = ['Student ID', 'Student Name', 'Branch', 'Batch', 'Section', 'Exam Date', 'Overall Correct', 'Overall Wrong', 'Overall UnAttempted', 'Overall Total', 'Overall Percentage', 'Overall Rank'];
        foreach ($subjects as $s) {
            $csvHeaders = array_merge($csvHeaders, ["{$s} Correct", "{$s} Wrong", "{$s} UnAttempted", "{$s} Total", "{$s} Percentage", "{$s} Rank"]);
        }
        $csvData = [['Title', 'Subject Wise Marks'], ['Exam Name', $exam->name], [], $csvHeaders];
        foreach ($answers as $a) {
            $overallPct = round(($a->overall_correct / $a->overall_total) * 100, 2);
            $row = [$a->student_id, $a->student?->student_name, $a->student?->branch?->name, $a->student?->batch, $a->student?->section, $exam->exam_date, $a->overall_correct, $a->overall_wrong, $a->overall_unattempted, $a->total, $overallPct, $overallRanks[$a->student_id] ?? ''];
            foreach ($subjects as $s) {
                $mark = $a->{"{$s}_CORRECT"} ?? 0;
                $total = $a->{"{$s}_TOTAL"} ?: 0;
                $total_mark = $a->{"{$s}_MARK"} ?: 0;
                $per = $total ? round(($mark / $total) * 100, 2) : 0;
                array_push($row, $mark, $a->{"{$s}_WRONG"} ?? 0, $a->{"{$s}_UNATTEMPTED"} ?? 0, $total_mark, $per, $subjectRanks[$s][$a->student_id] ?? '');
            }
            $csvData[] = $row;
        }
        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="Subject_Wise_Marks.csv"']);
    }

    public function BranchWiseMarks(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();
        if (!$exam) return back()->with('error', 'Exam not found.');
        $testcategory = strtoupper($exam->testcategory);

        $ranges = match ($testcategory) {
            'GRAND TEST' => [[700, 720], [651, 659], [601, 650], [551, 600], [501, 550], [451, 500], [351, 450], [251, 350], [151, 250], [1, 150]],
            'WEEKEND (PHYSICS)' => [[91, 120], [61, 90], [31, 60], [1, 30]],
            'WEEKEND (CHEMISTRY)' => [[141, 180], [101, 140], [51, 100], [1, 50]],
            'WEEKEND (BOTANY)' => [[221, 240], [201, 220], [181, 200], [121, 180], [61, 120], [1, 60]],
            'WEEKEND (ZOOLOGY)' => [[221, 240], [201, 220], [181, 200], [121, 180], [61, 120], [1, 60]],
            'CUMULATIVE (PHYZOO)' => [[351, 400], [301, 350], [251, 300], [201, 250], [151, 200], [101, 150], [1, 100]],
            'CUMULATIVE (CHEBOT)' => [[351, 400], [301, 350], [251, 300], [201, 250], [151, 200], [101, 150], [1, 100]],
            default => [[101, 120], [81, 100], [61, 80], [41, 60], [21, 40], [1, 20]],
        };


        $rangeExprs = collect($ranges)->map(function ($r) {
            [$low, $high] = $r;
            return "sum(if(c.total BETWEEN {$low} AND {$high}, 1, 0)) AS `{$high}-{$low}`";
        })->implode(',');

        $csvHeaders = ['S.No', 'Branch Name', 'Actual STR', 'Appeared STR', 'AB', 'Max Marks', 'Min Marks'];
        foreach ($ranges as $r) {
            $csvHeaders = array_merge($csvHeaders, ["{$r[0]}-{$r[1]}"]);
        }

        $studentmark = ExamAnswer::where('testname', $request->test_name)->selectRaw("student_id,SUM(mark) AS total")->groupBy('student_id');

        $results = Branch::join('student as b', 'branch.id', '=', 'b.campus')->leftJoinSub($studentmark, 'c', fn($join) => $join->on('c.student_id', '=', 'b.student_id'))->selectRaw("branch.name,count(b.student_id)actual_str,count(c.student_id)appeared_str,max(c.total)max_marks,min(c.total)min_marks,$rangeExprs")->groupBy('branch.name')->orderBy('branch.name')->get();

        $csvData = [
            ['Title', 'Branch Wise Marks'],
            ['Exam Name', $exam->name],
            [],
            $csvHeaders
        ];
        foreach ($results as $k => $r) {
            $row = [$k + 1, $r->name, $r->actual_str, $r->appeared_str, $r->actual_str - $r->appeared_str, $r->max_marks, $r->min_marks];
            foreach ($ranges as $range) {
                $row[] = $r->{"{$range[0]}-{$range[1]}"} ?? 0;
            }
            $csvData[] = $row;
        }
        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="Branch_Wise_Marks_Analysis.csv"']);
    }

    public function SectionWiseMarks(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();
        if (!$exam) return back()->with('error', 'Exam not found.');
        $testcategory = strtoupper($exam->testcategory);

        $ranges = match ($testcategory) {
            'GRAND TEST' => [[700, 720], [651, 659], [601, 650], [551, 600], [501, 550], [451, 500], [351, 450], [251, 350], [151, 250], [1, 150]],
            'WEEKEND (PHYSICS)' => [[91, 120], [61, 90], [31, 60], [1, 30]],
            'WEEKEND (CHEMISTRY)' => [[141, 180], [101, 140], [51, 100], [1, 50]],
            'WEEKEND (BOTANY)' => [[221, 240], [201, 220], [181, 200], [121, 180], [61, 120], [1, 60]],
            'WEEKEND (ZOOLOGY)' => [[221, 240], [201, 220], [181, 200], [121, 180], [61, 120], [1, 60]],
            'CUMULATIVE (PHYZOO)' => [[351, 400], [301, 350], [251, 300], [201, 250], [151, 200], [101, 150], [1, 100]],
            'CUMULATIVE (CHEBOT)' => [[351, 400], [301, 350], [251, 300], [201, 250], [151, 200], [101, 150], [1, 100]],
            default => [[101, 120], [81, 100], [61, 80], [41, 60], [21, 40], [1, 20]],
        };

        $rangeExprs = collect($ranges)->map(function ($r) {
            [$low, $high] = $r;
            return "sum(if(c.total BETWEEN {$low} AND {$high}, 1, 0)) AS `{$low}-{$high}`";
        })->implode(',');

        $csvHeaders = ['S.No', 'Section', 'Actual STR', 'Appeared STR', 'AB', 'Max Marks', 'Min Marks'];
        foreach ($ranges as $r) {
            $csvHeaders = array_merge($csvHeaders, ["{$r[0]}-{$r[1]}"]);
        }

        $studentmark = ExamAnswer::where('testname', $request->test_name)->selectRaw("student_id,SUM(mark) AS total")->groupBy('student_id');

        $results = Student::joinSub($studentmark, 'c', fn($join) => $join->on('c.student_id', '=', 'student.student_id'))->selectRaw("student.section,count(student.student_id)actual_str,count(c.student_id)appeared_str,max(c.total)max_marks,min(c.total)min_marks,$rangeExprs")->where('student.section', '!=', '')->groupBy('student.section')->orderBy('student.section')->get();

        $csvData = [
            ['Title', 'Section Wise Marks'],
            ['Exam Name', $exam->name],
            [],
            $csvHeaders
        ];
        foreach ($results as $k => $r) {
            $row = [$k + 1, $r->section, $r->actual_str, $r->appeared_str, $r->actual_str - $r->appeared_str, $r->max_marks, $r->min_marks];
            foreach ($ranges as $range) {
                $row[] = $r->{"{$range[0]}-{$range[1]}"} ?? 0;
            }
            $csvData[] = $row;
        }
        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="Section_Wise_Marks_Analysis.csv"']);
    }
    public function OverallMarkAnalysis(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();

        if (!$exam) return back()->with('error', 'Exam not found.');

        $subjects = array_map('trim', explode(',', strtoupper($exam->subject_name)));

        $expr = collect($subjects)->map(fn($s) => "SUM(IF(subject='$s' AND mark=4,1,0)) AS `{$s}_CORRECT`,SUM(IF(subject='$s' AND mark=-1,1,0)) AS `{$s}_WRONG`,SUM(IF(subject='$s' AND mark=0,1,0)) AS `{$s}_UNATTEMPTED`,SUM(IF(subject='$s',mark,0)) AS `{$s}_MARK`")->implode(',');

        $answers = ExamAnswer::where('testname', $request->test_name)->selectRaw("student_id,mode,SUM(IF(mark=4,1,0)) AS overall_correct,SUM(IF(mark=-1,1,0)) AS overall_wrong,SUM(IF(mark=0,1,0)) AS overall_unattempted,COUNT(*) AS overall_total,SUM(mark) AS total,$expr")->groupBy('student_id')->orderByDesc('total')->get();



        $csvHeaders = ['SID', 'MODE', 'STUDENT NAME', 'CAMPUS','GENDER','CTYPE','OVERALL CORRECT', 'OVERALL WRONG', 'OVERALL UNATTEMPTED', 'OVERALL TOTAL'];

        foreach ($subjects as $s) {
            $csvHeaders = array_merge($csvHeaders, ["{$s} Correct", "{$s} Wrong", "{$s} UnAttempted", "{$s} Total"]);
        }

        $csvData = [['Title', 'Subject Wise Marks'], ['Exam Name', $exam->name], [], $csvHeaders];
        foreach ($answers as $a) {
            $row = [$a->student_id, $a->mode, $a->student?->student_name, $a->student?->branch?->name,$a->student?->gender, $a->student?->coaching_type,$a->overall_correct, $a->overall_wrong, $a->overall_unattempted, $a->total];
            foreach ($subjects as $s) {
                $row = array_merge($row, [$a->{"{$s}_CORRECT"} ?? 0, $a->{"{$s}_WRONG"} ?? 0, $a->{"{$s}_UNATTEMPTED"} ?? 0, $a->{"{$s}_MARK"} ?? 0]);
            }
            $csvData[] = $row;
        }

        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="Overall_Marks_Analysis.csv"']);
    }
    public function RangeReport(Request $request)
    {
      $test_name = $request->test_name;

    if (!$test_name) {
        return back()->with('error', 'Please select exam name.');
    }

    $results = DB::table('exam_answer as ea')
        ->join('student as s', 's.student_id', '=', 'ea.student_id')
        ->where('ea.testname', $test_name)
        ->where('ea.academic_year', $this->academic_year)
        ->select('ea.student_id','s.student_name','s.coaching_type')
        ->selectRaw('SUM(COALESCE(ea.mark, 0)) as total_mark')
        ->groupBy('ea.student_id','s.student_name','s.coaching_type')
        ->orderByDesc('total_mark')
        ->get();
        
        $coachingTypes = $results
        ->pluck('coaching_type')
        ->filter()
        ->map(function ($type) {
            return strtoupper(trim($type));
        })
        ->unique()
        ->implode(' / ');
        
        $firstMark = $results->max(function ($student) {
            return (float) $student->total_mark;
        });

        $rangeMarks = $request->input('range_mark', []);
        $rangeMarks = collect($rangeMarks)->filter(function ($mark) {
            return is_numeric($mark);
            })
        ->map(function ($mark) {
            return (float) $mark;
        })->unique()->sortDesc()->values();

        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $test_name)->first();

        if (!$exam) {
            return back()->with('error', 'Exam not found.');
        }

        $totalMark = (int) $exam->total_questions * 4;

                $rangeReport = collect();
                    foreach ($rangeMarks as $range) {
                        $count = $results
                            ->filter(function ($student) use ($range) {
                                return (float) $student->total_mark >= $range;
                            })->count();

                if ($range == $totalMark) {
                $rangeText = $range . ' / ' . $totalMark;
            } else {
                $rangeText = $range . ' AND ABOVE';
            }

            $rangeReport->push([
                'range' => $rangeText,
                'count' => $count,
            ]);
    }

    $pdf = Pdf::loadView('pdf.range_report', ['test_name'   => $test_name,'firstMark'   => $firstMark,'rangeReport' => $rangeReport,'totalMark'   => $totalMark, 'coachingTypes' => $coachingTypes ]);

    return $pdf->download('Range_Report_' . $test_name . '.pdf');    
    }
    public function Dump_Report(Request $request)
    {
        $tests = Exam::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($query) { $query->where('branch_id','like','%' . auth()->user()->branch . '%'); })->select('name')->distinct()->orderBy('name')->get();

        $test_name = $request->test_name ?? null;
        $test_ids = '';
        $results = collect();
        $subjects = [];
        $marks = collect();
        $totalMarks = 0;
        $allOffline = false;

        if ($test_name) {
            $exam = Exam::where('academic_year', $this->academic_year)->where('name', $test_name)->first();

            if ($exam) {
                $examCoachingType = ($exam->coaching_type ?? '');

                $allOffline = ($examCoachingType === 'OFFLINE');
                $subjects = array_values(array_filter(array_map('trim', explode(',', $exam->subject_name ?? ''))));
                $test_ids = Exam::where('academic_year', $this->academic_year)->where('name', $test_name)->pluck('testid')->implode(',');

                $results = DB::table('exam_answer as ea')->join('student as s','s.student_id','=','ea.student_id')
                    ->leftJoin('branch as b','b.id','=','s.campus')
                    ->where('ea.testname', $test_name)
                    ->where('ea.academic_year', $this->academic_year)
                    ->select('ea.test_id','ea.student_id','ea.mode as stmode','s.student_name','s.gender','s.coaching_type','s.section','s.batch',
                    DB::raw("SUBSTRING_INDEX(b.campus, ',', 1) as campus"))
                    ->selectRaw('SUM(COALESCE(ea.mark, 0)) as mark')
                    ->groupBy('ea.test_id','ea.student_id','ea.mode','s.student_name','s.gender','s.coaching_type','s.section','b.campus','s.batch')
                    ->orderByDesc('mark')
                    ->orderBy('s.student_name')
                    ->get();
                $testIdArray = array_filter(
                    array_map('trim', explode(',', $test_ids))
                );

                if (!empty($testIdArray)) {
                    $totalQuestions = DB::table('exam_answer')
                        ->where('academic_year', $this->academic_year)
                        ->where('testname', $test_name)
                        ->distinct('q_no')
                        ->count('q_no');

                    $totalMarks = $totalQuestions * 4;

                    $marks = DB::table('exam_answer')->where('academic_year', $this->academic_year)->where('testname', $test_name)->select('student_id', 'subject')->selectRaw('SUM(mark = 4) as r')->selectRaw('SUM(mark = -1) as w')->selectRaw('SUM(mark = 0) as l')->selectRaw('SUM(mark) as tot')->groupBy('student_id', 'subject')->get()
                        ->keyBy(function ($row) {
                            return $row->student_id . '|' . strtoupper(trim($row->subject));
                        });
                }   
            }
        }

        if ($request->wantsJson() || $request->is('admin/*') || $request->ajax()) {
            $formattedResults = $results->map(function ($result, $index) use ($subjects, $marks) {
                $subjectMarks = [];
                foreach ($subjects as $subject) {
                    $key = $result->student_id . '|' . strtoupper(trim($subject));
                    $mark = $marks->get($key);
                    $subjectMarks[$subject] = [
                        'r' => (int) ($mark->r ?? 0),
                        'w' => (int) ($mark->w ?? 0),
                        'l' => (int) ($mark->l ?? 0),
                        'tot' => (int) ($mark->tot ?? 0),
                    ];
                }

                return [
                    's_no' => $index + 1,
                    'student_id' => $result->student_id,
                    'student_name' => $result->student_name,
                    'gender' => $result->gender,
                    'campus' => $result->campus,
                    'section' => $result->section,
                    'batch' => $result->batch,
                    'stmode' => $result->stmode,
                    'coaching_type' => $result->coaching_type,
                    'mark' => (int) $result->mark,
                    'subject_marks' => $subjectMarks,
                ];
            });

            return response()->json([
                'status' => true,
                'tests' => $tests->pluck('name')->filter()->values(),
                'test_name' => $test_name,
                'all_offline' => $allOffline,
                'total_marks' => $totalMarks,
                'subjects' => $subjects,
                'results' => $formattedResults,
            ]);
        }

        return view('exam.dump_report', compact( 'tests','test_name','test_ids','results','subjects','marks', 'totalMarks', 'allOffline'));
    }
    
    public function RoomAllocation(Request $request)
    {
        $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : collect();
        $room = $request->hostel ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no') : collect();
        $student = collect();
        if ($request->filled('hostel')) {
            $student = Student::where('hostel_id', $request->hostel)->where('academic_year', $this->academic_year)
                ->when($request->room != 'all' && $request->filled('room'), function ($q) use ($request) {
                    $q->where('room_no', $request->room);
                })
                ->when($request->filled('from_date') && $request->filled('to_date'), function ($q) use ($request) {
                    $q->whereBetween('allocation_date', [
                        $request->from_date,
                        $request->to_date
                    ]);
                })
                ->orderBy('allocation_date', 'asc')
                ->get();
        }
        return view('report.roomallocation', compact('hostels', 'room', 'student'));
    }

    public function InOutRegister(Request $request)
    {
        $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : collect();
        $room = $request->hostel ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no') : collect();
        $register = $request->room ? InOutRegister::where('hostel_id', $request->hostel)->when($request->room != 'all', fn($q) => $q->where('room_no', $request->room))->when($request->filled('from_date'),
         function ($q) use ($request) {$q->whereDate('created_at', '>=', $request->from_date); })->when($request->filled('to_date'), function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to_date);
        })
        ->with('student')
        ->get() : collect();
        return view('report.inoutregister', compact('hostels', 'room', 'register'));
    }

    public function Sickroom(Request $request)
    {
        $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : collect();
        $room = $request->hostel ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no') : collect();
        $sickroom = $request->room
            ? SickRoomEntry::where('hostel_id', $request->hostel)
                ->when($request->room != 'all', fn($q) => $q->where('room_no', $request->room))
                ->when($request->filled('from_date'), function ($q) use ($request) {
                    $q->whereDate('in_time', '>=', $request->from_date);
                })
                ->when($request->filled('to_date'), function ($q) use ($request) {
                    $q->whereDate('in_time', '<=', $request->to_date);
                })
                ->with('student')
                ->get()
            : collect();       
             return view('report.sickroom', compact('hostels', 'room', 'sickroom'));
    }

   public function HostelAttendance(Request $request)
    {
        $branches = Branch::all(); 
        $hostels = collect();
        $section = collect();
        $rooms = collect();
        $attendance = collect();

        $active_tab = $request->input('active_tab', 'section_tab');

        $selected_branch = ($active_tab == 'section_tab') ? $request->branch_id : $request->room_branch_id;
        if ($selected_branch) {
            $hostels = Hostel::where('branch_id', $selected_branch)->get();
        }

        if ($active_tab == 'section_tab') {
            if ($request->filled('hostel_id')) {
                $section = Student::where('hostel_id', $request->hostel_id)
                    ->where('hostel_dayscholar', 'HOSTEL')
                    ->where('academic_year', $this->academic_year)
                    ->distinct()
                    ->pluck('section');
            }

            if ($request->filled('from_date') && $request->filled('to_date') && $request->filled('hostel_id') && $request->filled('section')) {
                $attendance = HostelAttendance::with('student')
                    ->where('hostel_id', $request->hostel_id)
                    ->where('section', $request->section)
                    ->where('academic_year', $this->academic_year)
                    ->whereBetween('attendance_date', [$request->from_date, $request->to_date])
                    ->get()
                    ->groupBy(function($item) {
                        return $item->student_id . '_' . $item->attendance_date;
                    });
            }
        }

        if ($active_tab == 'room_tab') {
            $room_hostel_id = $request->room_hostel_id;
            
            if ($room_hostel_id) {
                $rooms = Student::where('hostel_id', $room_hostel_id)
                    ->where('hostel_dayscholar', 'HOSTEL')
                    ->where('academic_year', $this->academic_year)
                    ->whereNotNull('room_no')
                    ->where('room_no', '!=', '')
                    ->distinct()
                    ->pluck('room_no');
            }

            if ($request->filled('from_date') && $request->filled('to_date') && $room_hostel_id && $request->filled('room_no')) {
                $studentIds = Student::where('hostel_id', $room_hostel_id)
                    ->where('room_no', $request->room_no)
                    ->where('hostel_dayscholar', 'HOSTEL')
                    ->where('academic_year', $this->academic_year)
                    ->pluck('student_id');

                $attendance = HostelAttendance::with('student')
                    ->whereIn('student_id', $studentIds)
                    ->whereBetween('attendance_date', [$request->from_date, $request->to_date])
                    ->where('academic_year', $this->academic_year)
                    ->get()
                    ->groupBy(function($item) {
                        return $item->student_id . '_' . $item->attendance_date;
                    });
            }
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            $records = [];
            foreach ($attendance as $key => $logs) {
                $student = $logs->first()?->student;
                $morning = $logs->firstWhere('timing', 'Morning');
                $evening = $logs->firstWhere('timing', 'Evening');
                $logDate = $logs->first()?->attendance_date;

                $records[] = [
                    'student_id' => $student?->student_id ?? ($logs->first()?->student_id ?? '-'),
                    'student_name' => $student?->student_name ?? '-',
                    'course' => $student?->course ?? '-',
                    'coaching_type' => $student?->coaching_type ?? '-',
                    'section' => $logs->first()?->section ?? '-',
                    'room_no' => $logs->first()?->room_no ?? '-',
                    'date' => $logDate ? \Carbon\Carbon::parse($logDate)->format('d-m-Y') : '',
                    'raw_date' => $logDate,
                    'morning' => $morning?->status ?? '-',
                    'evening' => $evening?->status ?? '-',
                ];
            }

            return response()->json([
                'status' => true,
                'branches' => $branches,
                'hostels' => $hostels,
                'sections' => $section,
                'rooms' => $rooms,
                'records' => $records,
                'active_tab' => $active_tab
            ], 200);
        }

        return view('report.hostelattendance', compact('branches', 'hostels', 'section', 'rooms', 'attendance', 'active_tab'));
    }
        public function HostelList(Request $request)
       {
        $branches = Branch::all();
        $hostels = collect();
        $sections = collect();
        $rooms = collect();

        if ($request->filled('branch') && $request->filled('hostel') && $request->input('active_tab') == 'section_tab') {
            $hostels = Hostel::where('branch_id', $request->branch)->get();

            $sections = Student::where('campus', $request->branch)
                ->where('hostel_id', $request->hostel)
                ->where('hostel_dayscholar', 'HOSTEL')
                ->where('academic_year', $this->academic_year)
                ->where('section', '!=', '')
                ->whereNotNull('section')
                ->distinct()
                ->pluck('section');
        }

        if ($request->filled('room_branch') && $request->filled('room_hostel') && $request->input('active_tab') == 'room_tab') {
            $room_branch = $request->room_branch;
            $room_hostel = $request->room_hostel;

            $rooms = Student::where('campus', $room_branch)
                ->where('hostel_id', $room_hostel)
                ->where('hostel_dayscholar', 'HOSTEL')
                ->where('academic_year', $this->academic_year)
                ->select('room_no', DB::raw('count(*) as total_students'))
                ->whereNotNull('room_no')
                ->where('room_no', '!=', '')
                ->groupBy('room_no')
                ->orderBy('room_no', 'asc')
                ->get();
        }

        if ($request->filled('branch') && $hostels->isEmpty()) {
            $hostels = Hostel::where('branch_id', $request->branch)->get();
        }
        
        $room_hostels = collect();
        if ($request->filled('room_branch')) {
            $room_hostels = Hostel::where('branch_id', $request->room_branch)->get();
        }

        return view('report.hostel_list', compact('branches', 'hostels', 'room_hostels', 'sections', 'rooms'));
    }
    public function downloadHostelSectionPdf(Request $request)
    {
        $request->validate([
            'branch' => 'required',
            'hostel' => 'required',
            'section' => 'required',
            'view' => 'required'
        ]);

        $hostelName = Hostel::find($request->hostel)?->name ?? 'Hostel';
        $branchname = Branch::find($request->branch)?->name ?? '';
        $section = $request->section;

        $students = Student::where('campus', $request->branch)
            ->where('hostel_id', $request->hostel)
            ->where('section', $section)
            ->where('hostel_dayscholar', 'HOSTEL')
            ->where('academic_year', $this->academic_year)
            ->get();

        $pdf = Pdf::loadView("pdf.$request->view", compact('students', 'branchname', 'section', 'hostelName'));
        return $pdf->download("$hostelName-$section-$request->view.pdf");
    }
    public function downloadHostelRoomPdf(Request $request)
    {
        $request->validate([
            'room_branch' => 'required',
            'room_hostel' => 'required',
            'room_no' => 'required',
            'view' => 'required'
        ]);

        $room = $request->room_no;
        $view = $request->view;
        $hostelName = Hostel::find($request->room_hostel)?->name ?? 'Hostel';
        

        $students = Student::where('campus', $request->room_branch)
            ->where('hostel_id', $request->room_hostel) 
            ->where('room_no', $room)
        
            ->where('hostel_dayscholar', 'HOSTEL')
            ->where('academic_year', $this->academic_year)
            ->get();

        $pdf = Pdf::loadView("pdf.$view", compact('students', 'room', 'hostelName'));
        return $pdf->download("Room-$room-$view.pdf");
    }

    public function HostelCourier(Request $request)
    {
        $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : collect();
        $room = $request->hostel ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no') : collect();
        $hostel_courier = $request->room
        ? HostelCourier::where('hostel_id', $request->hostel)
            ->when($request->room != 'all', fn($q) => $q->where('room_no', $request->room))
            ->when($request->filled('from_date'), function ($q) use ($request) {
            $q->whereDate('datetime_arrival', '>=', $request->from_date);
        })
            ->when($request->filled('to_date'), function ($q) use ($request) {
            $q->whereDate('datetime_arrival', '<=', $request->to_date);
        })
            ->with('student')
            ->get()
            : collect();

        return view('report.hostelcourier', compact('hostels', 'room', 'hostel_courier'));
    }

    public function HostelRoomList(Request $request)
    {
        $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : collect();
        $room = $request->hostel ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no') : collect();

        if ($request->view) {
            $hostels = Hostel::find($request->hostel)?->name;
            $students = Student::where('hostel_id', $request->hostel)->where('academic_year', $this->academic_year)->where('campus', $request->branch)->where('room_no', $request->room)->get();
            $branchname = $students->first()->branch->name ?? '';
            $pdf = Pdf::loadView("pdf.$request->view", compact('students', 'branchname', 'hostel'));
            return $pdf->download("$hostels-$request->room-$request->view.pdf");
        }

        return view('report.hostelroomlist', compact('hostels', 'room'));
    }

    public function HostelSectionList(Request $request)
    {
        $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : collect();
        $section = $request->hostel ? Student::where('hostel_id', $request->hostel)->distinct()->pluck('section') : collect();

        if ($request->view) {
            $hostels = Hostel::find($request->hostel)?->name;
            $students = Student::where('hostel_id', $request->hostel)->where('academic_year', $this->academic_year)->where('campus', $request->branch)->where('section', $request->section)->get();
            $branchname = $students->first()->branch->name ?? '';
            $pdf = Pdf::loadView("pdf.$request->view", compact('students', 'branchname', 'hostel'));
            return $pdf->download("$hostels-$request->section-$request->view.pdf");
        }

        return view('report.hostelsectionlist', compact('hostels', 'section'));
    }

    public function HostelVacate(Request $request)
    {
        $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : [];
        $room = $request->hostel ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no') : collect();
        $vacate_log = collect();

        if ($request->room) {

        $vacate_log = DB::table('vacate_log')
            ->where('hostel_id', $request->hostel)
            ->when($request->room != 'all', function ($q) use ($request) {
            $q->where('room_no', $request->room);
            })
                ->when($request->filled('from_date'), function ($q) use ($request) {
                $q->whereDate('datetime', '>=', $request->from_date);
            })
                ->when($request->filled('to_date'), function ($q) use ($request) {
                $q->whereDate('datetime', '<=', $request->to_date);
            })
                ->orderBy('datetime', 'desc')
                ->get();
        }
        return view('report.hostelvacate', compact('hostels', 'room', 'vacate_log'));
    }
    public function HostelVacancy(Request $request)
        {
            $branches = Branch::all();
            $hostels = $request->filled('branch') ? Hostel::where('branch_id', $request->branch)->get() : collect();
            $room = $request->filled('hostel') ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->orderBy('room_no')->pluck('room_no') : collect();

            $vacancy_log = collect();

            if ($request->filled('branch') && $request->filled('hostel')) {
                $occupiedCounts = Student::where('hostel_id', $request->hostel)
                    ->when(auth()->user()->branch, fn($q) => $q->where('campus', auth()->user()->branch))
                    ->whereNotNull('room_no')
                    ->where('academic_year', $this->academic_year)
                    ->where('room_no', '!=', '')
                    ->groupBy('room_no')
                    ->select('room_no', DB::raw('count(*) as count'))
                    ->pluck('count', 'room_no')
                    ->toArray();

                $roomsQuery = HostelRoom::where('hostel_id', $request->hostel);

                if ($request->filled('room') && $request->room !== 'all') {
                    $roomsQuery->where('room_no', $request->room);
                }

                $roomsData = $roomsQuery->select(
                    'room_no',
                    DB::raw('MAX(cot_type) as cot_type'),
                    DB::raw('MAX(no_of_cots) as no_of_cots')
                )
                ->groupBy('room_no','cot_type')
                ->orderByRaw('CAST(SUBSTRING(room_no, 3) AS UNSIGNED) ASC')
                ->get();

                $selectedHostel = Hostel::find($request->hostel);
                $hostelName = $selectedHostel ? $selectedHostel->name : '';

                $vacancy_log = $roomsData->map(function ($roomRecord) use ($occupiedCounts, $hostelName) {
                    $roomNo = trim($roomRecord->room_no);
                    $occupied = $occupiedCounts[$roomNo] ?? 0;
                    $capacity = (int) $roomRecord->no_of_cots;
                    $vacancy = max(0, $capacity - $occupied);

                    return (object) [
                        'hostel_name' => $hostelName,
                        'room_no'     => $roomRecord->room_no,
                        'room_type'   => $roomRecord->cot_type ?? 'Standard',
                        'capacity'    => $capacity,
                        'occupied'    => $occupied,
                        'vacancy'     => $vacancy
                    ];
                });
            }

            return view('report.hostelvacancy', compact('branches', 'hostels', 'room', 'vacancy_log'));
        }
        public function UserLoginReport(Request $request)
        {
            $branches = Branch::all();

            $students = Student::query()->where('academic_year', $this->academic_year)
                ->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })
                ->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch); })
                ->when($request->filled('course'), function ($q) use ($request) { $q->where('course', $request->course); })
                // ->when($request->filled('section'), function ($q) use ($request) { $q->where('section', $request->section); })
                ->when($request->filled('coaching_type'), function ($q) use ($request) { $q->where('coaching_type', $request->coaching_type); })
                ->when($request->filled('status'), function ($q) use ($request) {
                    if ($request->status == '1') {
                        $q->where('active', 1);
                    } elseif ($request->status == '0') {
                        $q->where('active', 0)
                        ->whereNotNull('last_login');
                    }elseif ($request->status == 'not_accessed') {
                        $q->where('active', 0)
                        ->whereNull('last_login');
                    }
                })
                ->when($request->filled('device'), function ($q) use ($request) { $q->where('device', $request->device); })
                ->when($request->filled('from_date'), function ($q) use ($request) { $q->whereDate('last_login', '>=', $request->from_date);})
                ->when($request->filled('to_date'), function ($q) use ($request) { $q->whereDate('last_login', '<=', $request->to_date); })
                ->when($request->filled('search'), function ($q) use ($request) {$search = $request->search; $q->where(function($subQuery) use ($search) {
                    $subQuery->where('student_id', 'LIKE', "%{$search}%")
                            ->orWhere('student_name', 'LIKE', "%{$search}%")
                            ->orWhere('user_name', 'LIKE', "%{$search}%");
                });
            })->orderByDesc('last_login')->get();

            $totalStudents = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })
                ->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch); })->count();

            $todayLogin = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })
                ->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch); })
                ->whereDate('last_login', today())->count();

            $onlineStudents = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })
                ->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch); })
                ->where('active', 1)->count();

            $webLogin = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })
                ->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch); })
                ->whereDate('last_login', today())
                ->where('device', 'WEB')
                ->count();

            $androidLogin = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })
                ->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch);})
                ->whereDate('last_login', today())->where('device', 'ANDROID')->count();

            $iosLogin = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })
                ->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch); })
                ->whereDate('last_login', today())->where('device', 'IOS')->count();

            $courses = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch); })->select('course')->distinct()->orderBy('course')->pluck('course');
            // $sections = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($q) { $q->where('campus', auth()->user()->branch); })->when($request->filled('branch'), function ($q) use ($request) { $q->where('campus', $request->branch); })->when($request->filled('course'), function ($q) use ($request) { $q->where('course', $request->course); })->select('section')->distinct()->orderBy('section')->pluck('section');
            $coaching_type = Student::select('coaching_type')->where('academic_year', $this->academic_year)->distinct()->get();
            return view('report.userloginreport', compact('students','branches','courses','coaching_type','totalStudents','todayLogin','onlineStudents','webLogin','androidLogin','iosLogin'));
        }
    public function individualStudentReport(Request $request)
    {
        $isApi = $request->is('api/*') || $request->wantsJson();

        // API: GET students list (with search support)
        if ($isApi && $request->isMethod('get') && !$request->filled('student_id')) {
            $query = Student::select('student_id', 'student_name', 'course', 'campus', 'section')
                ->where('academic_year', $this->academic_year ?? AcademicYear::where('active', 1)->value('academic_year'))
                ->when(auth()->user() && auth()->user()->branch, fn($q) => $q->where('campus', auth()->user()->branch))
                ->when($request->filled('search'), function ($q) use ($request) {
                    $s = $request->search;
                    $q->where(function ($qq) use ($s) {
                        $qq->where('student_id', 'like', "%{$s}%")
                           ->orWhere('student_name', 'like', "%{$s}%");
                    });
                })
                ->orderBy('student_name')
                ->limit($request->input('limit', 50))
                ->get();
            return response()->json(['status' => true, 'students' => $query], 200);
        }

        $students = Student::select('student_id', 'student_name')->where('academic_year', $this->academic_year)->orderBy('student_name')->get();

        if ($request->isMethod('post')) {
            $student = Student::where('student_id', $request->student_id)->first();
            if (!$student) {
                if ($isApi) {
                    return response()->json(['status' => false, 'message' => 'Student not found'], 404);
                }
                return back()->with('error', 'Student not found');
            }
            $allExams = ExamSubjectReport::select(
        'category',
        'subject',
        'exdate',
        'sec'
    )
    ->where('sec', $student->section)
    ->groupBy('category', 'subject', 'exdate', 'sec')
    ->get()
    ->sortBy(function ($exam) {

        $category = $exam->category;

        // Main category order
        if (str_starts_with($category, 'CUMULATIVE TEST')) {
            $groupOrder = 1;
        } elseif (str_starts_with($category, 'GRAND TEST')) {
            $groupOrder = 2;
        } elseif (str_starts_with($category, 'WEEKEND TEST')) {
            $groupOrder = 3;
        } elseif (str_starts_with($category, 'UNIT TEST')) {
            $groupOrder = 4;
        } else {
            $groupOrder = 99;
        }

        // Subject order
        if (str_contains($category, 'PHY')) {
            $subjectOrder = 1;
        } elseif (str_contains($category, 'CHE')) {
            $subjectOrder = 2;
        } elseif (str_contains($category, 'BOT')) {
            $subjectOrder = 3;
        } elseif (str_contains($category, 'ZOO')) {
            $subjectOrder = 4;
        } elseif (str_contains($category, 'BIO')) {
            $subjectOrder = 5;
        } else {
            $subjectOrder = 0;
        }

        // Plain CUMULATIVE TEST first
        if ($category === 'CUMULATIVE TEST') {
            $subjectOrder = 0;
        }
        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $exam->exdate);
        return [($groupOrder * 100) + $subjectOrder,$date->timestamp];
    })
    ->values();

        $marks = ExamSubjectReport::where('stuid', $request->student_id)->get();

        $report = collect();

        foreach ($allExams as $exam) {
            $mark = $marks->firstWhere('subject', $exam->subject);

            $subjectFields = ['phy', 'che', 'bot', 'zoo', 'bio'];
            $subjectData = [];

            foreach ($subjectFields as $key) {
                $subjectData[$key . '_r'] = $mark->{$key . '_r'} ?? 0;
                $subjectData[$key . '_w'] = $mark->{$key . '_w'} ?? 0;
                $subjectData[$key . '_l'] = $mark->{$key . '_l'} ?? 0;
            }

            $data = [
                'category' => $exam->category,
                'subject'  => $exam->subject,
                'exdate'   => $exam->exdate,
                'phy_tot' => $mark->phy_tot ?? null,
                'che_tot' => $mark->che_tot ?? null,
                'bot_tot' => $mark->bot_tot ?? null,
                'zoo_tot' => $mark->zoo_tot ?? null,
                'bio_tot' => $mark->bio_tot ?? null,
                'nettot'  => $mark->nettot ?? null,
                'totmark' => $mark->totmark ?? 0,
            ];

            $data = array_merge($data, $subjectData);

            $report->push((object) $data);
        }
            $average = [
                'phy'   => round($marks->avg('phy_tot')),
                'che'   => round($marks->avg('che_tot')),
                'bot'   => round($marks->avg('bot_tot')),
                'zoo'   => round($marks->avg('zoo_tot')),
                'bio'   => round($marks->avg('bio_tot')),
                'total' => round($marks->avg('nettot')),
            ];

            if ($isApi) {
                // JSON preview for app: ?preview=1 or format=json
                if ($request->input('preview') == '1' || $request->input('format') == 'json' || $request->wantsJson()) {
                    // If explicitly asking for pdf download via ?download=pdf, fall through to pdf
                    if ($request->input('download') !== 'pdf') {
                        return response()->json([
                            'status' => true,
                            'student' => $student,
                            'report' => $report,
                            'average' => $average,
                            'marks_count' => $marks->count(),
                        ], 200);
                    }
                }
                // PDF binary for app download
                $pdf = PDF::loadView('pdf.individualstudentreport', compact('student', 'marks', 'average', 'report') );
                $output = $pdf->output();
                return response($output, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="'.$student->student_id.'_IndividualStudentReport.pdf"',
                ]);
            }

            $pdf = PDF::loadView('pdf.individualstudentreport', compact('student', 'marks', 'average', 'report') );

            return $pdf->download( $student->student_id . '_IndividualStudentReport.pdf');
        }
        if ($isApi) {
            return response()->json(['status' => true, 'students' => $students], 200);
        }
        return view('report.individualstudent', compact('students'));
    }   
    public function StudentExpense(Request $request)
    {
        $branches = Branch::orderBy('name')->get();

        $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : collect();
        $room = $request->hostel ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no') : collect();
        $students = Student::where('academic_year', $this->academic_year)->where('hostel_dayscholar', 'HOSTEL')
            ->when($request->branch, function ($query) use ($request) {
                $query->where('campus', $request->branch);
                })
            ->when($request->hostel, function ($query) use ($request) {
                $query->where('hostel_id', $request->hostel);
                })
            ->when($request->room && $request->room != 'all', function ($query) use ($request) {
                $query->where('room_no', $request->room);
                })
            ->when($request->student_id, function ($query) use ($request) {
                $query->where('student_id', $request->student_id);
                })
            ->orderBy('student_name')
            ->get();


        $phoneCardExpenses = PhoneCard::select('student_id', DB::raw('SUM(expense) as phone_card_total'))
            ->when($request->from_date, function ($query) use ($request) {
                $query->whereDate('phone_date', '>=', $request->from_date);
            })
            ->when($request->to_date, function ($query) use ($request) {
                $query->whereDate('phone_date', '<=', $request->to_date);
            })
            ->groupBy('student_id')
            ->pluck('phone_card_total', 'student_id');


        $sickRoomExpenses = SickRoomEntry::select('student_id', DB::raw('SUM(expense) as sick_room_total'))
            ->when($request->from_date, function ($query) use ($request) {
                $query->whereDate('in_time', '>=', $request->from_date);
            })
            ->when($request->to_date, function ($query) use ($request) {
                $query->whereDate('in_time', '<=', $request->to_date);
            })
            ->groupBy('student_id')
            ->pluck('sick_room_total', 'student_id');

        $expenseData = [];

        foreach ($students as $student) {
            $phoneCardTotal = $phoneCardExpenses[$student->student_id] ?? 0;
            $sickRoomTotal = $sickRoomExpenses[$student->student_id] ?? 0;
            $expenseData[$student->student_id] = [
                'phone_card_total' => $phoneCardTotal,
                'sick_room_total' => $sickRoomTotal,
                'total_expense' => $phoneCardTotal + $sickRoomTotal,
            ];
        }
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $students = $students->filter(function ($student) use ($expenseData) {
            $expense = $expenseData[$student->student_id] ?? null;
            if (!$expense) {
                return false;
            }
            return $expense['phone_card_total'] || $expense['sick_room_total'] ;
        })->values();
        }
        return view('report.studentexpance', compact('branches', 'hostels', 'room', 'students', 'expenseData'));
    }

public function studentReport(Request $request)
    {

        if ($request->isMethod('get') && !$request->ajax()) {
            $branches = Branch::all();
            return view('report.studentreport', compact('branches'));
        }

        if ($request->ajax()) {

            if ($request->has('branch') && !$request->has('course')) {
                $courses = Student::where('academic_year', $this->academic_year)->where('campus', $request->branch)->whereNotNull('course')->where('course', '!=', '')->distinct()->orderBy('course')->pluck('course');
                return response()->json($courses);
            }

            if ($request->has('branch') && $request->has('course') && !$request->has('section')) {
                $sections = Student::where('academic_year', $this->academic_year)->where('campus', $request->branch)->where('course', $request->course)->whereNotNull('section')->where('section', '!=', '')->distinct()->orderBy('section')->pluck('section');
                return response()->json($sections);
            }


        if ($request->has('branch') && $request->has('course') && $request->has('section') && !$request->has('coaching_type')) {
            $coachingTypes = Student::where('academic_year', $this->academic_year)->where('campus', $request->branch)->where('course', $request->course)->where('section', $request->section)->where('coaching_type', '!=', '')->distinct()->orderBy('coaching_type')->pluck('coaching_type');
            return response()->json($coachingTypes);
        }

        if ( $request->has('branch') && $request->has('course') && $request->has('section') &&
        $request->has('coaching_type')) {
            $students = Student::where('academic_year', $this->academic_year)->where('campus', $request->branch)->where('course', $request->course)->where('section', $request->section)->where('coaching_type', $request->coaching_type)->select('student_id','student_name')->orderBy('student_name')->get();
            return response()->json($students);
        }
        }
        if ($request->student_id === 'all') {

            $students = Student::with('branch')->where('academic_year', $this->academic_year)->where('campus', $request->branch)->where('course', $request->course)->where('section', $request->section)->where('coaching_type', $request->coaching_type)->orderBy('student_name')->get();
        } else {
            $student = Student::with('branch')->where('student_id', $request->student_id)->first();
            if (!$student) {
                return back()->with( 'error','Student not found');
            }
            $students = collect([$student]);
        }
        if ($students->isEmpty()) {
            return back()->with( 'error', 'No students found');
        }

        $reports = [];
        $allExamRows = DB::table('examsubjectreport')->join('student', 'student.student_id', '=', 'examsubjectreport.stuid')->where('student.academic_year', $this->academic_year)->where('student.campus', $request->branch)->where('student.course', $request->course)->where('student.section', $request->section)->where('student.coaching_type', $request->coaching_type)->select('examsubjectreport.testid','examsubjectreport.category','examsubjectreport.subject','examsubjectreport.exdate')->distinct()->orderBy('examsubjectreport.exdate')->get();

        foreach ($students as $student) {

            $studentRows = DB::table('examsubjectreport')->where('stuid', $student->student_id)->get()->map(function ($row) { 
                $row = $this->normalizeRow($row);
                $row->_is_absent_exam = false;
                return $row;
                })
                ->keyBy('subject');

            $rows = $allExamRows->map(function ($exam) use ($studentRows) {
                if ($studentRows->has($exam->subject)) {
                    return $studentRows->get($exam->subject);
                }
                $row = new \stdClass();

                $row->testid = $exam->testid;
                $row->category = $exam->category;
                $row->subject = $exam->subject;
                $row->exdate = $exam->exdate;
                $row->_is_absent_exam = true;

                foreach ([ 'phy', 'che', 'bot','zoo','bio'] as $key) {
                    $row->{$key . '_r'} = 0;
                    $row->{$key . '_w'} = 0;
                    $row->{$key . '_l'} = 0;
                    $row->{$key . '_tot'} = null;
                }
                $row->nettot = null;
                return $row;
            });

            $rows = $rows->map(
                fn($row) => $this->normalizeRow($row)
            );

            $subjectMaxMarks = [];
            $firstStudentRow = $studentRows->first();
            if ($firstStudentRow) {
                $subjectKeys = $this->detectSubjectKeys($firstStudentRow);
                foreach ($subjectKeys as $key => $label) {

                    $maxQuestions = $studentRows->max(function ($row) use ($key) {
                        $r = (int) ($row->{$key . '_r'} ?? 0);
                        $w = (int) ($row->{$key . '_w'} ?? 0);
                        $l = (int) ($row->{$key . '_l'} ?? 0);
                        return $r + $w + $l;
                    });

                    $subjectMaxMarks[$key] = [
                        'label' => $label,
                        'mark'  => $maxQuestions * 4,
                    ];
                }
            }

            if ($rows->isNotEmpty()) {
                $studentReport = $this->buildDynamicReport($rows, $subjectMaxMarks, $request->section); 
            } else {
                $studentReport = collect();
            }

            $reports[] = [
                'student' => $student,
                'marks' => $rows,
                'report' => $studentReport,
                'subjects' => ['phy' => 'Phy', 'che' => 'Che', 'bot' => 'Bot', 'zoo' => 'Zoo', 'bio' => 'Bio',],
                'max_marks' => $subjectMaxMarks,
                'weeklySummaries' => [],
            ];
        }
    
        if (empty($reports)) {
            return back()->with( 'error','No students found');
        }

        $pdf = PDF::loadView( 'pdf.studentreport',compact('reports'));
        return $pdf->download('studentReport.pdf');
    }
    private function normalizeRow($row)
    {
        $category = trim($row->category ?? '');
        $subject  = trim($row->subject ?? '');

        $categorySubject = null;
        if (preg_match('/\(([^()]*)\)\s*$/', $category, $m)) {
            $categorySubject = trim($m[1]);
        }

        $baseCategory = $categorySubject? trim(preg_replace('/\s*\([^)]*\)\s*$/', '', $category)) : $category;

        $examName = preg_replace('/\s*-\s*\d{1,2}[.\/-]\d{1,2}[.\/-]\d{4}\s*$/', '', $subject);
        $examName = trim(preg_replace('/\s*\([^)]*\)\s*$/', '', $examName));

        if ($examName === '') {
            $examName = $baseCategory;
        }

        $row->parsed_date       = $this->parseDate($row->exdate);
        $row->_base_category    = $baseCategory;
        $row->_category_subject = $categorySubject;
        $row->_exam_name        = $examName;
        $row->_is_numbered      = (bool) preg_match('/\d/', $examName);

        return $row;
    }

    private function buildDynamicReport($rows, $subjectMaxMarks = [], $section = null)
    {
        $groups = $rows->groupBy('_base_category');
        $report = collect();

        foreach ($groups as $categoryName => $categoryRows) {
            $categoryRows = $categoryRows->sortBy(function ($r) {
                return $r->parsed_date ? $r->parsed_date->timestamp : PHP_INT_MAX;
            })->values();

            $isSubjectWise = $categoryRows->contains(fn($r) => !empty($r->_category_subject));
            $isNumbered    = $categoryRows->contains(fn($r) => $r->_is_numbered);

            if ($isSubjectWise && !$isNumbered) {
                $type = 'weekly';
                $data = $this->buildWeeklyReport($categoryRows);
            } elseif ($isSubjectWise && $isNumbered) {
                $type = 'numbered';
                $data = $this->buildMergedReport($categoryRows, groupField: '_exam_name');
            } else {
                $type = 'simple';
                $data = $this->buildMergedReport($categoryRows, groupField: '_exam_name', includeOverallTop: true, section: $section);
            }

            $report->push([
                'category' => $categoryName,
                'type'     => $type,
                'rows'     => $data,
                'max_marks'=>$subjectMaxMarks
            ]);
        }

        return $report->values();
    }

    private function buildWeeklyReport($rows)
    {
        $startDate = $rows->pluck('parsed_date')->filter()->sort()->first();

        if (!$startDate) {
            return collect();
        }

        $grouped = $rows->groupBy(function ($row) use ($startDate) {
            if (!$row->parsed_date) {
                return 0;
            }
            return (int) floor($startDate->diffInDays($row->parsed_date) / 7) + 1;
        });

        $weeks = collect();
        foreach ($grouped->sortKeys() as $week => $group) {
            if ($week == 0) {
                continue;
            }

            $dates = $group->pluck('parsed_date')->filter()->sort();
            $from  = $dates->first();
            $to    = $dates->last();
            $subjects = $this->allSubjectTotals($group);

            $weeks->push([
                'sno'   => $week,
                'label' => 'Week - ' . $week,
                'range' => ($from && $to) ? $from->format('d-m-Y') . ' To ' . $to->format('d-m-Y') : '',
                'subjects' => $subjects,
                'total'    => $this->sumOrNull($subjects),
            ]);
        }

        return $weeks->values();
    }

    private function buildMergedReport($rows, $groupField = null,  $includeOverallTop = false, $section = null)
    {
        $groups = $groupField ? $rows->groupBy($groupField) : $rows->map(fn($r) => collect([$r]));

        $result = collect();
        $i = 0;

        foreach ($groups as $examName => $group) {
            $i++;

            $dates = $group->pluck('parsed_date')->filter()->sort();
            $date  = $dates->first();
            $subjects = $this->allSubjectTotals($group);

        foreach ($group as $examRow) {
            if (($examRow->_is_absent_exam ?? false) !== true) {
                continue;
            }
            $examSubject = $examRow->subject ?? null;
            if (!$examSubject) {
                continue;
            }
            $examRecords = DB::table('examsubjectreport')->where('subject', $examSubject)->where('sec', $section)->get();

            $subjectMap = ['phy' => 'PHYSICS', 'che' => 'CHEMISTRY','bot' => 'BOTANY', 'zoo' => 'ZOOLOGY', 'bio' => 'BIOLOGY', ];

            foreach ($subjectMap as $key => $label) {
                $conducted = $examRecords->contains(function ($record) use ($key) {
                    return
                        (int) ($record->{$key . '_r'} ?? 0) > 0 ||
                        (int) ($record->{$key . '_w'} ?? 0) > 0 ||
                        (int) ($record->{$key . '_l'} ?? 0) > 0 ||
                        (int) ($record->{$key . '_tot'} ?? 0) > 0;
                });

                if ($conducted) {
                    $subjects[$label] = 'AB';
                }
            }
        }
        $allSubjectsAbsent = collect($subjects)->filter(fn($value) => $value === 'AB')->isNotEmpty();

        $hasActualMark = collect($subjects)->contains(function ($value) {
                return is_numeric($value);
            });

            $netTotals = $group->pluck('nettot')->filter(fn($v) => is_numeric($v));

             $row = [
                'sno' => $i,
                'exam' => $groupField ? $examName : ($group->first()->_exam_name ?? $examName),
                'date' => $date ? $date->format('d-m-Y') : ($group->first()->exdate ?? ''),
                'subjects' => $subjects,
                'total' => $hasActualMark ? (int) $netTotals->sum() : ($allSubjectsAbsent ? 'AB' : $this->sumOrNull($subjects)),
            ];
            if ($includeOverallTop) {
                $examSubject = $group->first()->subject;
                

            $overallTop = DB::table('examsubjectreport')->where('subject', $examSubject)
                ->where('sec', $section)
                ->whereNotNull('nettot')
                ->orderBy('nettot', 'DESC')
                ->limit(1)
                ->value('nettot');

                $row['overall_top'] = $overallTop;
            }
            $result->push($row);
        }
        return $result->values();
    }


    private function allSubjectTotals($group)
    {
        
        $subjectKeys = $this->detectSubjectKeys($group->first());
        $subjects = [];

        foreach ($subjectKeys as $key => $label) {
        $actualMark = null;     
        $examExists = false;

            foreach ($group as $row) {

                if (($row->_is_absent_exam ?? false) === false) {

                    $r = (int) ($row->{$key . '_r'} ?? 0);
                    $w = (int) ($row->{$key . '_w'} ?? 0);
                    $l = (int) ($row->{$key . '_l'} ?? 0);

                    if ($r > 0 || $w > 0 || $l > 0) {
                        $examExists = true;
                        if (is_numeric($row->{$key . '_tot'} ?? null)) {
                            $actualMark = (int) $row->{$key . '_tot'};
                        }
                    }
                }

                else {
                    $examSubject = strtoupper(
                        trim($row->_category_subject ?? '')
                    );
                    if ($examSubject === $label) {
                        $examExists = true;
                    }
                }
            }

            if ($actualMark !== null) {
                $subjects[$label] = $actualMark;
            } elseif ($examExists) {
                $subjects[$label] = 'AB';
            } else {
                $subjects[$label] = null;
            }
        }

        return $subjects;
    }

    private function detectSubjectKeys($row)
    {
        $labels = [
            'phy' => 'PHYSICS',
            'che' => 'CHEMISTRY',
            'bot' => 'BOTANY',
            'zoo' => 'ZOOLOGY',
            'bio' => 'BIOLOGY',
        ];

        $keys = [];
        foreach ($labels as $key => $label) {
            if (property_exists($row, $key . '_tot')) {
                $keys[$key] = $label;
            }
        }
        return $keys;
    }

    private function sumOrNull(array $subjects)
    {
        $vals = array_filter($subjects, function ($v) {
            return $v !== null && is_numeric($v);
        });

        return empty($vals) ? null : array_sum($vals);
    }

 private function parseDate($exdate)
    {
        $exdate = trim((string) $exdate);
 
        if ($exdate === '' || strtoupper($exdate) === 'NULL') {
            return null;
        }
 
        foreach (['d-m-Y', 'd.m.Y', 'd/m/Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $exdate)->startOfDay();
            } catch (\Exception $e) {
                // try next format
            }
        }
 
        try {
            return Carbon::parse($exdate)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }
}
