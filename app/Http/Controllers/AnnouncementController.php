<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Student;
use App\Providers\FcmServiceProvider;
use App\Http\Controllers\HomeController;
use App\Jobs\SendAnnouncementNotification;

class AnnouncementController extends Controller
{

    public function index(Request $request)
    {
        $announcements = Announcement::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->latest()->get();

        return view('announcement.index', compact('announcements'));
    }

    public function create()
    {

        return view('announcement.create');
    }

    public function store(Request $request, FcmServiceProvider $fcm)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'batch', 'category'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;

        $data['student_ids'] = [];
        $attachments = [];
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-'. $originalName;
                    $file->move('assets/attachments', $fileName);
                    $attachments[] = 'assets/attachments/' . $fileName;
                }
            }
        $data['attachment'] = $attachments ?: null;
        $announcement = Announcement::create($data);

         $students = $announcement->StudentList()->map(function ($student) { return $student->device_token;})->filter()->unique()->toArray();
         
        if (count($students) > 0) {
            $fcm->sendMulticast($students,"There is an announcement from GPCC",$announcement->title,env('APP_LOGO'));
        }

        return to_route('announcement.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(Request $request, Announcement $announcement)
    {
        $type = Student::StudentFilterQuery($announcement->branch, $announcement->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($announcement->branch, $announcement->course, $announcement->type, $announcement->category, $announcement->batch, $announcement->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($announcement->branch, $announcement->course, $announcement->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();


        return view('announcement.edit', compact('announcement', 'type', 'section', 'students'));
    }


    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'batch', 'category'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;

        $data['student_ids'] = [];
        $attachments = [];
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . $originalName;
                    $file->move('assets/attachments', $fileName);
                    $attachments[] = 'assets/attachments/' . $fileName;
                }
            }
            $data['attachment'] = $attachments ?: null;
        $announcement->update($data);
        return redirect()->route('announcement.index')->with('success', 'Announcement details successfully updated.');
    }


    public function destroy(Request $request, $id = null)
    {
        if ($request->has('ids')) {
            Announcement::whereIn('id', $request->ids)->delete();
        }
        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }


    public function notification(Request $request)
    {
        $student = Student::where('student_id', auth()->user()->student_id)->first();
        $announcements = Announcement::ForStudent($student)->latest()->get();
        return view('student.notification', compact('announcements'));
    }
}
