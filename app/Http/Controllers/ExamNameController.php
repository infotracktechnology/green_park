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
            
    $coachingTypes = ExamName::whereNotNull('coaching_type')->pluck('coaching_type')->flatMap(fn($item) => explode(',', $item))->map(fn($item) => trim($item))->unique()->values();

    $courses = ExamName::whereNotNull('course')->pluck('course')->unique()->values();

    $batches = ExamName::whereNotNull('batch')->pluck('batch')->flatMap(fn($item) => explode(',', $item))->map(fn($item) => trim($item))->unique()->values();

    $query = ExamName::query();

    if ($request->filled('coaching_type')) {
        $query->where('coaching_type', 'LIKE', '%' . $request->coaching_type . '%');
    }

    if ($request->filled('course')) {
        $query->where('course', $request->course);
    }

    if ($request->filled('batch')) {
        $query->where('batch', 'LIKE', '%' . $request->batch . '%');
    }
        $examNames = $query->get();

        return view('examname.index', compact('examNames', 'coachingTypes', 'courses', 'batches'));
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