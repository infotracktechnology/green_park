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

        Student::where('student_id', $request->student_id)
            ->update([
                'neet_file' => ""
            ]);

        return redirect()->route('neetscorecard.index')
            ->with('success', 'Student can now upload the NEET scorecard again.');
    }

    $query = Student::with('branch')
        ->where('academic_year', $this->academic_year)
        ->whereNotNull('neet_file')
        ->where('neet_file', '!=', '');

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('student_name', 'like', '%' . $request->search . '%')
              ->orWhere('student_id', 'like', '%' . $request->search . '%');
        });
    }

    $students = $query->orderBy('student_name')->get();

    return view('neetscorecard.index', compact('students'));
}
}
