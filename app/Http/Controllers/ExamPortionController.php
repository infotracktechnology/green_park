<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use App\Models\Examportion;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

use Illuminate\Support\Facades\DB;

class ExamPortionController extends Controller
{
    public function index(Request $request)
    {
        $examportions = Examportion::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'examportions' => $examportions], 200);
        }

        return view('examportion.index', compact('examportions'));
    }
    public function create()
    {

        return view('examportion.create');
    }

    public function show(Request $request, Examportion $examportion)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'examportion' => $examportion]);
        }

        return redirect()->route('examportion.index');
    }
    public function store(Request $request)
    {
        $data = $request->except(['_token', '_method', 'attachment', 'existing_attachment']);

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
            } else {
                $data[$field] = null;
            }
        }

        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;
        if ($data['is_schedule'] == 0) {
            $data['start_at'] = null;
        }

        $attachments = [];
        if ($request->hasFile('attachment')) {
            $destinationPath = public_path('assets/examportion');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('attachment') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '-' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $attachments[] = 'assets/examportion/' . $fileName;
                }
            }
        }
        $data['attachment'] = !empty($attachments) ? array_values($attachments) : null;
        $examportion = Examportion::create($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Exam portion created successfully.', 'data' => $examportion], 200);
        }

        return to_route('examportion.index')->with('success', 'Examportion created successfully');
    }

    public function edit(Examportion $examportion)
    {
        $type = Student::StudentFilterQuery($examportion->branch, $examportion->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($examportion->branch, $examportion->course, $examportion->type, $examportion->category, $examportion->batch, $examportion->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($examportion->branch, $examportion->course, $examportion->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'examportion' => $examportion, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('examportion.edit', compact('examportion', 'type', 'section', 'students'));
    }

    public function update(Request $request, Examportion $examportion)
    {
        $data = $request->except(['_token', '_method', 'attachment', 'existing_attachment']);

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
            } else {
                $data[$field] = null;
            }
        }

        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;
        if ($data['is_schedule'] == 0) {
            $data['start_at'] = null;
        }

        // Retain remaining existing attachments
        $attachments = [];
        if ($request->has('existing_attachment')) {
            $existing = $request->input('existing_attachment');
            if (is_array($existing)) {
                $attachments = array_values(array_filter($existing));
            } elseif (is_string($existing) && !empty($existing)) {
                $attachments = [$existing];
            }
        }

        // Upload and append new files
        if ($request->hasFile('attachment')) {
            $destinationPath = public_path('assets/examportion');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('attachment') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '-' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $attachments[] = 'assets/examportion/' . $fileName;
                }
            }
        }
        $data['attachment'] = !empty($attachments) ? array_values($attachments) : null;
        $examportion->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Exam portion updated successfully.', 'data' => $examportion], 200);
        }

        return redirect()->route('examportion.index')->with('success', 'Examportion updated successfully.');
    }


    public function destroy(Request $request, $id=null)
    {
       if($request->has('ids')) {
        $examportions = Examportion::whereIn('id', $request->ids)->get();
        foreach ($examportions as $examportion) {
            if(!empty($examportion->attachment)){
                foreach ($examportion->attachment as $attachment) {
                    if (file_exists($attachment)) {
                        unlink($attachment);
                    }
                }
            }
            $examportion->delete();
        }
       }

        return redirect()->back()->with('success', 'Examportion deleted successfully.');
    }

    public function examportion(Request $request)
    {
        $student = Student::where('student_id', auth()->user()->student_id)->first();
        $examportions = Examportion::ForStudent($student);
        return view('student.examportion', compact('examportions'));
    }
}
