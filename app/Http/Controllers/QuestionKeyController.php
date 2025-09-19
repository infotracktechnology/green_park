<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\QuestionKey;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;



class QuestionKeyController extends Controller
{
    public function index()
    {
        $questionkeys = QuestionKey::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, function ($query) {
                $query->where('branch', 'like', '%' . auth()->user()->branch . '%');
            })
            ->latest()
            ->get();
        return view('questionkey.index', compact('questionkeys'));
    }


    public function create()
    {
        return view('questionkey.create');
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
            $request->file('file')->move('questionkey',$fileName);
            $data['file_path'] = 'questionkey/'.$fileName;
        }

        QuestionKey::create($data);
        return redirect()->route('questionkey.index')->with('success', 'Question Key added successfully!');
    }


    public function edit(QuestionKey $questionkey)
    {
        $type = Student::StudentFilterQuery($questionkey->branch, $questionkey->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($questionkey->branch, $questionkey->course, $questionkey->type, $questionkey->category, $questionkey->batch, $questionkey->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($questionkey->branch, $questionkey->course, $questionkey->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        return view('questionkey.edit', compact('questionkey', 'type', 'section', 'students'));
    }

    public function update(Request $request, QuestionKey $questionkey)
    {
       $data = $request->except('file');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('file')) {
            $originalName = $request->file('file')->getClientOriginalName();
            $fileName = time().'_'.$originalName;
            $request->file('file')->move('questionkey',$fileName);
            $data['file_path'] = 'questionkey/'.$fileName;
        }

        $questionkey->update($data);

        return redirect()->route('questionkey.index')->with('success', 'Question Key updated successfully!');
    }


    public function destroy(QuestionKey $questionkey)
    {
        if (file_exists($questionkey->file_path)) {
            unlink($questionkey->file_path);
        }

        $questionkey->delete();

        return redirect()->route('questionkey.index')->with('success', 'Question Key deleted successfully!');
    }

    public function questionkey()
    {   
        $students = Student::where('student_id', auth('student')->user()->student_id)->first();
        $questionkeys = QuestionKey::ForStudent($students);
        return view('student.questionkey', compact('questionkeys'));
    }
}
