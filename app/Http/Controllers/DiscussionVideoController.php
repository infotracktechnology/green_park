<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscussionVideo;
use App\Models\Branch;
use App\Models\AcademicYear;
use App\Models\Student;

class DiscussionVideoController extends Controller
{

    public function index(Request $request)
    {
        $discussionvideos = DiscussionVideo::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->when($request->course, fn($q) => $q->where('course','like','%'.$request->course.'%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'discussionvideos' => $discussionvideos]);
        }

        return view('discussionvideo.index', compact('discussionvideos'));
    }

    public function show(Request $request, DiscussionVideo $discussionvideo)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'discussionvideo' => $discussionvideo]);
        }
        return redirect()->route('discussionvideo.index');
    }

    public function create()
    {
        return view('discussionvideo.create');
    }

    public function store(Request $request)
    {
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

        $discussionvideo = DiscussionVideo::create($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Discussion video created successfully!', 'data' => $discussionvideo], 200);
        }

        return redirect()->route('discussionvideo.index')->with('success', 'Discussion video created successfully!');
    }

    public function edit(Request $request, DiscussionVideo $discussionvideo)
    {
        $type = Student::StudentFilterQuery($discussionvideo->branch, $discussionvideo->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($discussionvideo->branch, $discussionvideo->course, $discussionvideo->type, $discussionvideo->category, $discussionvideo->batch, $discussionvideo->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($discussionvideo->branch, $discussionvideo->course, $discussionvideo->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'discussionvideo' => $discussionvideo, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('discussionvideo.edit', compact('discussionvideo', 'type', 'section', 'students'));
    }

    public function update(Request $request, DiscussionVideo $discussionvideo)
    {
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

        $discussionvideo->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Discussion video updated successfully!', 'data' => $discussionvideo], 200);
        }

        return redirect()->route('discussionvideo.index')->with('success', 'Discussion video updated successfully!');
    }

    public function destroy(Request $request, DiscussionVideo $discussionvideo)
    {
        $discussionvideo->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Discussion video deleted successfully!']);
        }

        return redirect()->route('discussionvideo.index')->with('success', 'Discussion video deleted successfully!');
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids) {
            return response()->json(['message' => 'No videos selected'], 400);
        }
        DiscussionVideo::whereIn('id', explode(",", $ids))->delete();

        return response()->json(['message' => 'Selected discussion videos deleted successfully!'], 200);
    }

    public function discussionvideo(Request $request)
    {
        $subject = $request->subject ?? 0;
        $student = Student::where('student_id', auth()->user()->student_id)->first();
        $discussionvideos = DiscussionVideo::ForStudent($student);
        $discussionvideos = $discussionvideos->groupBy('date');

        return view('student.discussionvideo', compact('discussionvideos', 'subject'));
    }
}
