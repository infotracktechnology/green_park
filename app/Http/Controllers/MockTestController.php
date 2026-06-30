<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MockTest;
use App\Models\ExamAnswer;
use App\Models\Exam;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
class MockTestController extends Controller
{
    public function index(Request $request)
    {
        $mocktests = MockTest::where('academic_year', $this->academic_year)->when(auth()->user()->branch, fn($q) => $q->where('branch', 'like', '%' . auth()->user()->branch . '%'))->when($request->coaching_type, fn($q) => $q->where('coaching_type', 'like', '%' . $request->coaching_type . '%'))->latest()->get();

        return view('mocktest.index', compact('mocktests'));
    }
    public function create()
    {
        $exams = Exam::where('academic_year', $this->academic_year)->groupBy('name')->get();
        return view('mocktest.create', compact('exams'));
    }
    public function store(Request $request)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        MockTest::create($data);
        return to_route('mocktest.index')->with('success', 'Mock Test created successfully');
    }

    public function edit(MockTest $mocktest)
    {
        $type = Student::StudentFilterQuery($mocktest->branch, $mocktest->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($mocktest->branch, $mocktest->course, $mocktest->type, $mocktest->category, $mocktest->batch, $mocktest->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($mocktest->branch, $mocktest->course, $mocktest->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        $exams = Exam::where('academic_year', $this->academic_year)->groupBy('name')->get();

        return view('mocktest.edit', compact('mocktest', 'type', 'section', 'students', 'exams'));
    }

    public function update(Request $request, MockTest $mocktest)
    {
        $data = $request->except('attachment');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $mocktest->update($data);
        return redirect()->route('mocktest.index')->with('success', 'Mocktest updated successfully.');
    }


    public function destroy(Request $request, $id = null)
    {
        if ($request->has('ids')) {
            $mocktests = MockTest::whereIn('id', $request->ids)->get();
            foreach ($mocktests as $mocktest) {
                $mocktest->delete();
            }
        }

        return redirect()->back()->with('success', 'Examportion deleted successfully.');
    }

    public function MockTest(Request $request)
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $mocktests = MockTest::ForStudent($student);
        $exam = null;
        $timer=null;
        if ($request->has('exam_name')) {
             $existinganswer = ExamAnswer::where('testname', $request->exam_name)->where('student_id', auth('student')->user()->student_id)->first();
            if ($existinganswer) {
                return redirect()->back()->with('error', 'You have already attempted this exam and cannot attempt it again.');
            }
            
            $exam = Exam::where('name', $request->exam_name)->first();
            $mockexam = MockTest::where('exam_name', $request->exam_name)->first();
            $timer = Carbon::parse($mockexam->end_at)->diffInSeconds(now());
        }

        if ($request->isMethod('POST')) {
            $examanswer = ExamAnswer::where('testname', $request->testname)->where('student_id', $request->student_id)->first();
            if ($examanswer) {
                ExamAnswer::where('testname', $request->testname)->where('student_id', $request->student_id)->delete();
            }

            $answers = [];
            for ($q = 1; $q <= 180; $q++) {
                $answer = $request->answers[$q] ?? 0;
                $answers[] = ['q_no' => $q, 'answer' => $answer, 'student_id' => $request->student_id, 'testname' => $request->testname, 'test_id' => $request->test_id, 'academic_year' => $this->academic_year, 'subject' => $request->subject[$q]];
            }
            ExamAnswer::insert($answers);
            session()->put('download_pdf', $request->testname);
            return to_route('student.mock');
        }

        return view('student.mocktest', compact('mocktests', 'exam', 'student', 'timer'));
    }
    public function downloadMockTestPdf($testname)
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $exam = Exam::where('name', $testname)->first();
        $answersData = ExamAnswer::where('testname', $testname)->where('student_id', $student->student_id)->get()->keyBy('q_no');

        $answers = [];
        for ($i = 1; $i <= $exam->total_questions; $i++) {
            $answers[$i] = $answersData[$i]->answer ?? 0;
        }

        $pdf = PDF::loadView('pdf.student_mocktest', compact('student', 'exam', 'answers', 'testname'));

        return $pdf->download($testname.'-'.$student->student_id.'.pdf');
    }
}
