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
        // Fetch branches from the 'green-park' database
        $branches = Branch::all();

        return view('import.student', ['branches' => $branches]);
    }

    public function upload(Request $request)
    {
        // Step 1: Validate the request
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt', // Only CSV files up to 2MB
        ]);

        $branch = $request->branch ?? '';
        $academic_year = $request->academic_year ?? '';
        // Step 2: Handle file upload
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();

            // Step 3: Parse the CSV file
            $data = $this->parseCSV($filePath);

            // Step 4: Insert data into the database
            if (!empty($data)) {
                foreach ($data as $row) {
                    
                    foreach($row as $key => $value){
                        $row[$key] = is_string($value) && $key != 'password_1'   ? mb_convert_case($value, MB_CASE_TITLE, 'UTF-8') : $value;
                    }
                    

                    $student_id = $row['student_id'] ?? 0;
                      
                    $student = Student::where('student_id', $student_id)->first();
                   if($student){
                    try{
                      $update = $student->update($row);
                    }catch(\Exception $e){
                        return back()->with('error', 'Error updating student: ' . $e->getMessage());
                    }
                   }
                   else{
                    $row = array_merge($row, ['campus' => $branch, 'academic_year' =>$academic_year]);
                    try{
                    $create = Student::create($row);
                    }catch(\Exception $e){
                        return back()->with('error', 'Error creating student: ' . $e->getMessage());
                    }
                    
                   }
                }
                
            }
            return back()->with('success', 'CSV file uploaded and data saved successfully!');
        }

        return back()->with('error', 'No data found in the file or file upload failed.');
    }


    public function parseCSV($filePath){
    $csvData = [];
    if (($handle = fopen($filePath, 'r')) !== false) {
        $header = null;

        while (($row = fgetcsv($handle, 10000, ',')) !== false) {
            // Ignore empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Capture header from the first row
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