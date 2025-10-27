<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\Student;
use App\Models\Announcement;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Providers\CsvServiceProvider;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function section_exam(Request $request)
    {
        $sections = Student::select('section')->distinct()->orderBy('section')->get();
        $tests = Exam::where('academic_year', $this->academic_year)->groupBy('name')->get();
        $test_name = $request->test_name ?? 0;

        if ($request->has('publish')) {
            $testIds = Exam::where('academic_year', $this->academic_year)->where('name', $test_name)->update(['publish' => $request->publish]);
            return view('report.section_exam', compact('sections', 'tests', 'test_name'));
        }

        if ($request->query('type') == 'overall') {
        $testIds = Exam::where('name', $test_name)->pluck('testid')->toArray();
        $section = $request->section;
        
        $results = DB::table('exam_answer as a')->join('student as b', 'a.student_id', '=', 'b.student_id')->join('branch as c', 'b.campus', '=', 'c.id')->whereIn('a.test_id', $testIds)->where('b.section', $request->section)->selectRaw("a.test_id,a.student_id,a.mode as stmode,GROUP_CONCAT(DISTINCT a.subject)subjects,sum(a.mark)mark,b.student_name,c.name,b.coaching_type,b.gender,b.section")->groupBy('a.student_id')->orderBy('test_id')->orderBy('student_name')->get();

            if (count($results) == 0) {
                return back()->with('error', 'No data found');
            }
            $testids = implode(',', $testIds);
            return view('report.overall_print', compact('results', 'test_name', 'section', 'testids'));
        }

        if ($request->query('type') == 'omr') {
            $testIds = Exam::where('name', $test_name)->pluck('testid')->toArray();
            $section = $request->section;
            $answers = DB::table('exam_answer as a')->join('student as b', 'a.student_id', '=', 'b.student_id')->whereIn('a.test_id', $testIds)->where('b.section', $request->section)->selectRaw("a.*,b.section,b.student_name")->orderBy('test_id')->orderBy('student_name')->orderBy('q_no')->get();
            if (count($answers) == 0) {
                return back()->with('error', 'No data found');
            }
            $studentAnswers = [];
            foreach ($answers as $answer) {
                $studentAnswers[$answer->student_id][] = $answer;
            }
            $formattedData = [];
            foreach ($studentAnswers as $studentId => $studentData) {
                $chunks = array_chunk($studentData, ceil(count($studentData) / 4));
                $pages = array_chunk($chunks, 4);

                foreach ($pages as $page) {
                    $formattedData[] = $page;
                }
            }


            return view('report.omr_print', compact('formattedData', 'test_name', 'section'));
        }

        return view('report.section_exam', compact('sections', 'tests', 'test_name'));
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

    public function AttendanceReport(Request $request)
    {
        $attendances = [];
        if ($request->has('branch_id')) {
            $attendances = Attendance::where('branch_id', $request->branch_id)->where('attendance_date', $request->date)->get()->groupBy('section');
            $attendances = $attendances->map(function ($attendance, $section) use ($request) {
                $present = $attendance->where('status', 'P')->unique('student_id')->count();
                $absent = $attendance->where('status', 'A')->unique('student_id')->count();
                $boys = Student::where('section', $section)->where('campus', $request->branch_id)->where('gender', 'Male')->count();
                $girls = Student::where('section', $section)->where('campus', $request->branch_id)->where('gender', 'Female')->count();
                return [
                    'section' => $section,
                    'boys' => $boys,
                    'girls' => $girls,
                    'total' => $boys + $girls,
                    'present' => $present,
                    'absent' => $absent
                ];
            });
        }

        return view('report.attendancereport', compact('attendances'));
    }

    public function BatchList(Request $request)
    {
        $report = Student::join('branch as b', 'student.campus', '=', 'b.id')->selectRaw("b.name as campus,hostel_dayscholar,batch,section,COUNT(*) as strength,b.id")->where('academic_year', $this->academic_year)->groupBy('b.name', 'hostel_dayscholar', 'batch', 'section')->orderBy('b.id')->get();
        return view('report.batchlist', compact('report'));
    }
    public function SectionList(Request $request)
    {
        $branch = $request->branch ?? 0;
        $course = $request->course ?? 0;
        $data = Student::join('branch as b', 'student.campus', '=', 'b.id')->selectRaw("b.name as campus,batch,section,COUNT(*) as total,b.id,concat(gender,'-',hostel_dayscholar)gender,sum(ac_nonac='AC')ac,sum(ac_nonac='NON AC')nonac,sum(board_of_study_XII_std='SB')sb,sum(board_of_study_XII_std='CBSE')cbse,hostel_dayscholar")->where('academic_year', $this->academic_year)->where('b.id', $branch)->where('course', $course)->groupBy('section')->orderBy('hostel_dayscholar')->get();
        $grouped = $data->groupBy(['gender']);

        if ($request->isMethod('post')) {
            $section = $request->section;
            $students = Student::where('section', $section)->where('academic_year', $this->academic_year)->get();
            $branchname = $request->branchname;
            $pdf = Pdf::loadView("pdf.$request->view", compact('students', 'branchname', 'section'));
            return $pdf->download("$section-$request->view.pdf");
        }

        return view('report.sectionlist', compact('grouped'));
    }
    public function ExaminationAnalysis(Request $request) {
        $tests = Exam::where('academic_year', $this->academic_year)->groupBy('name')->orderBy('id', 'desc')->get();
        return view('report.examinationanalysis', compact('tests'));
    }

    public function LeastAttempted(Request $request) {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->get();

        if (!$exam) return back()->with('error', 'Exam not found.');

        $csvData = [];
        $csvData[] = ['Title', 'Least Attempted Questions'];
        $csvData[] = ['Exam Name', $exam->first()?->name];
        $csvData[] = [];
        $csvData[] = ['S.NO', 'Q.NO', 'PERCENTAGE'];
        $answers = ExamAnswer::whereIn('test_id',$exam->pluck('testid'))->selectRaw("q_no,count(student_id)total,sum(answer>0)least")->groupBy('q_no')->get();
        foreach ($answers as $key => $answer) {
            $csvData[] = [$key+1, $answer->q_no, round(($answer->least/$answer->total)*100, 2)];
        }
        $content = CsvServiceProvider::export($csvData);
        return Response::make($content, 200, ['Content-Type' => 'text/csv','Content-Disposition' => 'attachment; filename="Least Attempted Questions.csv"']);
    }

     public function CommonTrackTopper(Request $request) {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->get();
        if (!$exam) return back()->with('error', 'Exam not found.');

        $examname  = $exam->first()?->name;
        $examdate  = $exam->first()?->exam_date;
        $subjects = array_map('trim', explode(',', strtoupper($exam->first()?->subject_name)));

        $csvData = [];
        $csvData[] = ['Title', 'Common Track Topper'];
        $csvData[] = ['Exam Name', $examname];
        $csvData[] = [];

        $csvData[] = array_merge(['S.No', 'Student ID', 'Student Name', 'Branch', 'Exam Date', 'Exam Name', 'Batch'],$subjects,['Total']);

        $sumExpressions = [];
        foreach ($subjects as $sub) {
            $sumExpressions[] = "SUM(IF(subject = '{$sub}', mark, 0)) AS `{$sub}`";
        }
        $answers = ExamAnswer::whereIn('test_id',$exam->pluck('testid'))->selectRaw("student_id,sum(mark)mark,".implode(',', $sumExpressions))->groupBy('student_id')->orderBy('mark','desc')->get();

        foreach ($answers as $key => $answer) {
            $csvData[] = [$answer->student_id,$answer->student?->student_name,$answer->student?->branch?->name,$examdate,$examname,$answer->student?->batch];
            foreach ($subjects as $sub) {
            $csvData[] = $answer->$sub ?? 0;
            }
            $csvData[] = [$answer->mark];
        }

        $content = CsvServiceProvider::export($csvData);
        return Response::make($content, 200, ['Content-Type' => 'text/csv','Content-Disposition' => 'attachment; filename="Common Track Topper.csv"']);
    }
}
