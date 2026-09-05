<?php
namespace App\Http\Controllers;

use App\Models\Worksheet;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class WorksheetController extends Controller
{
    public function index(Request $request)
    {
        $worksheets = Worksheet::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'worksheets' => $worksheets], 200);
        }

        return view('worksheet.index', compact('worksheets'));
    }

    public function create()
    {
        return view('worksheet.create');
    }

    public function show(Request $request, Worksheet $worksheet)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'worksheet' => $worksheet]);
        }

        return redirect()->route('worksheet.index');
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token', '_method', 'file']);

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
            } else {
                $data[$field] = null;
            }
        }

        if (isset($data['usertype']) && $data['usertype'] === 'INDIVIDUAL') {
            $data['gender'] = null;
            $data['section'] = null;
        } elseif (isset($data['usertype']) && $data['usertype'] === 'GROUP') {
            $data['students'] = null;
            if (empty($data['gender'])) {
                $data['gender'] = 'All';
            }
        }

        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;
        if ($data['is_schedule'] == 0) {
            $data['start_at'] = null;
        }

        $file_path = [];
        if ($request->hasFile('file')) {
            $destinationPath = 'worksheet';
            foreach ($request->file('file') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time().'_'.$originalName;
                    $file->move($destinationPath, $fileName);
                    $file_path[] = 'worksheet/'.$fileName;
                }
            }
        }
        $data['file_path'] = !empty($file_path) ? array_values($file_path) : null;
        $worksheet = Worksheet::create($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Worksheet created successfully.', 'data' => $worksheet], 200);
        }

        return redirect()->route('worksheet.index')->with('success', 'Worksheet created successfully.');
    }

    public function edit(Request $request, Worksheet $worksheet)
    {
        $type = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, $worksheet->type, $worksheet->category, $worksheet->batch, $worksheet->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, $worksheet->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'worksheet' => $worksheet, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('worksheet.edit', compact('worksheet', 'type', 'section', 'students'));
    }


    public function update(Request $request, Worksheet $worksheet)
    {
        $data = $request->except(['_token', '_method', 'file', 'existing_file_path']);

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
            } else {
                $data[$field] = null;
            }
        }

        if (isset($data['usertype']) && $data['usertype'] === 'INDIVIDUAL') {
            $data['gender'] = null;
            $data['section'] = null;
        } elseif (isset($data['usertype']) && $data['usertype'] === 'GROUP') {
            $data['students'] = null;
            if (empty($data['gender'])) {
                $data['gender'] = 'All';
            }
        }

        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;
        if ($data['is_schedule'] == 0) {
            $data['start_at'] = null;
        }

        // Retain remaining existing files
        $file_path = $worksheet->file_path ?? [];
        if (!is_array($file_path)) {
            $file_path = [$file_path];
        }
        if ($request->has('existing_file_path')) {
            $existing = $request->input('existing_file_path');
            if (is_array($existing)) {
                $file_path = array_values(array_filter($existing));
            } elseif (is_string($existing) && !empty($existing)) {
                $file_path = [$existing];
            }
        }

        // Upload and append new files
        if ($request->hasFile('file')) {
            $destinationPath = 'worksheet';
            foreach ($request->file('file') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time().'_'.$originalName;
                    $file->move($destinationPath, $fileName);
                    $file_path[] = 'worksheet/'.$fileName;
                }
            }
        }
        $data['file_path'] = !empty($file_path) ? array_values($file_path) : null;
        $worksheet->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Worksheet updated successfully.', 'data' => $worksheet], 200);
        }

        return redirect()->route('worksheet.index')->with('success', 'Worksheet updated successfully.');
    }


    public function destroy(Request $request, $id=null)
    {
        if($request->has('ids')) {
            $worksheets = Worksheet::whereIn('id', $request->ids)->get();
            foreach ($worksheets as $worksheet) {
                if(!empty($worksheet->file_path)){
                    foreach($worksheet->file_path as $file_path){
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }

                    }
                } 
                $worksheet->delete();
            }
        }
        return redirect()->back()->with('success', 'Worksheet deleted.');
    }

    public function worksheet()
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $worksheets = Worksheet::ForStudent($student);
        return view('student.worksheet', compact('worksheets'));
    }
}
