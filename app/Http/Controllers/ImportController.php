<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.student');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt', // Only CSV files up to 2MB
        ]);

        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();
            $data = $this->parseCSV($filePath);
            $data = array_map(function ($row) use ($request) {
             $row['academic_year'] = $request->academic_year;
            }, $data);
            try{
             if($request->operation == 'add'){
                foreach($data as $row){
                    Student::create($row);
                }
             }
             else{
                foreach($data as $row){
                    if(empty($row['student_id']) || !isset($row['student_id'])) continue;
                    unset($row['password_1'], $row['user_name']);
                    Student::where('student_id', $row['student_id'])->update($row);
                }
             }
            }
            catch(\Exception $e){
               return back()->with('error', 'Error: ' . $e->getMessage());
            }
            return back()->with('success', 'File processed successfully.');
        }
        return back()->with('error', 'No data found in the file or file upload failed.');
    }


    public function parseCSV($filePath){
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
            foreach ($row as $key => $value) {
                if ($value === ''){
                    $value = null;
                }
             $row[$key] = trim($value);
            }
            $csvData[] = array_combine($header, $row);
        }
        fclose($handle);
    }

    return $csvData;
}

}