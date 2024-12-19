<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use Illuminate\Support\Facades\Storage;
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
            'branch' => 'required',
            'csv_file' => 'required|mimes:csv,txt|max:2048', // Only CSV files up to 2MB
        ]);

        // Step 2: Handle file upload
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();

            // Step 3: Parse the CSV file
            $data = $this->parseCSV($filePath);

            // Step 4: Insert data into the database
            if (!empty($data)) {
                foreach ($data as $row) {
                    DB::table('admissions')->insert([
                        'branch_id' => $request->input('branch'),  // Assign branch ID
                        'name' => $row['name'] ?? null,
                        'email' => $row['email'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                return back()->with('success', 'CSV file uploaded and data saved successfully!');
            }
        }

        return back()->with('error', 'No data found in the file or file upload failed.');
    }

    /**
     * Parse CSV File into an Associative Array
     */
    private function parseCSV($filePath)
{
    $csvData = [];
    if (($handle = fopen($filePath, 'r')) !== false) {
        $header = null;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Ignore empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Capture header from the first row
            if (!$header) {
                $header = array_map('strtolower', $row); // Normalize to lowercase
                continue;
            }

            // Validate that the row matches header column count
            if (count($header) !== count($row)) {
                // Log or skip the invalid row
                \Log::warning('Skipping invalid row: ' . implode(',', $row));
                continue;
            }

            // Combine header and row
            $csvData[] = array_combine($header, $row);
        }
        fclose($handle);
    }

    return $csvData;
}

}