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
                if($request->operation == 'add'){
                $row['campus'] = $request->branch;
                }
                if(isset($row['password_1'])){
                $row['password'] = bcrypt($row['password_1']);
                }
                return $row;
            }, $data);

            try{
                Student::upsert($data, ['student_id'], array_keys($data[0]));
                return back()->with('success', 'CSV file uploaded and data saved successfully!');
            }
            catch(\Exception $e){
                return back()->with('error', 'Error saving data:'.$e->getMessage());
            }
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