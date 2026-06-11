<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassVideo;
use App\Models\Student;
use App\Models\RevisionVideo;
use App\Models\AcademicYear;
use Carbon\Carbon;

class ClassVideoController extends Controller
{
    public function index(Request $request)
    {
        $classvideos = ClassVideo::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->latest()->get();

        return view('classvideo.index', compact('classvideos'));
    }


    public function create()
    {
        return view('classvideo.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        ClassVideo::create($data);
        return redirect()->route('classvideo.index')->with('success', 'Video created successfully.');
    }

    public function destroy(ClassVideo $classvideo)
    {
        $classvideo->delete();
        return redirect()->route('classvideo.index')->with('success', 'Video deleted successfully!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids) {
            return response()->json(['success' => false, 'message' => 'No videos selected.'], 400);
        }
        ClassVideo::whereIn('id', $ids)->delete();
        return response()->json(['success' => true, 'message' => 'Videos deleted successfully.']);
    }


    public function edit(ClassVideo $classvideo)
    {
        $type = Student::StudentFilterQuery($classvideo->branch, $classvideo->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($classvideo->branch, $classvideo->course, $classvideo->type, $classvideo->category, $classvideo->batch, $classvideo->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($classvideo->branch, $classvideo->course, $classvideo->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        return view('classvideo.edit', compact('classvideo', 'type', 'section', 'students'));
    }

    public function update(Request $request, ClassVideo $classvideo)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $classvideo->update($data);
        return redirect()->route('classvideo.index')->with('success', 'Video updated successfully.');
    }


    public function classvideo(Request $request)
    {
        $student = Student::where('student_id', auth()->user()->student_id)->first();
        $classvideos = ClassVideo::ForStudent($student);
        $classvideos = $classvideos->groupBy('date');
        return view('student.classvideo', compact('classvideos'));
    }

    public function showUploadForm()
    {
        return view('classvideo.upload');
    }
    public function upload(Request $request)
    {
        $data = $request->except('file', '_token');
        $classVideos = [];

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
           
            $classVideos[] = array_merge($data, [
                'subject' => $record['subject'],
                'video_id' => $record['video_id'],
                'period' => $record['period'] ?? '0',
                'chapter' => $record['chapter'] ?? '',
                'day' => $record['day'] ?? '',
                'part' => $record['part'] ?? '',
                'date' => $this->parseDate($record['date'] ?? '')
            ]);
        }

        ClassVideo::insert($classVideos);
        return redirect()->route('classvideo.index')->with('success', 'Class videos uploaded successfully.');
    }

    public function schedule(Request $request)
    {
        $ClassVideo = ClassVideo::whereIn('id', $request->ids)->update(['start_at' => $request->start_at, 'end_at' => $request->end_at]);
        if ($ClassVideo) {
            return response()->json(['status' => true, 'message' => 'Class video scheduled successfully.']);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to schedule class video.']);
        }
    }

    private function parseDate($dateStr)
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
}
