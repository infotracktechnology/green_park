<?php

namespace App\Http\Controllers;

use App\Models\StaffAnnouncement;
use App\Models\Staff;
use App\Models\Branch;
use Illuminate\Http\Request;

class StaffAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $department = Staff::whereNotNull('department')->where('department', '!=', '')->distinct()->pluck('department');

        $announcements = StaffAnnouncement::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch', 'like', '%' . auth()->user()->branch . '%'))
            ->when($request->department, fn($q) => $q->where('department', 'like', '%' . $request->department . '%'))
            ->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'announcements' => $announcements], 200);
        }

        return view('staffannouncement.index', compact('announcements', 'department'));
    }

    public function create()
    {
        $department = Staff::whereNotNull('department')->where('department', '!=', '')->distinct()->pluck('department');
        $branches = Branch::get();
        $staff = Staff::whereNotNull('username')->where('username', '!=', '')->orderBy('username')->get(['username','school_initial','branch_id', 'department'
        ]);
        return view('staffannouncement.create', compact('department', 'branches', 'staff'));
    }
    public function store(Request $request)
    {
        
        $data = $request->except(['_token','_method','existing_attachment','staff']);

        
        foreach (['branch', 'department'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
            } else {
                $data[$field] = null;
            }
        }
        $data['staff_ids'] = $request->input('staff', []);
        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;

        if ($data['is_schedule'] == 0) {
            $data['start_at'] = null;
        }

      
        $attachments = [];

        if ($request->hasFile('attachment')) {
            $destinationPath = 'assets/attachments';
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

        $announcement = StaffAnnouncement::create($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true,'message' => 'Staff Announcement created successfully.','data' => $announcement], 200);
        }

        return to_route('staffannouncement.index') ->with('success', 'Staff Announcement created successfully.');
    }

    public function edit(Request $request, $id)
    {
        $announcement = StaffAnnouncement::findOrFail($id);
        $department = Staff::whereNotNull('department')->where('department', '!=', '')->distinct()->pluck('department');

        $branches = Branch::get();
         $staff = Staff::whereNotNull('username')->where('username', '!=', '')->orderBy('username')->get(['username', 'school_initial', 'branch_id', 'department' ]);
        if ($request->wantsJson()) {
            return response()->json([ 'status' => true, 'announcement' => $announcement, 'department' => $department, 'branches' => $branches ?? [], ]);
        }

        return view('staffannouncement.edit', compact('announcement','department','branches','staff'));
    }

    public function update(Request $request, StaffAnnouncement $staffannouncement)
    {
        $data = $request->except(['_token','_method','existing_attachment','staff' ]);

        foreach (['branch', 'department'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field])
                    ? implode(',', $data[$field])
                    : $data[$field];
            } else {
                $data[$field] = null;
            }
        }

        $data['staff_ids'] = $request->input('staff', []);
        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;

        if ($data['is_schedule'] == 0) {
            $data['start_at'] = null;
        }

        $existingAttachments = $request->input('existing_attachment', []);

$attachments = $existingAttachments;

if ($request->hasFile('attachment')) {

    $destinationPath = 'assets/attachments';

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

$data['attachment'] = !empty($attachments)
    ? array_values($attachments)
    : null;

$staffannouncement->update($data);
// dd($staffannouncement);
if ($request->wantsJson()) {

    return response()->json([
        'status' => true,
        'message' => 'Staff Announcement updated successfully.',
        'data' => $staffannouncement
    ], 200);
}

return to_route('staffannouncement.index')->with('success', 'Staff Announcement updated successfully.');
    }
    public function destroy(Request $request, $id = null)
    {
        if ($request->has('ids')) {
            StaffAnnouncement::whereIn('id', $request->ids)->delete();
        }
        return redirect()->back()->with('success', 'Staff Announcement deleted successfully.');
    }


}