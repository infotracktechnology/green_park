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
            ->latest()->get();

        return view('discussionvideo.index', compact('discussionvideos'));
    }



    public function create()
    {
        return view('discussionvideo.create');
    }


    public function store(Request $request)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        DiscussionVideo::create($data);

        return redirect()->route('discussionvideo.index')->with('success', 'Discussion video created successfully!');
    }

    public function edit(DiscussionVideo $discussionvideo)
    {
        $type = Student::StudentFilterQuery($discussionvideo->branch, $discussionvideo->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($discussionvideo->branch, $discussionvideo->course, $discussionvideo->type, $discussionvideo->category, $discussionvideo->batch, $discussionvideo->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($discussionvideo->branch, $discussionvideo->course, $discussionvideo->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        return view('discussionvideo.edit', compact('discussionvideo', 'type', 'section', 'students'));
    }

    public function update(Request $request, DiscussionVideo $discussionvideo)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $discussionvideo->update($data);

        return redirect()->route('discussionvideo.index')->with('success', 'Discussion video updated successfully!');
    }


    public function destroy(DiscussionVideo $discussionvideo)
    {
        $discussionvideo->delete();

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
