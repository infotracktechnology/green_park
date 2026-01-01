<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.student');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $filePath = $request->file('csv_file')->getRealPath();
        $data = $this->parseCSV($filePath);

        if (empty($data)) {
            return back()->with('error', 'No data found in CSV.');
        }

        $header = array_map('strtolower', array_keys($data[0]));
        $existingColumns = array_map('strtolower', array_column(DB::getSchemaBuilder()->listColumns('students'), 'column_name'));
        $newColumns = array_diff($header, $existingColumns);

        DB::beginTransaction();
        try {
            if (!empty($newColumns)) {
                Schema::table('students', function (Blueprint $table) use ($newColumns) {
                    foreach ($newColumns as $column) {
                        if (!Schema::hasColumn('students', $column)) {
                            $table->string($column)->nullable();
                        }
                    }
                });
            }

            if ($request->operation === 'add') {
                $data = array_map(function ($row) use ($request) {
                    $row['academic_year'] = $request->academic_year;
                    $row['campus'] = $request->branch;
                    return $row;
                }, $data);
                foreach ($data as $row) {
                    Student::create($row);
                }
            } else {
                foreach ($data as $row) {
                    if (empty($row['student_id']) || !isset($row['student_id'])) continue;
                    unset($row['password_1'], $row['user_name']);
                    Student::where('student_id', $row['student_id'])->update($row);
                }
            }
            DB::commit();
            return back()->with('success', 'Students Data has been processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error:' . $e->getMessage());
        }
    }

    public function parseCSV($filePath)
    {
        $csvData = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = null;

            while (($row = fgetcsv($handle, 10000, ',')) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                if (!$header) {
                    $header = array_map('strtolower', $row);
                    continue;
                }

                if (count($header) !== count($row)) {
                    continue;
                }

                $csvData[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $csvData;
    }
}
