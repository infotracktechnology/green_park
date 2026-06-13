<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Student;
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
        $sections = [];
        $students = [];
        $attendance = [];

        if ($request->has('branch_id')) {
            $sections = Student::whereIn('campus', explode(',', $request->branch_id))->select('section')->distinct()->get();
        }

        if ($request->has('show')) {
            if (Holiday::isHoliday($request->attendance_date, $request->branch_id, $request->attendance_timing, $request->section)) {
                return redirect()->back()->with('error', 'Holiday already exists for this date!');
            }

            $attendance = Attendance::where('attendance_date', $request->attendance_date)->where('branch_id', $request->branch_id)->where('section', $request->section)->get();

            $students = Student::whereIn('campus', explode(',', $request->branch_id))->where('section', $request->section)->where('coaching_type', 'OFFLINE')->get();

            return view('holiday.attendance', compact('sections', 'students', 'attendance'));
        }

        if ($request->has('delete')) {
            $attendance = Attendance::where('attendance_date', $request->attendance_date)->where('branch_id', $request->branch_id)->where('section', $request->section)->whereIn('timing', explode(',', $request->timing))->delete();
            return response()->json(['success' => 'Attendance deleted successfully!']);
        }

        return view('holiday.attendance', compact('sections', 'students', 'attendance'));
    }


    public function attendance_store(Request $request)
    {
        $attendanceData = [];
        foreach ($request->status as $key => $status) {
            foreach ($status as $time => $value) {
                $attendance_id = $request->attendance_id[$key][$time];
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
        $attendance = Attendance::upsert($attendanceData, ['id'], ['status']);

        return redirect()->back()->with('success', 'Attendance saved successfully.');
    }
}
