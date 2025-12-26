<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnswerKey;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class AnswerkeyController extends Controller
{
    public function index(Request $request)
    {
        $answerkeys = AnswerKey::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->latest()->get();

        return view('answerkey.index', compact('answerkeys'));
    }


    public function create()
    {
     
        return view('answerkey.create',);
    }


    public function store(Request $request)
    {
         $data = $request->except('file');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('file')) {
            $originalName = $request->file('file')->getClientOriginalName();
            $fileName = time().'_'.$originalName;
            $request->file('file')->move('answer_key',$fileName);
            $data['file_path'] = 'answer_key/'.$fileName;
        }

        AnswerKey::create($data);

        return redirect()->route('answerkey.index')->with('success', 'Answer Key added successfully!');
    }


    public function edit(AnswerKey $answerkey)
    {
        $type = Student::StudentFilterQuery($answerkey->branch, $answerkey->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($answerkey->branch, $answerkey->course, $answerkey->type, $answerkey->category, $answerkey->batch, $answerkey->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($answerkey->branch, $answerkey->course, $answerkey->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        return view('answerkey.edit', compact('answerkey', 'type', 'section', 'students'));
    }

    public function update(Request $request, AnswerKey $answerkey)
    {
         $data = $request->except('file');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('file')) {
            $originalName = $request->file('file')->getClientOriginalName();
            $fileName = time().'_'.$originalName;
            $request->file('file')->move('answer_key',$fileName);
            $data['file_path'] = 'answer_key/'.$fileName;
        }

        $answerkey->update($data);

        return redirect()->route('answerkey.index')->with('success', 'Answer Key updated successfully!');
    }


    public function destroy(Request $request, $id=null)
    {
        if($request->has('ids')) {
            $answerkeys = AnswerKey::whereIn('id', $request->ids)->get();
            foreach ($answerkeys as $answerkey) {
                if (file_exists($answerkey->file_path)) {
                    unlink($answerkey->file_path);
                }
                $answerkey->delete();
            }
        }
        return redirect()->back()->with('success', 'Answer Key deleted successfully!');
    }


    public function answerkey()
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $answerkeys = AnswerKey::ForStudent($student);
        return view('student.answerkey', compact('answerkeys'));
    }
}
