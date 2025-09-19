<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use App\Models\Examportion;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

use Illuminate\Support\Facades\DB;

class ExamPortionController extends Controller
{
    public function index()
    {
        $examportions = Examportion::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, function ($query) {
                $query->where('branch_id', 'like', '%' . auth()->user()->branch . '%');
            })
            ->get();

        return view('examportion.index', compact('examportions'));
    }
    public function create()
    {

        return view('examportion.create');
    }
    public function store(Request $request)
    {
        $data = $request->except('attachment');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('attachment')) {
            $originalName = $request->file('attachment')->getClientOriginalName();
            $fileName = time().'_'.$originalName;
            $request->file('attachment')->move('assets/examportion',$fileName);
            $data['attachment'] = 'assets/examportion/'.$fileName;
        }

        Examportion::create($data);
        return to_route('examportion.index')->with('success', 'Examportion created successfully');
    }

    public function edit(Examportion $examportion)
    {
        $type = Student::StudentFilterQuery($examportion->branch, $examportion->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($examportion->branch, $examportion->course, $examportion->type, $examportion->category, $examportion->batch, $examportion->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($examportion->branch, $examportion->course, $examportion->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        return view('examportion.edit', compact('examportion', 'type', 'section', 'students'));
    }

    public function update(Request $request, Examportion $examportion)
    {
        $data = $request->except('attachment');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

         if ($request->hasFile('attachment')) {
            $originalName = $request->file('attachment')->getClientOriginalName();
            $fileName = time().'_'.$originalName;
            $request->file('attachment')->move('assets/examportion',$fileName);
            $data['attachment'] = 'assets/examportion/'.$fileName;
        }

        $examportion->update($data);
        return redirect()->route('examportion.index')->with('success', 'Examportion updated successfully.');
    }


    public function destroy(Request $request, Examportion $examportion)
    {

        if ($examportion->attachment && file_exists(public_path($examportion->attachment))) {
            unlink(public_path($examportion->attachment));
        }
        $examportion->delete();

        session()->flash('success', 'Examportion deleted successfully');
        return to_route('examportion.index');
    }

    public function examportion(Request $request)
    {
        $student = Student::where('student_id', auth()->user()->student_id)->first();
        $examportions = Examportion::ForStudent($student);
        return view('student.examportion', compact('examportions'));
    }
}
