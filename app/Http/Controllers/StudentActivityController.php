<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\StudentActivity;

class StudentActivityController extends Controller
{
    public function index()
    {

        $entries = StudentActivity::all();
        return view('studentactivity.index', compact('entries'));
    }

    public function create()
    {
        $sections = Student::select('section')->distinct()->pluck('section');
        $students = Student::select('student_id', 'user_name', 'student_name', 'section')->get();
        return view('studentactivity.create', compact('sections', 'students'));
    }
    public function store(Request $request)
    {
        
        StudentActivity::create([
            'student_id' => $request->student_id,
            'section' => $request->section,
            'date' => $request->date,
            'reason' => $request->reason,
        ]);
    
        return redirect()->route('studentactivity.index')
            ->with('success', 'Sick Room Entry added successfully!');
    }
    


    public function edit($id)
{
    $studentactivity = StudentActivity::findOrFail($id);
    $sections = Student::select('section')->distinct()->pluck('section');
    $students = Student::select('student_id', 'user_name', 'student_name', 'section')->get();

    return view('studentactivity.edit', compact('studentactivity', 'sections', 'students'));
}


public function update(Request $request, $id)
{
   

    $studentactivity = StudentActivity::findOrFail($id);

    $studentactivity->update([
        'student_id' => $request->student_id,
        'section' => $request->section,
        'date' => $request->date,
        'reason' => $request->reason,
    ]);

    return redirect()->route('studentactivity.index')->with('success', 'Entry updated successfully!');
}



    public function destroy(StudentActivity $studentactivity)
    {
        $studentactivity->delete();
        return redirect()->route('studentactivity.index')->with('success', 'Entry deleted successfully!');
    }
}
