<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Announcement;
use App\Models\Attendance;

class ReportController extends Controller
{
    public function section_exam(Request $request)
    {
        $sections = DB::table('student')->select('section')->distinct()->orderBy('section')->get();
        $tests = Exam::groupBy('name')->get();
        $test_name = $request->test_name ?? 0;

        if ($request->has('publish')) {
            $testIds = Exam::where('name', $test_name)->update(['publish' => $request->publish]);
            return view('report.section_exam', compact('sections', 'tests', 'test_name'));
        }

        if ($request->query('type') == 'overall') {
            $testIds = Exam::where('name', $test_name)->pluck('id')->toArray();
            $testIds = $testIds != '' ? $testIds : 0;
            $section = $request->section;
            $results = DB::table('exam_answer as a')->join('student as b', 'a.student_id', '=', 'b.student_id')->join('branch as c', 'b.campus', '=', 'c.id')->whereIn('a.test_id', $testIds)->where('b.section', $request->section)->select('a.test_id', 'a.student_id', 'a.mode as stmode', DB::raw('GROUP_CONCAT(DISTINCT a.subject) as subjects'), DB::raw('SUM(a.mark) as mark'), 'b.student_name', 'c.name', 'b.coaching_type', 'b.gender', 'b.section', 'a.test_id')->groupBy('a.student_id')->orderBy('test_id')->orderBy('student_name')->get();
            if (count($results) == 0) {
                return back()->with('error', 'No data found');
            }
            $testids = implode(',', $testIds);
            return view('report.overall_print', compact('results', 'test_name', 'section', 'testids'));
        }

        if ($request->query('type') == 'omr') {
            $testIds = Exam::where('name', $test_name)->pluck('id')->toArray();
            $testIds = $testIds != '' ? $testIds : 0;
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
        $report = Student::join('branch as b', 'student.campus', '=', 'b.id')->selectRaw("b.name as campus,hostel_dayscholar,batch,section,COUNT(*) as strength,b.id")->groupBy('b.name', 'hostel_dayscholar', 'batch', 'section')->orderBy('b.id')->get();
        return view('report.batchlist', compact('report'));
    }
}
