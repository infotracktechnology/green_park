<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::where('academic_year', $this->academic_year)->when(auth()->user()->branch, function ($query) {
            $query->where('branch_id', 'like', '%' . auth()->user()->branch . '%');
        })->latest()->get();

        return view('holiday.index', compact('holidays'));
    }

    public function create(Request $request)
    {
        if ($request->has('branch')) {
            $section = Student::whereIn('campus', explode(',', $request->branch))->where('section', '!=', '')->whereNotNull('section')->select('section')->distinct()->get();
            return response()->json($section);
        }
        return view('holiday.create');
    }


    public function store(Request $request)
    {
        // if($request->type == "Week Of"){
        //     $date = Carbon::now()->month($request->month)->startOfMonth()->nthOfMonth($request->week_of, $request->day);
        //     $data = $request->except('day');
        //     $data['day'] = $date->format('l');
        //     $data['date'] = $date->format('Y-m-d');
        //     Holiday::create($data);
        // }
        // else{
        //     $start_date = Carbon::parse($request->start_date);
        //     $dates = collect(range(0, $request->no_of_days - 1))
        //         ->map(function ($day) use ($start_date, $request) {
        //             $date = $start_date->copy()->addDays($day);
        //             return [
        //                 'date' => $date->format('Y-m-d'),
        //                 'type' => $request->type,
        //                 'name' => $request->name,
        //                 'academic_year' => $request->academic_year,
        //                 'month' => $date->format('m'),
        //                 'day' => $date->format('l'),
        //             ];
        //         });

        //     Holiday::insert($dates->toArray());

        // }
        $data = $request->all();

        if ($request->has('section')) {
            $data['section'] = implode(',', $request->section);
        }

        if ($request->has('branch_id')) {
            $data['branch_id'] = implode(',', $request->branch_id);
        }

        Holiday::create($data);
        return redirect()->route('holiday.index')->with('success', 'Holiday added successfully!');
    }


    public function edit(Holiday $holiday)
    {
        $section_ids = explode(',', $holiday->section);
        $branch_ids = explode(',', $holiday->branch_id);
        $sections = Student::where('gender', $holiday->gender)->whereIn('campus', $branch_ids)->where('hostel_dayscholar', $holiday->hostel)->select('section')->distinct()->get();
        return view('holiday.edit', compact('holiday', 'section_ids', 'sections', 'branch_ids'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        // if($request->type == "Week Of"){
        //     $date = Carbon::now()->month($request->month)->startOfMonth()->nthOfMonth($request->week_of, $request->day);
        //     $data = $request->except('day');
        //     $data['day'] = $date->format('l');
        //     $data['date'] = $date->format('Y-m-d');
        //     $holiday->update($data);
        // }
        // else{
        //     $data = $request->all();
        //     $holiday->update($data);
        // }
        $data = $request->all();

        if ($request->has('section')) {
            $data['section'] = implode(',', $request->section);
        }

        if ($request->has('branch_id')) {
            $data['branch_id'] = implode(',', $request->branch_id);
        }

        $holiday->update($data);
        return redirect()->route('holiday.index')->with('success', 'Holiday updated successfully!');
    }


    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('holiday.index')->with('success', 'Holiday deleted successfully!');
    }

    public function attendance(Request $request)
    {
        $user = auth()->user();
        $branchId = $request->branch_id ?? ($user ? ($user->branch_id ?? $user->branch) : null);
        if (is_array($branchId)) {
            $branchId = $branchId[0] ?? null;
        }

        // Get staff handling classes / sections if logged in as staff
        $staff = null;
        if ($user instanceof Staff) {
            $staff = $user;
        } elseif ($user && ($user->type ?? '') === 'Staff') {
            $staff = Staff::where('username', $user->username)->orWhere('id', $user->id)->first();
        }

        $handlingSections = [];
        if ($staff) {
            if (!empty($staff->class_assign['sections']) && is_array($staff->class_assign['sections'])) {
                $handlingSections = array_merge($handlingSections, $staff->class_assign['sections']);
            }
            $handlingSections = array_values(array_unique(array_filter($handlingSections)));
        }

        $allSections = [];
        if ($branchId) {
            $allSections = Student::whereIn('campus', explode(',', $branchId))
                ->whereNotNull('section')
                ->where('section', '!=', '')
                ->select('section')
                ->distinct()
                ->pluck('section')
                ->toArray();
        }

        $sections = !empty($handlingSections) ? $handlingSections : $allSections;
        $selectedSection = $request->section ?? ($sections[0] ?? '');
        $date = $request->attendance_date ?? date('Y-m-d');
        $timing = $request->attendance_timing ?? 'Morning,Afternoon';

        if ($request->has('delete')) {
            Attendance::where('attendance_date', $date)
                ->where('branch_id', $branchId)
                ->where('section', $selectedSection)
                ->when($request->filled('timing'), function ($q) use ($request) {
                    $q->whereIn('timing', explode(',', $request->timing));
                })
                ->delete();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['status' => true, 'message' => 'Attendance deleted successfully!']);
            }
            return response()->json(['success' => 'Attendance deleted successfully!']);
        }

        $isHoliday = false;
        if ($branchId && $selectedSection) {
            $isHoliday = Holiday::isHoliday($date, $branchId, $timing, $selectedSection);
        }

        $students = [];
        $attendance = [];
        if ($branchId && $selectedSection) {
            $attendance = Attendance::where('attendance_date', $date)
                ->where('branch_id', $branchId)
                ->where('section', $selectedSection)
                ->get();

            $academicYear = $this->academic_year ?? AcademicYear::where('active', 1)->value('academic_year');

            $students = Student::whereIn('campus', explode(',', $branchId))
                ->where('academic_year', $academicYear)
                ->where('section', $selectedSection)
                ->whereIn('coaching_type', ['OFFLINE', 'ONLINE LIVE'])
                ->where('admission_date', '<=', $date)
                ->get();
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            $studentList = $students->map(function ($row) use ($attendance) {
                $morning = $attendance->where('timing', 'Morning')->where('student_id', $row->student_id)->first();
                $afternoon = $attendance->where('timing', 'Afternoon')->where('student_id', $row->student_id)->first();

                return [
                    'id' => $row->id,
                    'student_id' => $row->student_id,
                    'student_name' => $row->student_name,
                    'academic_year' => $row->academic_year,
                    'coaching_type' => $row->coaching_type,
                    'section' => $row->section,
                    'gender' => $row->gender,
                    'morning_status' => $morning ? $morning->status : 'P',
                    'afternoon_status' => $afternoon ? $afternoon->status : 'P',
                    'morning_id' => $morning?->id,
                    'afternoon_id' => $afternoon?->id,
                    'has_morning_entry' => $morning !== null,
                    'has_afternoon_entry' => $afternoon !== null,
                ];
            });

            return response()->json([
                'status' => true,
                'academic_year' => $this->academic_year,
                'branch_id' => $branchId,
                'branches' => Branch::all(),
                'handling_sections' => $handlingSections,
                'all_sections' => $allSections,
                'sections' => $sections,
                'selected_section' => $selectedSection,
                'attendance_date' => $date,
                'attendance_timing' => $timing,
                'is_holiday' => $isHoliday,
                'students' => $studentList,
            ], 200);
        }

        $sections = Student::whereIn('campus', explode(',', $branchId ?? ''))->select('section')->distinct()->get();
        return view('holiday.attendance', compact('sections', 'students', 'attendance'));
    }

    public function attendance_store(Request $request)
    {
        $attendanceData = [];

        // Support JSON API records payload
        if ($request->has('records') && is_array($request->records)) {
            $academicYear = $request->academic_year ?? $this->academic_year ?? AcademicYear::where('active', 1)->value('academic_year');
            $branchId = $request->branch_id;
            $attendanceDate = $request->attendance_date;
            $section = $request->section;

            foreach ($request->records as $item) {
                $attendanceId = $item['id'] ?? null;
                $studentId = $item['student_id'];
                $timing = $item['timing'];
                $status = $item['status'] ?? 'A';

                if (!empty($attendanceId)) {
                    $attendanceData[] = [
                        'id' => $attendanceId,
                        'academic_year' => $academicYear,
                        'branch_id' => $branchId,
                        'attendance_date' => $attendanceDate,
                        'timing' => $timing,
                        'student_id' => $studentId,
                        'section' => $section,
                        'status' => $status,
                    ];
                } else {
                    $attendanceData[] = [
                        'academic_year' => $academicYear,
                        'branch_id' => $branchId,
                        'attendance_date' => $attendanceDate,
                        'timing' => $timing,
                        'student_id' => $studentId,
                        'section' => $section,
                        'status' => $status,
                    ];
                }
            }

            Attendance::upsert($attendanceData, ['id'], ['status']);

            return response()->json([
                'status' => true,
                'message' => 'Attendance saved successfully.',
            ], 200);
        }

        // Web form submission
        if ($request->has('status')) {
            foreach ($request->status as $key => $status) {
                foreach ($status as $time => $value) {
                    $attendance_id = $request->attendance_id[$key][$time] ?? null;
                    if (isset($attendance_id) && $attendance_id != '') {
                        $attendanceData[] = ['id' => $attendance_id, 'status' => $value ?? 'A'];
                    } else {
                        $student_id = $request->student_id[$key][$time];
                        $attendanceData[] = [
                            'academic_year' => $request->academic_year,
                            'branch_id' => $request->branch_id,
                            'attendance_date' => $request->attendance_date,
                            'timing' => $time,
                            'student_id' => $student_id,
                            'section' => $request->section,
                            'status' => $value ?? 'A',
                        ];
                    }
                }
            }
            Attendance::upsert($attendanceData, ['id'], ['status']);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => true,
                'message' => 'Attendance saved successfully.',
            ], 200);
        }

        return redirect()->back()->with('success', 'Attendance saved successfully.');
    }
}
