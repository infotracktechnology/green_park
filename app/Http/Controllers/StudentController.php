<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\student;
use Illuminate\Support\Facades\DB;


class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = DB::table('student')
            ->join('branch', 'student.campus', '=', 'branch.id')
            ->select('student.*', 'branch.name as campus')
            ->get();

        $students = $students->filter(function ($student) {
            return isset($student->id);
        });

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

            // 'email' => ['unique:student,email', 'email'],
        ]);
        $students = student::create($request->all());
        return to_route('student.index');
    }



    public function edit(Request $request, student $student) {
        $branches = DB::table('branch')->select('id', 'name')->get();

        return view('student.edit', compact('student', 'branches'));
    }

   
    public function update(Request $request, student $student) {

        // $data=$request->all();
        // $student->update($data);

        $student->update($request->all());
        return to_route('student.index');

    }
  
    public function destroy($id)
    {
       
    }
}
