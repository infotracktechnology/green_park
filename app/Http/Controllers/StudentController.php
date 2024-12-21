<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request)
    {
        $students = DB::table('student')
            ->join('branch', 'student.campus', '=', 'branch.id')
            ->select('student.*', 'branch.name as campus')
            ->get();

        return view('student.index', compact('students'));
    }


    public function create(Request $request)
    {
        $branches = DB::table('branch')->select('id', 'name')->get();
        if($request->has('city')) {
            $pincodes = DB::table('district_list')->where('District', $request->city)->select('Pincode')->get();
            return response()->json($pincodes);
        }
        return view('student.create', compact('branches'));
    }

    /**
     * Store a newly created student in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            // 'student_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'ph_no1' => ['unique:student,ph_no1', 'numeric', 'min:10'],
            'ph_no2' => ['unique:student,ph_no2', 'numeric', 'min:10'],
            'father_ph_no' => ['unique:student,father_ph_no', 'numeric', 'min:10'],
            'mother_ph_no' => ['unique:student,mother_ph_no', 'numeric', 'min:10'],
        ]);

        Student::create($request->all());

        return redirect()->route('student.index')->with('success', 'Student created successfully.');
    }

    /**
     * Show the form for editing a student's details.
     */
    public function edit(Request $request, Student $student)
    {
        $branches = DB::table('branch')->select('id', 'name')->get();
        $districts = DB::table('district_list')->where('State', $student->state)->distinct()->orderBy('District')->get();
        $states = DB::table('district_list')->select('State')->distinct()->orderBy('State')->get();
        $pincodes = DB::table('district_list')->where('District', $student->district)->select('Pincode')->get();
        return view('student.edit', compact('student', 'branches',  'districts','states','pincodes'));
    }

    /**
     * Update the specified student in the database.
     */
    public function update(Request $request, Student $student)
    {
        $data = $request->all();
        $data['hostel_dayscholar'] = $data['hostel_dayscholar'] ?? null;

        $student->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('student.index')->with('success', 'Student details successfully updated.');
    }

    /**
     * Remove the specified student from the database.
     */
    public function destroy($id)
    {
        // Add delete logic here if needed
    }

    /**
     * Show the section shuffle page.
     */
    public function section()
    {
        $students = DB::table('student')
            ->join('branch', 'student.campus', '=', 'branch.id')
            ->select('student.*', 'branch.name as campus')
            ->get();

        return view('student.section', compact('students'));
    }

    /**
     * Update the section for selected students.
     */
    public function update_section(Request $request)
    {
        if ($request->student_ids) {
            foreach ($request->student_ids as $student_id) {
                $student = Student::find($student_id);
                if ($student) {
                    $student->section = $request->section;
                    $student->save();
                }
            }
        }

        return redirect()->route('section.student')->with('success', 'Student details successfully updated.');
    }
}
