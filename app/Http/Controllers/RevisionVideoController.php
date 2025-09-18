<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RevisionVideo;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Http\Controllers\ImportController;


class RevisionVideoController extends Controller
{
    public function index()
    {
        $academic_years = AcademicYear::all();

        $revisionvideos = RevisionVideo::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, function ($query) {
                $query->where('branch', 'like', '%' . auth()->user()->branch . '%');
            })->get();

        return view('revisionvideo.index', compact('revisionvideos'));
    }

    public function create()
    {
        return view('revisionvideo.create');
    }

    public function store(Request $request)
    {
        $data = $request->except('file', '_token');
        $revisionvideos = [];

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }
        $file = $request->file('file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));

        if (empty($csvData)) {
            return back()->with('error', 'CSV file is empty.');
        }

        $header = array_map('trim', array_shift($csvData));

        foreach ($csvData as $row) {
            $row = array_map('trim', $row);
            $record = array_combine($header, $row);
            if (empty($record['subject']) || empty($record['video_id'])) {
                return back()->with('error', 'Subject and Video ID fields are required.');
            }
            $revisionvideos[] = array_merge($data, ['subject' => $record['subject'], 'video_id' => $record['video_id'], 'period' => $record['period'] ?? '0', 'chapter' => $record['chapter'] ?? 'Unknown']);
        }

        RevisionVideo::insert($revisionvideos);
        return redirect()->route('revisionvideo.index')->with('success', 'Class videos uploaded successfully.');
    }

    public function destroy(RevisionVideo $revisionvideo)
    {
        $revisionvideo->delete();
        return redirect()->route('revisionvideo.index')->with('success', 'Revision Video deleted successfully!');
    }



    public function edit(RevisionVideo $revisionvideo)
    {
        $type = Student::StudentFilterQuery($revisionvideo->branch, $revisionvideo->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($revisionvideo->branch, $revisionvideo->course, $revisionvideo->type, $revisionvideo->category, $revisionvideo->batch, $revisionvideo->gender)->select('section')->distinct()->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($revisionvideo->branch, $revisionvideo->course, $revisionvideo->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();
        
        return view('revisionvideo.edit', compact('revisionvideo', 'type', 'section', 'students'));
    }

    public function update(Request $request, RevisionVideo $revisionvideo) {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $revisionvideo->update($data);
        return redirect()->route('revisionvideo.index')->with('success', 'Video updated successfully.');
    }


    public function revisionvideo(Request $request)
    {
        $student = Student::where('student_id', auth()->user()->student_id)->first();
        $revisionvideos = RevisionVideo::ForStudent($student);
        return view('student.revisionvideo', compact('revisionvideos'));
    }


    public function bulkDelete(Request $request)
    {
        $ids = explode(",", $request->ids);

        if (empty($ids)) {
            return response()->json(['message' => 'No videos selected'], 400);
        }

        RevisionVideo::whereIn('id', $ids)->delete();

        return response()->json(['message' => RevisionVideo::whereIn('id', $ids)], 200);
    }
}
