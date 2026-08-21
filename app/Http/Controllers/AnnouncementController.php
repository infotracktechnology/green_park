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
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{

    public function index(Request $request)
    {
        $announcements = Announcement::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch', 'like', '%' . auth()->user()->branch . '%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type', 'like', '%' . $request->coaching_type . '%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'announcements' => $announcements], 200);
        }

        return view('announcement.index', compact('announcements'));
    }

    public function create()
    {

        return view('announcement.create');
    }

    public function show(Request $request, Announcement $announcement)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'announcement' => $announcement]);
        }

        return redirect()->route('announcement.index');
    }

    public function store(Request $request, FcmServiceProvider $fcm)
    {
        $data = $request->except(['_token', '_method', 'existing_attachment']);

        foreach (['coaching_type', 'branch', 'batch', 'category'] as $field) {
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

        $data['student_ids'] = [];
        $attachments = [];

        if ($request->hasFile('attachment')) {
            $destinationPath = public_path('assets/attachments');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('attachment') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '-' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $attachments[] = 'assets/attachments/' . $fileName;
                }
            }
        }

        $data['attachment'] = !empty($attachments) ? array_values($attachments) : null;
        $announcement = Announcement::create($data);

        try {
            $tokens = $announcement->StudentList()->map(fn($student) => $student->device_token)->filter()->unique()->values()->toArray();

            if (!empty($tokens)) {
                foreach (array_chunk($tokens, 500) as $chunk) {
                    $fcm->sendMulticast($chunk, "There is an announcement from GPCC", $announcement->title, env('APP_LOGO'), [], "Announcements");
                }
            }
        } catch (\Throwable $e) {
            Log::error('FCM Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Announcement created successfully.', 'data' => $announcement], 200);
        }

        return to_route('announcement.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(Request $request, Announcement $announcement)
    {
        $type = Student::StudentFilterQuery($announcement->branch, $announcement->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($announcement->branch, $announcement->course, $announcement->type, $announcement->category, $announcement->batch, $announcement->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($announcement->branch, $announcement->course, $announcement->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'announcement' => $announcement, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('announcement.edit', compact('announcement', 'type', 'section', 'students'));
    }


    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->except(['_token', '_method', 'existing_attachment']);

        foreach (['coaching_type', 'branch', 'batch', 'category'] as $field) {
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

        $data['student_ids'] = $announcement->student_ids ?? [];

        // Retain remaining existing attachments
        $attachments = [];
        if ($request->has('existing_attachment')) {
            $existing = $request->input('existing_attachment');
            if (is_array($existing)) {
                $attachments = array_values(array_filter($existing));
            } elseif (is_string($existing) && !empty($existing)) {
                $attachments = [$existing];
            }
        }

        // Upload and append new files
        if ($request->hasFile('attachment')) {
            $destinationPath = public_path('assets/attachments');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            foreach ($request->file('attachment') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '-' . $originalName;
                    $file->move($destinationPath, $fileName);
                    $attachments[] = 'assets/attachments/' . $fileName;
                }
            }
        }

        $data['attachment'] = !empty($attachments) ? array_values($attachments) : null;
        $announcement->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Announcement details updated successfully.', 'data' => $announcement], 200);
        }

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
