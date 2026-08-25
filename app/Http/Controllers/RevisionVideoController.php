<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RevisionVideo;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Http\Controllers\ImportController;
use Carbon\Carbon;

class RevisionVideoController extends Controller
{
    public function index(Request $request)
    {
        $revisionvideos = RevisionVideo::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->when($request->course, fn($q) => $q->where('course','like','%'.$request->course.'%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'revisionvideos' => $revisionvideos]);
        }

        return view('revisionvideo.index', compact('revisionvideos'));
    }

    public function show(Request $request, RevisionVideo $revisionvideo)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'revisionvideo' => $revisionvideo]);
        }
        return redirect()->route('revisionvideo.index');
    }

    public function create()
    {
        return view('revisionvideo.create');
    }

    public function store(Request $request)
    {
        $data = $request->except('file', '_token', '_method');

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

        if ($request->hasFile('file')) {
            $revisionvideos = [];
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
                
                $revisionvideos[] = array_merge($data, [
                    'subject' => $record['subject'],
                    'video_id' => $record['video_id'],
                    'period' => $record['period'] ?? '0',
                    'chapter' => $record['chapter'] ?? 'Unknown',
                    'day' => $record['day'] ?? '',
                    'date' => $this->parseDate($record['date'] ?? '')
                ]);
            }

            RevisionVideo::insert($revisionvideos);

            if ($request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Revision videos uploaded successfully.'], 200);
            }

            return redirect()->route('revisionvideo.index')->with('success', 'Class videos uploaded successfully.');
        }

        $revisionvideo = RevisionVideo::create($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Revision video created successfully.', 'data' => $revisionvideo], 200);
        }

        return redirect()->route('revisionvideo.index')->with('success', 'Revision video created successfully.');
    }

    public function destroy(Request $request, RevisionVideo $revisionvideo)
    {
        $revisionvideo->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Revision Video deleted successfully!']);
        }

        return redirect()->route('revisionvideo.index')->with('success', 'Revision Video deleted successfully!');
    }

    public function edit(Request $request, RevisionVideo $revisionvideo)
    {
        $type = Student::StudentFilterQuery($revisionvideo->branch, $revisionvideo->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($revisionvideo->branch, $revisionvideo->course, $revisionvideo->type, $revisionvideo->category, $revisionvideo->batch, $revisionvideo->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($revisionvideo->branch, $revisionvideo->course, $revisionvideo->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();
        
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'revisionvideo' => $revisionvideo, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('revisionvideo.edit', compact('revisionvideo', 'type', 'section', 'students'));
    }

    public function update(Request $request, RevisionVideo $revisionvideo) {
        $data = $request->except(['_token', '_method']);

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

        $revisionvideo->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Revision video updated successfully.', 'data' => $revisionvideo], 200);
        }

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
