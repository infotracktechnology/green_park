<?php

namespace App\Http\Controllers;

use App\Models\ExamName;
use App\Models\Branch;
use App\Models\Options;
use App\Models\student;
use Illuminate\Http\Request;

class ExamNameController extends Controller 
{
    public function index(Request $request)
    {
        $examNames = ExamName::all();

        return view('examname.index', compact('examNames'));
    }

    public function create(Request $request)
    {
        $testcategory = Options::where('type', 'testcategory')->first()->value;

        return view('examname.create', compact('testcategory'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

    foreach (['branch', 'coaching_type', 'category', 'batch'] as $field) {
        $data[$field] = isset($data[$field])
            ? implode(',', $data[$field])
            : null;
    }

    ExamName::create($data);

    return to_route('examname.index')->with('success', 'Exam created successfully');
    }

   public function edit(Request $request, $id)

{
    $examname = ExamName::findOrFail($id);
        $type = Student::StudentFilterQuery($examname->branch, $examname->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($examname->branch, $examname->course, $examname->type, $examname->category, $examname->batch, $examname->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($examname->branch, $examname->course, $examname->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        $testcategory = Options::where('type', 'testcategory')->first()->value;
// dd($examname);
    return view('examname.edit', compact('examname', 'type', 'section', 'students', 'testcategory'));
}

public function update(Request $request, $id)
{
    $examname = ExamName::findOrFail($id);

    $examname->update($request->all());

    return to_route('examname.index')
        ->with('success', 'Exam updated successfully');
}

    public function destroy(Request $request, $id)
    {
       $examname = ExamName::findOrFail($id);
        $examname->delete();

        return to_route('examname.index')->with('success', 'Exam deleted successfully');
    }
}