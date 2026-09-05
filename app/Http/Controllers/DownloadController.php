<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Download;
use App\Models\Student;
use App\Models\AcademicYear;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $downloads = Download::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch', 'like', '%' . auth()->user()->branch . '%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type', 'like', '%' . $request->coaching_type . '%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'downloads' => $downloads], 200);
        }

        return view('download.index', compact('downloads'));
    }

    public function create()
    {
        $academicyear = AcademicYear::all();
        return view('download.create');
    }

    public function show(Request $request, Download $download)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'download' => $download]);
        }

        return redirect()->route('download.index');
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
            $destinationPath = 'download';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('file') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $file_path[] = 'download/'.$fileName;
                }
            }
        }
        $data['file_path'] = !empty($file_path) ? array_values($file_path) : null;
        $download = Download::create($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Download created successfully.', 'data' => $download], 200);
        }

        return redirect()->route('download.index')->with('success', 'Download added successfully!');
    }

    public function edit(Request $request, Download $download)
    {
        $type = Student::StudentFilterQuery($download->branch, $download->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($download->branch, $download->course, $download->type, $download->category, $download->batch, $download->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($download->branch, $download->course, $download->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'download' => $download, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('download.edit', compact('download', 'type', 'section', 'students'));
    }

    public function update(Request $request, Download $download)
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
        $file_path =$download->file_path ?? [];
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
            $destinationPath = 'download';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('file') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $file_path[] = 'download/'.$fileName;
                }
            }
        }
        $data['file_path'] = !empty($file_path) ? array_values($file_path) : null;
        $download->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Download details updated successfully.', 'data' => $download], 200);
        }

        return redirect()->route('download.index')->with('success', 'Download updated successfully!');
    }

    public function destroy(Request $request, $id = null)
    {
        if ($request->has('ids')) {
            $downloads = Download::whereIn('id', $request->ids)->get();
            foreach ($downloads as $download) {
                if (!empty($download->file_path)) {
                    foreach ($download->file_path as $file_path) {
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        } elseif (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
                $download->delete();
            }
        }

        return redirect()->back()->with('success', 'Download deleted successfully!');
    }

    public function download()
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $downloads = Download::ForStudent($student);
        return view('student.download', compact('downloads'));
    }
}
