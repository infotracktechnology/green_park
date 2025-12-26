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
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->latest()->get();

        return view('download.index', compact('downloads'));
    }

    public function create()
    {
        $academicyear = AcademicYear::all();

        return view('download.create');
    }



    public function store(Request $request)
    {
        $data = $request->except('file');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('file')) {
            $originalName = $request->file('file')->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            $request->file('file')->move('download', $fileName);
            $data['file_path'] = 'download/' . $fileName;
        }

        Download::create($data);

        return redirect()->route('download.index')->with('success', 'Answer Key added successfully!');
    }



    public function edit(Download $download)
    {
        $type = Student::StudentFilterQuery($download->branch, $download->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($download->branch, $download->course, $download->type, $download->category, $download->batch, $download->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($download->branch, $download->course, $download->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        return view('download.edit', compact('download', 'type', 'section', 'students'));
    }

    public function update(Request $request, Download $download)
    {
        $data = $request->except('file');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('file')) {
            $originalName = $request->file('file')->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            $request->file('file')->move('download', $fileName);
            $data['file_path'] = 'download/' . $fileName;
        }
        $download->update($data);

        return redirect()->route('download.index')->with('success', 'Answer Key updated successfully!');
    }


    public function destroy(Request $request,$id=null)
    {
        if($request->has('ids')) {
            $downloads = Download::whereIn('id', $request->ids)->get();
            foreach ($downloads as $download) {
                if (file_exists($download->file_path)) {
                    unlink($download->file_path);
                }
                $download->delete();
            }
        }
        
        return redirect()->back()->with('success', 'Answer Key deleted successfully!');
    }


    public function download()
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $downloads = Download::ForStudent($student);
        return view('student.download', compact('downloads'));
    }
}
