<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;


class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = DB::table('student')
            ->join('branch', 'student.campus', '=', 'branch.id')
            ->select('student.*', 'branch.name as campus')
            ->get();

        return view('student.index', compact('students'));
    }


    public function create()
    {
        $branches = DB::table('branch')->select('id', 'name')->get();
        return view('student.create', compact('branches'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'ph_no1' => ['unique:student,ph_no1', 'numeric', 'min:10'],
            'ph_no2' => ['unique:student,ph_no2', 'numeric', 'min:10'],
            'father_ph_no' => ['unique:student,father_ph_no', 'numeric', 'min:10'],
            'mother_ph_no' => ['unique:student,mother_ph_no', 'numeric', 'min:10'],
        ]);
        $students = Student::create($request->all());
        return redirect()->route('student.index');
    }



    public function edit(Request $request, Student $student)
    {
        $branches = DB::table('branch')->select('id', 'name')->get();
        $districts = DB::table('district_list')->where('State', $student->state)->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('student.edit', compact('student', 'branches',  'districts','states'));
    }


    public function update(Request $request, Student $student)
    {
        $data = $request->all();
        $data['hostel_dayscholar'] = $data['hostel_dayscholar'] ?? null;
        $student->update($data);
    
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
    
        session()->flash('success', 'Student details successfully updated.');
        return redirect()->route('student.index');
    }
    

    public function destroy($id) {

    }

    public function section()
    {
          $students = DB::table('student')
            ->join('branch', 'student.campus', '=', 'branch.id')
            ->select('student.*', 'branch.name as campus')
            ->get();
            return view('student.section')->with('students', $students);
        
    }

    public function update_section(Request $request)
    {
      if ($request->student_ids) {
        foreach ($request->student_ids as $student_id) {
          $student = Student::find($student_id);
          $student->section = $request->section;
          $student->save();
        }
      }
      session()->flash('success', 'Student details successfully updated.');
      return redirect()->route('section.student');

    }
}
