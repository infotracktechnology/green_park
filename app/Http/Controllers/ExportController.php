<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ExportController extends Controller
{
    public function student_export(Request $request)
    {
        if ($request->has('fields')) {
            $requestedFields = $request->input('fields', []);

            if (!is_array($requestedFields) || empty($requestedFields)) {
                return redirect()->back()->with('error', 'Please select at least one field to export.');
            }

            // SQL Injection / invalid column error thavirkka valid columns mattum filter panrom
            $tableColumns = Schema::getColumnListing((new Student())->getTable());
            $fields = array_values(array_intersect($requestedFields, $tableColumns));

            if (empty($fields)) {
                return redirect()->back()->with('error', 'Invalid fields selected for export.');
            }

            $userBranch = auth()->user()?->branch;

            $query = Student::select($fields)
                ->when($userBranch, fn($q) => $q->where('campus', $userBranch))
                ->when($request->academic_year, fn($q) => $q->where('academic_year', $request->academic_year))
                ->when($request->course, fn($q) => $q->where('course', $request->course))
                ->when($request->branch, fn($q) => $q->where('campus', $request->branch))
                ->when($request->coaching_type, fn($q) => $q->where('coaching_type', $request->coaching_type));

            // Records irukka nu fast-aa check panrom (without loading data)
            if (!(clone $query)->exists()) {
                return redirect()->back()->with('error', 'No student records found matching the selected criteria.');
            }

            $fileName = 'student_export_' . now()->format('Y-m-d_His') . '.csv';

            return response()->streamDownload(function () use ($query, $fields) {
                set_time_limit(0);

                $handle = fopen('php://output', 'w');

                // Excel-la UTF-8 characters (like Tamil/symbols) olunga open aaga BOM add panrom
                fwrite($handle, "\xEF\xBB\xBF");

                // CSV Header row
                fputcsv($handle, $fields);

                $rowCount = 0;

                // toBase()->cursor() Eloquent objects create pannama low memory-la stream pannum
                foreach ($query->toBase()->cursor() as $student) {
                    fputcsv($handle, (array) $student);

                    // Ovvoru 500 rows-kum output flush panradhaala Apache/PHP timeout aagadhu
                    if (++$rowCount % 500 === 0) {
                        flush();
                    }
                }

                fclose($handle);
            }, $fileName, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        $academic_years = Student::whereNotNull('academic_year')->distinct()->pluck('academic_year');
        $courses = Student::whereNotNull('course')->distinct()->pluck('course');
        $branches = Branch::whereNotNull('name')->distinct()->pluck('name');
        $coaching_types = Student::whereNotNull('coaching_type')->distinct()->pluck('coaching_type');

        return view('student.export', compact('academic_years', 'courses', 'branches', 'coaching_types'));
    }
}