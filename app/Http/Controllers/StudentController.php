<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSubjectReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Announcement;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request)
    {
        $students = [];
        if ($request->has('course')) {
            $students = Student::when($this->academic_year, fn($q) => $q->where('academic_year', $this->academic_year))->when(auth()->user()->branch, fn($q) => $q->where('campus', 'like', '%' . auth()->user()->branch . '%'))->where('course', $request->course)->get();
        }

        return view('student.index', compact('students'));
    }


    public function create(Request $request)
    {
        if ($request->has('city')) {
            $pincodes = DB::table('district_list')->where('District', $request->city)->select('Pincode')->get();
            return response()->json($pincodes);
        }
        return view('student.create');
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $data['academic_year'] = $this->academic_year;
        Student::create($data);
        return redirect()->route('student.index')->with('success', 'Student created successfully.');
    }


    public function edit(Request $request, Student $Student)
    {
        $districts = DB::table('district_list')->where('State', $Student->state)->distinct()->orderBy('District')->get();
        $states = DB::table('district_list')->select('State')->distinct()->orderBy('State')->get();
        $pincodes = DB::table('district_list')->where('District', $Student->district)->select('Pincode')->get();

        return view('student.edit', compact('districts', 'states', 'pincodes', 'Student'));
    }


    public function update(Request $request, Student $student)
    {
        $data = $request->all();
        $data['hostel_dayscholar'] = $data['hostel_dayscholar'] ?? null;
        $data['ac_nonac'] = $data['ac_nonac'] ?? null;
        $data['password']  = bcrypt($request->password_1);

        $student->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('student.index')->with('success', 'Student details successfully updated.');
    }

    public function RestoreStudent(Request $request)
    {
        $students = Student::onlyTrashed()->get();
        return view('student.restore', compact('students'));
    }


    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('student.index')->with('success', 'Student deleted successfully.');
    }


    public function section()
    {
        $students = DB::table('student')
            ->join('branch', 'student.campus', '=', 'branch.id')
            ->select('student.*', 'branch.name as campus')
            ->get();

        return view('student.section', compact('students'));
    }


    public function update_section(Request $request)
    {
        if ($request->student_ids) {
            foreach ($request->student_ids as $student_id) {
                $student = Student::find($student_id);
                if ($student) {
                    $student->section = $request->section;
                    $student->save();
                }
            }
        }

        return redirect()->route('section.student')->with('success', 'Student details successfully updated.');
    }
    public function profile()
    {
        return view('student.profile');
    }
 

    public function dashboard()
    {
        $student = Student::where('student_id',auth()->user()->student_id)->first();
        $exam = $student->GetExam();

        $examStartTime = $exam ? $exam->start_at->toIso8601String() : null;

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();


        $distinctAttendanceSubQuery = Attendance::select('attendance_date', 'timing', 'status')
            ->where('student_id', $student->student_id)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->distinct();

        $stats = DB::query()->fromSub($distinctAttendanceSubQuery, 'distinct_attendance')->selectRaw("COUNT(DISTINCT attendance_date) as total_days,SUM(CASE WHEN status = 'P' THEN 0.5 ELSE 0 END) as present_days")->first();

        $totalDaysInMonth = $stats->total_days ?? 0;
        $presentDaysInMonth = $stats->present_days ?? 0;

        $percentage = $totalDaysInMonth > 0 ? round(($presentDaysInMonth / $totalDaysInMonth) * 100, 2) : 0;

        return view('dashboards.studentdashboard', compact('examStartTime', 'totalDaysInMonth', 'presentDaysInMonth', 'percentage'));
    }

    function marksheet(Request $request)
    {
        $sid = auth()->user()->student_id;

        $exams = DB::table("exam_answer as a")->join('exam as b', 'a.test_id', '=', 'b.testid')->where('a.student_id', $sid)->where('b.publish', 'Yes')->selectRaw("exam_date,b.name,test_id,sum(mark)mark,(count(q_no)*4)total,markrange")->groupBy('test_id')->orderBy('b.updated_at', 'desc')->limit(5)->get()->map(function ($test) {
            return ['exam_date' => $test->exam_date,'name' => $test->name,'test_id' => $test->test_id,'mark' => $test->mark,'total' => $test->total,'markrange' => $test->markrange,'first_mark' => ExamAnswer::where('test_id', $test->test_id)->selectRaw('SUM(mark) as mark')->groupBy('student_id')->orderByDesc('mark')->value('mark')];
        });

        $subjectexam = null;
        if($request->exam){
           $subjectexam = ExamSubjectReport::where("subject", "like", "%$request->exam%")->where("stuid", $sid)->orderByRaw("STR_TO_DATE(exdate, '%d-%m-%Y') desc")->get();
        }
        return view('student.marksheet', compact('exams', 'subjectexam'));
    }

    function mark_subject(Request $request, $testid)
    {
        $sid = auth()->user()->student_id;
        $subjects = ExamAnswer::selectRaw("sum(mark=4)r,sum(mark=-1)w,sum(mark=0)l,sum(mark)tot,(count(q_no)*4)total,subject")->where('test_id', $testid)->where('student_id', $sid)->groupBy('subject')->orderByRaw("FIELD(subject, 'Physics', 'Chemistry', 'Botany', 'Zoology')")->get();
        return view('student.mark_subject', compact('subjects'));
    }
    function mark_download(Request $request, $testid)
    {
        $sid = auth()->user()->student_id;
        $answers = ExamAnswer::where('test_id', $testid)->where('student_id', $sid)->orderBy('q_no')->get();
        $answers = $answers->chunk(45);
        $exam = Exam::where('testid', $testid)->where('academic_year', $this->academic_year)->first();
        $pdf = Pdf::loadView('pdf.marksheet', compact('answers', 'exam'));
        return $pdf->download("$exam->name - $sid.pdf");
    }



    public function getExamStartTime()
    {
        $exam = Exam::where('')->first();

        if (!$exam || !$exam->start_at) {
            return response()->json(['error' => 'Exam start time not found'], 404);
        }

        return response()->json([
            'start_at' => Carbon::parse($exam->start_at)->toISOString()
        ]);
    }


    public function attendance()
    {
        $student_id = Auth::user()->student_id;
        $attendance = DB::table('attendance')
            ->selectRaw("
            student_id,
            DATE_FORMAT(attendance_date, '%Y-%m') AS month,
            GROUP_CONCAT(timing) AS timings,
            GROUP_CONCAT(status) AS statuses,
            COUNT(DISTINCT attendance_date) AS total_days,
            SUM(CASE WHEN status = 'P' THEN 0.5 ELSE 0 END) AS present_days
        ")
            ->where('student_id', $student_id)
            ->groupByRaw("student_id, DATE_FORMAT(attendance_date, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(attendance_date, '%Y-%m')")
            ->get();

        $total_present = $attendance->sum('present_days');
        $total_days = $attendance->sum('total_days');
        $percentage = $total_days > 0 ? round(($total_present / $total_days) * 100, 2) : 0;

        return view('student.attendance', compact('attendance', 'total_present', 'total_days', 'percentage'));
    }
}
