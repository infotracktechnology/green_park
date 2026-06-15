<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Branch;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    function student_export(Request $request)
    {


    if ($request->has('fields')) {
        $students = Student::select($request->fields)
            ->when(auth()->user()->branch, fn($q) => $q->where('campus', auth()->user()->branch))
            ->when($request->academic_year, fn($q) => $q->where('academic_year', $request->academic_year))
            ->when($request->course, fn($q) => $q->where('course', $request->course))
            ->when($request->branch, fn($q) => $q->where('campus', $request->branch))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type', $request->coaching_type))
            ->get()
            ->toArray();

            $file = fopen('student_export.csv', 'w');
            $headers = array_keys($students[0]);
            fputcsv($file, $headers);
            foreach ($students as $student) {
                fputcsv($file, $student);
            }
            fclose($file);

            return response()->download('student_export.csv', 'student_export.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Expires' => '0'
            ]);
        }

        $academic_years = Student::whereNotNull('academic_year')->distinct()->pluck('academic_year');
        $courses = Student::whereNotNull('course')->distinct()->pluck('course');
        $branches = Branch::whereNotNull('name')->distinct()->pluck('name'); 
        $coaching_types = Student::whereNotNull('coaching_type')->distinct()->pluck('coaching_type');

        return view('student.export', compact('academic_years', 'courses', 'branches', 'coaching_types'));
    }
}