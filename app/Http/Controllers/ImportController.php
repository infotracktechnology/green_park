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
use Carbon\Carbon;

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
                if (isset($row['admission_date']) && !empty($row['admission_date'])) {
                    $row['admission_date'] = $this->parseDate($row['admission_date']);
                }
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
                    if (isset($row['admission_date']) && !empty($row['admission_date'])) {
                        $row['admission_date'] = $this->parseDate($row['admission_date']);
                    }
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

    public function parseDate($dateStr)
    {
        if (empty($dateStr)) {
            return null;
        }
        $dateStr = trim($dateStr);

        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d', 'm/d/Y', 'j-n-Y', 'j/n/Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateStr)->format('Y-m-d');
            } catch (\Exception $e) {
                // try next
            }
        }

        try {
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse(str_replace('/', '-', $dateStr))->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
    public function HostelArrivalUpload(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('import.hostelarrival');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $filePath = $request->file('csv_file')->getRealPath();
        $data = $this->parseCSV($filePath);

        if (empty($data)) {
            return back()->with('error', 'No data found in CSV.');
        }

        $table = 'hostel_arrival_detials';

        $allowedColumns = [
            'student_id', 'admission_date', 'academic_year',
            'campus', 'course', 'section', 'batch',
            'coaching_type', 'hostel_dayscholar', 'student_name',
            'initial', 'roll_no', 'neetmark', 'gender',
            'father_name', 'father_ph_no', 'mother_name',
            'mother_ph_no', 'ac_nonac', 'door_no',
            'street_name', 'permanent_address', 'city',
            'district', 'state', 'pincode',
            'board_of_study_x_std', 'board_of_study_xii_std',
            'hostel_id', 'room_no', 'cots_no', 'allocation_date',
        ];

        $inserted = 0;
        $skipped = 0;
        $processedIds = [];

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                $row = array_change_key_case($row, CASE_LOWER);
                $record = [];

                foreach ($allowedColumns as $column) {
                    if (array_key_exists($column, $row)) {
                        $value = trim((string) $row[$column]);
                        $record[$column] = $value === '' ? null : $value;
                    }
                }

                if (empty($record['student_id'])) {
                    $skipped++;
                    continue;
                }

                $studentId = $record['student_id'];

                if (isset($processedIds[$studentId])) {
                    $skipped++;
                    continue;
                }
                $processedIds[$studentId] = true;
                if (DB::table($table)->where('student_id', $studentId)->exists()) {
                    $skipped++;
                    continue;
                }

                foreach (['admission_date', 'allocation_date'] as $dateColumn) {
                    if (!empty($record[$dateColumn])) {
                        $record[$dateColumn] = $this->parseDate($record[$dateColumn]);

                        if (!$record[$dateColumn]) {
                            throw new \Exception("Invalid {$dateColumn} for student {$studentId}");
                        }
                    }
                }
                $record['created_at'] = now();
                $record['updated_at'] = now();
                DB::table($table)->insert($record);
                $inserted++;
            }
            DB::commit();
            return back()->with('success', "Import completed. Inserted: {$inserted}, Skipped: {$skipped}");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Hostel arrival CSV import failed', ['message' => $e->getMessage()]);

            return back()->with('error', 'CSV import failed. Check the CSV format and database columns.');
        }
    }
}
