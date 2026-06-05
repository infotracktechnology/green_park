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

        DB::beginTransaction();
        try {
            $data = array_map(function ($row) use ($request) {
                $row['academic_year'] = $request->academic_year;
                //$row['campus'] = $request->branch;
                return $row;
            }, $data);

            foreach ($data as $row) {
                if(empty($row['campus']) || !isset($row['campus'])) continue;
                DB::table('student')->insert($row);
            }
            DB::commit();
            return back()->with('success', 'Students Data has been processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error:' . $e->getMessage());
        }
    }

    public function StudentUpdate(Request $request)
    {
        if ($request->isMethod('post')) {
            $filePath = $request->file('csv_file')->getRealPath();
            $data = $this->parseCSV($filePath);
            if (empty($data)) {
                return back()->with('error', 'No data found in CSV.');
            }
            DB::beginTransaction();
            try {
                foreach ($data as $row) {
                    if (empty($row['student_id']) || !isset($row['student_id'])) continue;
                    Student::where('student_id', $row['student_id'])->update($row);
                }
                DB::commit();
                return back()->with('success', 'Students Second Batch Data has been Updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error:' . $e->getMessage());
            }
        }
        return view('import.studentupdate');
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
