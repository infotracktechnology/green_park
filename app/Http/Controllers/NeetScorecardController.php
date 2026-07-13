<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Student;

class NeetScorecardController extends Controller
{

   public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:student,student_id',
            ]);

            $student = Student::where('student_id', $request->student_id)->first();
            if ($student) {
                if ($student->neet_file && file_exists(base_path($student->neet_file))) {
                    unlink(base_path($student->neet_file));
                }
                $student->update([
                    'neet_file' => null,
                ]);
            }

            return redirect()->route('neetscorecard.index')
                ->with('success', 'Student can now upload the NEET scorecard again.');
        }
        $course = Student::where('academic_year', $this->academic_year)->pluck('course')->unique()->values();

        $query = Student::with('branch')->where('academic_year', $this->academic_year)->whereNotNull('neet_file')->where('neet_file', '!=', '');

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->search . '%')
                ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->orderBy('student_name')->get();

        return view('neetscorecard.index', compact('students', 'course'));
    }

    public function edit($student_id)
    {
        $student = Student::where('student_id', $student_id)->firstOrFail();
        return view('neetscorecard.edit', compact('student'));
    }
    public function update(Request $request, $student_id)
    {
        $request->validate([
            'neetappno' => 'required',
            'neetrollno' => 'required',
            'neetmark' => 'required',
        ]);

        $student = Student::where('student_id', $student_id)->firstOrFail();

        $student->update([
            'neetappno' => $request->neetappno,
            'neetrollno' => $request->neetrollno,
            'neetcomm' => $request->neetcomm,
            'neetspecialcategory' => $request->neetspecialcategory,
            'neetmark' => $request->neetmark,
        ]);

        return redirect()->route('neetscorecard.index')
            ->with('success', 'NEET details updated successfully.');
    }

    public function saveRemark(Request $request)
    {
        Student::where('student_id', $request->student_id)
            ->update([
                'neetremark' => $request->remark
            ]);

        return response()->json([
            'status' => true
        ]);
    }
    
}
