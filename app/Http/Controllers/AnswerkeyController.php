<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnswerKey;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class AnswerkeyController extends Controller
{
    public function index(Request $request)
    {
        $answerkeys = AnswerKey::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch', 'like', '%' . auth()->user()->branch . '%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type', 'like', '%' . $request->coaching_type . '%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'answerkeys' => $answerkeys], 200);
        }

        return view('answerkey.index', compact('answerkeys'));
    }

    public function create()
    {
        return view('answerkey.create');
    }

    public function show(Request $request, AnswerKey $answerkey)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'answerkey' => $answerkey]);
        }

        return redirect()->route('answerkey.index');
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
            $destinationPath = 'answerkey';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('file') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '-' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $file_path[] = 'answerkey/' . $fileName;
                }
            }
        }
        $data['file_path'] = !empty($file_path) ? array_values($file_path) : null;
        $answerkey = AnswerKey::create($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Answer key created successfully.', 'data' => $answerkey], 200);
        }

        return redirect()->route('answerkey.index')->with('success', 'Answer Key added successfully!');
    }

    public function edit(Request $request, AnswerKey $answerkey)
    {
        $type = Student::StudentFilterQuery($answerkey->branch, $answerkey->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($answerkey->branch, $answerkey->course, $answerkey->type, $answerkey->category, $answerkey->batch, $answerkey->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($answerkey->branch, $answerkey->course, $answerkey->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'answerkey' => $answerkey, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('answerkey.edit', compact('answerkey', 'type', 'section', 'students'));
    }

    public function update(Request $request, AnswerKey $answerkey)
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

        // Retain remaining existing files
        $file_path = [];
        if ($request->has('existing_file')) {
            $existing = $request->input('existing_file');
            if (is_array($existing)) {
                $file_path = array_values(array_filter($existing));
            } elseif (is_string($existing) && !empty($existing)) {
                $file_path = [$existing];
            }
        }

        // Upload and append new files
        if ($request->hasFile('file')) {
            $destinationPath = 'answerkey';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('file') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '-' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $file_path[] = 'answerkey/' . $fileName;
                }
            }
        }
        $data['file_path'] = !empty($file_path) ? array_values($file_path) : null;
        $answerkey->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Answer key updated successfully.', 'data' => $answerkey], 200);
        }

        return redirect()->route('answerkey.index')->with('success', 'Answer Key updated successfully!');
    }

    public function destroy(Request $request, $id = null)
    {
        if ($request->has('ids')) {
            $answerkeys = AnswerKey::whereIn('id', $request->ids)->get();
            foreach ($answerkeys as $answerkey) {
                if (!empty($answerkey->file_path)) {
                    foreach ($answerkey->file_path as $file_path) {
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        } elseif (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
                $answerkey->delete();
            }
        }
        return redirect()->back()->with('success', 'Answer Key deleted successfully!');
    }

    public function answerkey()
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $answerkeys = AnswerKey::ForStudent($student);
        return view('student.answerkey', compact('answerkeys'));
    }
}
