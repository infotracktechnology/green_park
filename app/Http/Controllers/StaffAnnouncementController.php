<?php

namespace App\Http\Controllers;

use App\Models\StaffAnnouncement;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\AcademicYear;
use App\Providers\FcmServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $department = Staff::whereNotNull('department')->where('department', '!=', '')->distinct()->pluck('department');

        $announcements = StaffAnnouncement::where('academic_year', $this->activeAcademicYear())
            ->when($this->branchFilter(), function ($q, $branch) {
                $q->where('branch', 'like', '%' . $branch . '%');
            })
            ->when($request->department, fn($q) => $q->where('department', 'like', '%' . $request->department . '%'))
            ->latest()->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['status' => true, 'announcements' => $this->serializeAnnouncements($announcements), 'departments' => $department], 200);
        }

        return view('staffannouncement.index', compact('announcements', 'department'));
    }

    /**
     * Master data (departments, branches & staff) used by the create/edit forms.
     */
    public function masterdata(Request $request)
    {
        $departments = Staff::whereNotNull('department')->where('department', '!=', '')
            ->distinct()->orderBy('department')->pluck('department')->values();

        $staff = Staff::whereNotNull('username')->where('username', '!=', '')
            ->orderBy('username')
            ->get(['username', 'school_initial', 'branch_id', 'department'])
            ->map(function ($row) {
                return [
                    'username' => $row->username,
                    'school_initial' => $row->school_initial,
                    'branch_id' => $row->branch_id,
                    'department' => $row->department,
                    'display_name' => trim($row->username . ' ' . ($row->school_initial ?? '')),
                ];
            })->values();

        return response()->json([
            'status' => true,
            'academic_year' => $this->activeAcademicYear(),
            'departments' => $departments,
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'staff' => $staff,
        ], 200);
    }

    /**
     * Announcements targeted to the authenticated staff member.
     */
    public function staffAnnouncements(Request $request)
    {
        $staff = $this->resolveStaff($request);

        if (!$staff) {
            return response()->json([
                'status' => false,
                'message' => 'Staff profile not identified. Please login again.',
                'announcements' => [],
            ], 404);
        }

        $announcements = StaffAnnouncement::ForStaff($staff)
            ->when($this->activeAcademicYear(), fn($q, $year) => $q->where('academic_year', $year))
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'announcements' => $this->serializeAnnouncements($announcements),
        ], 200);
    }

    public function create()
    {
        $department = Staff::whereNotNull('department')->where('department', '!=', '')->distinct()->pluck('department');
        $branches = Branch::get();
        $staff = Staff::whereNotNull('username')->where('username', '!=', '')->orderBy('username')->get([
            'username',
            'school_initial',
            'branch_id',
            'department'
        ]);
        return view('staffannouncement.create', compact('department', 'branches', 'staff'));
    }
    public function store(Request $request)
    {

        $data = $request->except(['_token', '_method', 'existing_attachment', 'staff']);


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

        if (($data['usertype'] ?? 'GROUP') === 'INDIVIDUAL') {
            $data['staff_ids'] = array_values(array_filter((array) $data['staff_ids']));
        } else {
            $data['staff_ids'] = [];
            if (empty($data['gender'])) {
                $data['gender'] = 'All';
            }
        }

        if (empty($data['academic_year']) || trim((string) $data['academic_year']) === '') {
            $data['academic_year'] = $this->activeAcademicYear();
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

        // Fire FCM push notification to the filtered (targeted) staff
        try {
            $announcement->NotifyStaffs(app(FcmServiceProvider::class));
        } catch (\Throwable $e) {
            Log::error('Staff announcement notification error: ' . $e->getMessage(), [
                'staffannouncement_id' => $announcement->id,
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['status' => true, 'message' => 'Staff Announcement created successfully.', 'data' => $this->serializeAnnouncements([$announcement])->first()], 200);
        }

        return to_route('staffannouncement.index')->with('success', 'Staff Announcement created successfully.');
    }

    public function show(Request $request, $id)
    {
        $announcement = StaffAnnouncement::find($id);

        if (!$announcement) {
            return response()->json(['status' => false, 'message' => 'Staff Announcement not found.'], 404);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['status' => true, 'announcement' => $this->serializeAnnouncements([$announcement])->first()], 200);
        }

        return redirect()->route('staffannouncement.index');
    }

    public function edit(Request $request, $id)
    {
        $announcement = StaffAnnouncement::findOrFail($id);
        $department = Staff::whereNotNull('department')->where('department', '!=', '')->distinct()->pluck('department');

        $branches = Branch::get();
        $staff = Staff::whereNotNull('username')->where('username', '!=', '')->orderBy('username')->get(['username', 'school_initial', 'branch_id', 'department']);
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => true,
                'announcement' => $this->serializeAnnouncements([$announcement])->first(),
                'department' => $department,
                'branches' => $branches ?? [],
                'staff' => $staff,
                'academic_year' => $announcement->academic_year ?: $this->activeAcademicYear(),
            ]);
        }

        return view('staffannouncement.edit', compact('announcement', 'department', 'branches', 'staff'));
    }

    public function update(Request $request, StaffAnnouncement $staffannouncement)
    {
        $data = $request->except(['_token', '_method', 'existing_attachment', 'staff']);

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

        if (($data['usertype'] ?? $staffannouncement->usertype ?? 'GROUP') === 'INDIVIDUAL') {
            $data['staff_ids'] = array_values(array_filter((array) $data['staff_ids']));
        } else {
            $data['staff_ids'] = [];
            if (empty($data['gender'])) {
                $data['gender'] = 'All';
            }
        }

        if (empty($data['academic_year']) || trim((string) $data['academic_year']) === '') {
            $data['academic_year'] = $staffannouncement->academic_year ?: $this->activeAcademicYear();
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
        if ($request->wantsJson() || $request->is('api/*')) {

            return response()->json([
                'status' => true,
                'message' => 'Staff Announcement updated successfully.',
                'data' => $this->serializeAnnouncements([$staffannouncement->fresh()])->first()
            ], 200);
        }

        return to_route('staffannouncement.index')->with('success', 'Staff Announcement updated successfully.');
    }
    public function destroy(Request $request, $id = null)
    {
        $ids = $request->input('ids');

        if (empty($ids) && $id) {
            $ids = [$id];
        }

        if (!empty($ids)) {
            StaffAnnouncement::whereIn('id', (array) $ids)->delete();
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['status' => true, 'message' => 'Staff Announcement deleted successfully.'], 200);
        }

        return redirect()->back()->with('success', 'Staff Announcement deleted successfully.');
    }

    /**
     * Academic year used to scope the announcements (falls back to the active year).
     */
    private function activeAcademicYear()
    {
        return $this->academic_year ?? optional(AcademicYear::where('active', 1)->first())->academic_year;
    }

    /**
     * Branch used to scope the admin listing (handles both admin & staff users).
     */
    private function branchFilter()
    {
        $user = auth()->user();

        if ($user instanceof Staff) {
            return $user->branch_id;
        }

        return $user?->branch;
    }

    /**
     * Resolve the Staff record of the authenticated api user.
     */
    private function resolveStaff(Request $request)
    {
        $user = auth()->user();

        if ($user instanceof Staff) {
            return $user;
        }

        if ($request->filled('staff_id')) {
            return Staff::find($request->staff_id);
        }

        if ($user && !empty($user->username)) {
            return Staff::where('username', $user->username)
                ->orWhere('biometric_no', $user->username)
                ->first();
        }

        return null;
    }

    /**
     * Shape announcements for the mobile app / JSON responses.
     */
    private function serializeAnnouncements($announcements)
    {
        return collect($announcements)->map(function ($announcement) {
            return [
                'id' => $announcement->id,
                'academic_year' => $announcement->academic_year,
                'usertype' => $announcement->usertype,
                'branch' => $announcement->branch,
                'branch_names' => $announcement->branch ? $announcement->branchNames() : 'All',
                'department' => $announcement->department,
                'department_names' => $announcement->department ? str_replace(',', ', ', $announcement->department) : 'All',
                'gender' => $announcement->gender,
                'staff_ids' => $announcement->staff_ids ?? [],
                'title' => $announcement->title,
                'content' => $announcement->content,
                'is_schedule' => (int) ($announcement->is_schedule ?? 0),
                'start_at' => $announcement->start_at ? $announcement->start_at->format('Y-m-d H:i:s') : null,
                'end_at' => $announcement->end_at ? $announcement->end_at->format('Y-m-d H:i:s') : null,
                'attachment' => array_values($announcement->attachment ?? []),
                'created_at' => $announcement->created_at ? $announcement->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $announcement->updated_at ? $announcement->updated_at->format('Y-m-d H:i:s') : null,
            ];
        })->values();
    }
}
