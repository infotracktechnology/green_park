<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\QuestionKey;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class QuestionKeyController extends Controller
{
    public function index(Request $request)
    {
        $questionkeys = QuestionKey::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch', 'like', '%' . auth()->user()->branch . '%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type', 'like', '%' . $request->coaching_type . '%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'questionkeys' => $questionkeys], 200);
        }

        return view('questionkey.index', compact('questionkeys'));
    }

    public function create()
    {
        return view('questionkey.create');
    }

    public function show(Request $request, QuestionKey $questionkey)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'questionkey' => $questionkey]);
        }

        return redirect()->route('questionkey.index');
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

        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;
        if ($data['is_schedule'] == 0) {
            $data['start_at'] = null;
        }

        $file_path = [];
        if ($request->hasFile('file')) {
            $destinationPath = public_path('questionkey');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('file') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '-' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $file_path[] = 'questionkey/' . $fileName;
                }
            }
        }
        $data['file_path'] = !empty($file_path) ? array_values($file_path) : null;
        $questionkey = QuestionKey::create($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Question key created successfully.', 'data' => $questionkey], 200);
        }

        return redirect()->route('questionkey.index')->with('success', 'Question Key added successfully!');
    }

    public function edit(Request $request, QuestionKey $questionkey)
    {
        $type = Student::StudentFilterQuery($questionkey->branch, $questionkey->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($questionkey->branch, $questionkey->course, $questionkey->type, $questionkey->category, $questionkey->batch, $questionkey->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($questionkey->branch, $questionkey->course, $questionkey->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'questionkey' => $questionkey, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('questionkey.edit', compact('questionkey', 'type', 'section', 'students'));
    }

    public function update(Request $request, QuestionKey $questionkey)
    {
        $data = $request->except(['_token', '_method', 'file']);

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
            $destinationPath = public_path('questionkey');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('file') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '-' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $file_path[] = 'questionkey/' . $fileName;
                }
            }
        }
        $data['file_path'] = !empty($file_path) ? array_values($file_path) : null;
        $questionkey->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Question key updated successfully.', 'data' => $questionkey], 200);
        }

        return redirect()->route('questionkey.index')->with('success', 'Question Key updated successfully!');
    }

    public function destroy(Request $request, $id = null)
    {
        if ($request->has('ids')) {
            $questionkeys = QuestionKey::whereIn('id', $request->ids)->get();
            foreach ($questionkeys as $questionkey) {
                if (!empty($questionkey->file_path)) {
                    foreach ($questionkey->file_path as $file_path) {
                        if (file_exists(public_path($file_path))) {
                            unlink(public_path($file_path));
                        } elseif (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
                $questionkey->delete();
            }
        }

        return redirect()->back()->with('success', 'Question Key deleted successfully!');
    }

    public function questionkey()
    {
        $students = Student::where('student_id', auth('student')->user()->student_id)->first();
        $questionkeys = QuestionKey::ForStudent($students);
        return view('student.questionkey', compact('questionkeys'));
    }
}
