<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Chairmanvideo;
use App\Providers\FcmServiceProvider;
use Illuminate\Support\Facades\DB;

class ChairmanVideoController extends Controller
{
    public function index(Request $request)
    {

        $chairmanvideos = Chairmanvideo::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->latest()->get();

        return view('chairmanvideo.index', compact('chairmanvideos'));
    }

    public function create()
    {

        return view('chairmanvideo.create');
    }
    public function store(Request $request,FcmServiceProvider $fcm)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $data['start_at'] = $request->filled('start_at') ? $request->start_at : null;
        $data['end_at'] = $request->filled('end_at') ? $request->end_at : null;

        if ($request->hasFile('attachment')) {
            $fileName = time() . '.' . $request->attachment->extension();
            $request->attachment->move(public_path('chairman/video'), $fileName);
            $data['attachment'] = 'chairman/video/'.$fileName;
        }

        $chairmanvideo = Chairmanvideo::create($data);

         try {
            $tokens = $chairmanvideo->StudentList()->map(fn($student) => $student->device_token)->filter()->unique()->values()->toArray();

            if (!empty($tokens)) {
                foreach (array_chunk($tokens, 500) as $chunk) {
                    $fcm->sendMulticast($chunk, "There is an Chairman video from GPCC", $chairmanvideo->title, env('APP_LOGO'), [], "Chairman Video");
                }
            }
        } catch (\Throwable $e) {
            Log::error('FCM Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return redirect()->route('chairmanvideo.index')->with('success', 'Chairman video created successfully.');
    }

    public function edit(Request $request, Chairmanvideo $chairmanvideo)
    {
        $type = Student::StudentFilterQuery($chairmanvideo->branch, $chairmanvideo->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($chairmanvideo->branch, $chairmanvideo->course, $chairmanvideo->type, $chairmanvideo->category, $chairmanvideo->batch, $chairmanvideo->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($chairmanvideo->branch, $chairmanvideo->course, $chairmanvideo->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        return view('chairmanvideo.edit', compact('chairmanvideo', 'type', 'section', 'students'));
    }
    public function update(Request $request, Chairmanvideo $chairmanvideo)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $data['start_at'] = $request->filled('start_at') ? $request->start_at : null;
        $data['end_at'] = $request->filled('end_at') ? $request->end_at : null;

        if ($request->hasFile('attachment')) {
            if ($chairmanvideo->attachment && file_exists(public_path($chairmanvideo->attachment))) {
                unlink(public_path($chairmanvideo->attachment));
            }
            $fileName = time() . '.' . $request->attachment->extension();
            $request->attachment->move(public_path('chairman/video'), $fileName);
            $chairmanvideo->attachment = 'chairman/video/' . $fileName;
        }

        $chairmanvideo->update($data);

        return redirect()->route('chairmanvideo.index')->with('success', 'Video updated successfully!');
    }
    public function destroy(Request $request, $id = null)
    {
        if ($request->has('ids')) {
            Chairmanvideo::whereIn('id', $request->ids)->delete();
        }
        return redirect()->back()->with('success', 'Chairman video deleted successfully.');
    }
    public function chairmanvideo(Request $request)
    {
        $student = Student::where('student_id', auth()->user()->student_id)->first();
        $chairmanvideos = Chairmanvideo::ForStudent($student)->latest()->get();
        $chairmanvideos = $chairmanvideos->groupBy(function($video) {
            return $video->created_at ? $video->created_at->format('d-m-Y') : 'N/A';
        });
        return view('student.chairmanvideo', compact('chairmanvideos'));
    }

    public function video(Request $request, $id)
    {
        $id = base64_decode($id);
        return view('layouts.video', compact('id'));
    }
}
