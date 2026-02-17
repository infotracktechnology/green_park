<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MockTest;
use App\Models\ExamAnswer;
use App\Models\Exam;
use App\Models\Student;

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
        if ($request->isMethod('POST')) {
            dd($request->all());
        }

        return view('student.mocktest', compact('mocktests'));
    }
}
