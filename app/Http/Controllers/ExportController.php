<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ExportController extends Controller
{
  function student_export(Request $request)
  {
    if ($request->has('category')) {

      $columns = explode(',', $request->category);
      $students = Student::select($columns)->get()->toArray();

      // Write data to CSV
      $file = fopen('student_export.csv', 'w');
      $headers = array_keys($students[0]);
      fputcsv($file, $headers);
      foreach ($students as $student) {
        fputcsv($file, $student);
      }
      fclose($file);
      return response()->download('student_export.csv','student_export.csv',['Content-Type: text/csv','Cache-Control'=>'no-cache, must-revalidate','Expires'=>'0']);
    }
    return view('student.export');
  }
}
