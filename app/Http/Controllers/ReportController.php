<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\Student;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Options;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Providers\CsvServiceProvider;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function section_exam(Request $request)
    {
        $sections = Student::when(auth()->user()->branch, fn($query) => $query->where('campus', auth()->user()->branch))->where('section', '!=', '')->select('section')->distinct()->orderBy('section')->get();

        $category = Options::where('type', 'testcategory')->first()->value ?? [];

        $exams = [];

        if ($request->has('testcategory')) {
            $exams = Exam::where('testcategory', $request->testcategory)->select('name')->distinct()->get()->pluck('name');
        }

        $test_name = $request->test_name ?? 0;

        if ($request->query('type') == 'overall') {
            $section = $request->section;
            $exam = Exam::where('name', $test_name)->first();
        
        $answers = ExamAnswer::selectRaw("exam_answer.*,a.student_name")->join('student as a', 'exam_answer.student_id', '=', 'a.student_id')->where('a.section', $section)->whereIn('test_id', Exam::where('name', $test_name)->pluck('testid'))->get();

            $subjects = $answers->pluck('subject')->unique()->values()->toArray();
            $results = $answers->groupBy('student_id')->map(function($logs) use ($subjects) {
            $student = $logs->first()->student;
            $subjectStats = collect($subjects)->mapWithKeys(function($subject) use ($logs) {
                $sub = $logs->where('subject', $subject);
            return [$subject=>['right' => $sub->where('mark', 4)->count(),'wrong' => $sub->where('mark', -1)->count(),'left'  => $sub->where('mark', 0)->count(), 'total' => $sub->sum('mark')]];
             });
             return ['student_id' => $student->student_id, 'student_name' => $student->student_name, 'test_id' => $logs->first()->test_id, 'subjects' => $subjectStats, 'total' => $subjectStats->sum('total')];
             })->values();
            $pdf = Pdf::loadView('report.overall_print', compact('results', 'subjects', 'test_name', 'section'));
            return $pdf->download("OVERALLPRINT-$exam->name - $section.pdf");
        }

        if ($request->query('type') == 'omr') {
            $section = $request->section;
            $exam = Exam::where('name', $test_name)->first();

           $answers = ExamAnswer::selectRaw("exam_answer.*,a.student_name")->join('student as a', 'exam_answer.student_id', '=', 'a.student_id')->where('a.section', $section)->whereIn('test_id', Exam::where('name', $test_name)->pluck('testid'))->orderBy('student_id')->orderBy('q_no')->get();

            $students = $answers->groupBy('student_id')->map(function ($items) {
                $student = $items->first()->student;
                $testId = $items->first()->test_id;
                $subjects = $items->groupBy('subject')->map(function ($sub) {
                    return ['subject' => $sub->first()->subject, 'right' => $sub->where('mark', 4)->count(), 'wrong' => $sub->where('mark', -1)->count(), 'left'  => $sub->where('mark', 0)->count(), 'total' => $sub->sum('mark'), 'max' => $sub->count() * 4];
                })->values();
                $totalRight = $subjects->sum('right');
                $totalWrong = $subjects->sum('wrong');
                $totalLeft  = $subjects->sum('left');
                $totalMarks = $subjects->sum('total');
                $maxMarks   = $subjects->sum('max');
                $splitAnswers = $items->chunk(ceil($items->count() / 4))->values();
                return ['student_id'   => $student->student_id, 'student_name' => $student->student_name, 'test_id' => $testId, 'answers' => $splitAnswers, 'subjects' => $subjects, 'totals' => compact('totalRight', 'totalWrong', 'totalLeft', 'totalMarks', 'maxMarks')];
            });
            $pdf = Pdf::loadView('report.omr_print', compact('answers', 'exam', 'students', 'test_name'));
            return $pdf->download("OMRPRINT-$exam->name - $section.pdf");
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
    public function ExaminationAnalysis(Request $request)
    {
        $category = Options::where('type', 'testcategory')->first()->value ?? [];
        $exams = [];

        if ($request->has('testcategory')) {
            $exams = Exam::where('testcategory', $request->testcategory)->select('name')->distinct()->get()->pluck('name');
        }

        return view('report.examinationanalysis', compact('category', 'exams'));
    }
    public function LeastAttempted(Request $request)
    {
        $exam = Exam::where('academic_year', $this->academic_year)->where('name', $request->test_name)->first();

        if (!$exam) return back()->with('error', 'Exam not found.');

        $answers = ExamAnswer::whereIn('test_id', Exam::where('name', $exam->name)->pluck('testid'))->selectRaw("q_no, COUNT(student_id) AS total, SUM(answer > 0) AS least")->groupBy('q_no')->get();

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

        $answers = ExamAnswer::whereIn('test_id', Exam::where('name', $exam->name)->pluck('testid'))->selectRaw("student_id, SUM(mark) AS total, $sumExp")->groupBy('student_id')->orderByDesc('total')->get();

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
        $answers = ExamAnswer::whereIn('test_id', Exam::where('name', $exam->name)->pluck('testid'))->selectRaw("student_id, SUM(mark) AS total, $expr")->groupBy('student_id')->orderByDesc('total')->get();

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


        $answers = ExamAnswer::join('student as s', 'exam_answer.student_id', '=', 's.student_id')->whereIn('test_id', Exam::where('name', $exam->name)->pluck('testid'))->selectRaw("exam_answer.student_id, s.student_name, s.campus, s.batch, s.section,SUM(IF(mark=4,1,0)) AS overall_correct,SUM(IF(mark=-1,1,0)) AS overall_wrong,SUM(IF(mark=0,1,0)) AS overall_unattempted,COUNT(*) AS overall_total,SUM(mark) AS total,$expr")->where('s.section', '!=', '')->groupBy('exam_answer.student_id')->orderBy('s.section')->orderByDesc('total')->get();

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

        $answers = ExamAnswer::whereIn('test_id', Exam::where('name', $exam->name)->pluck('testid'))->selectRaw("student_id,SUM(IF(mark=4,1,0)) AS overall_correct,SUM(IF(mark=-1,1,0)) AS overall_wrong,SUM(IF(mark=0,1,0)) AS overall_unattempted,COUNT(*) AS overall_total,SUM(mark) AS total,$expr")->groupBy('student_id')->orderByDesc('total')->get();

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

        $csvHeaders = ['SNo', 'Branch Name', 'Actual STR', 'Appeared STR', 'AB', 'Max Marks', 'Min Marks'];
        foreach ($ranges as $r) {
            $csvHeaders = array_merge($csvHeaders, ["{$r[0]}-{$r[1]}"]);
        }

        $studentmark = ExamAnswer::whereIn('test_id', Exam::where('name', $exam->name)->pluck('testid'))->selectRaw("student_id,SUM(mark) AS total")->groupBy('student_id');

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

        $csvHeaders = ['SNo', 'Section', 'Actual STR', 'Appeared STR', 'AB', 'Max Marks', 'Min Marks'];
        foreach ($ranges as $r) {
            $csvHeaders = array_merge($csvHeaders, ["{$r[0]}-{$r[1]}"]);
        }

        $studentmark = ExamAnswer::whereIn('test_id', Exam::where('name', $exam->name)->pluck('testid'))->selectRaw("student_id,SUM(mark) AS total")->groupBy('student_id');

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

        $answers = ExamAnswer::whereIn('test_id', Exam::where('name', $exam->name)->pluck('testid'))->selectRaw("student_id,SUM(IF(mark=4,1,0)) AS overall_correct,SUM(IF(mark=-1,1,0)) AS overall_wrong,SUM(IF(mark=0,1,0)) AS overall_unattempted,COUNT(*) AS overall_total,SUM(mark) AS total,$expr")->groupBy('student_id')->orderByDesc('total')->get();



        $csvHeaders = ['Student ID', 'Student Name', 'Branch', 'Batch', 'Section', 'Exam Date', 'Overall Correct', 'Overall Wrong', 'Overall UnAttempted', 'Overall Total'];

        foreach ($subjects as $s) {
            $csvHeaders = array_merge($csvHeaders, ["{$s} Correct", "{$s} Wrong", "{$s} UnAttempted", "{$s} Total"]);
        }

        $csvData = [['Title', 'Subject Wise Marks'], ['Exam Name', $exam->name], [], $csvHeaders];
        foreach ($answers as $a) {
            $row = [$a->student_id, $a->student?->student_name, $a->student?->branch?->name, $a->student?->batch, $a->student?->section, $a->exam_date, $a->overall_correct, $a->overall_wrong, $a->overall_unattempted, $a->total];
            foreach ($subjects as $s) {
                $row = array_merge($row, [$a->{"{$s}_CORRECT"} ?? 0, $a->{"{$s}_WRONG"} ?? 0, $a->{"{$s}_UNATTEMPTED"} ?? 0, $a->{"{$s}_MARK"} ?? 0]);
            }
            $csvData[] = $row;
        }

        return response(CsvServiceProvider::export($csvData), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="Overall_Marks_Analysis.csv"']);
    }
}
