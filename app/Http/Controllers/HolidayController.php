<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::latest()->get();
        return view('holiday.index', compact('holidays'));
    }

    public function create(Request $request)
    {
        if ($request->has('branch')) {
            // Fetch sections based only on the selected branch
            $section = Student::whereIn('campus', explode(',', $request->branch))
                ->select('section')
                ->distinct()
                ->get();
    
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
        $data=$request->all();

        if($request->has('section')){
            $data['section'] = implode(',', $request->section);
        }

        if($request->has('branch_id')){
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
        return view('holiday.edit', compact('holiday', 'section_ids','sections','branch_ids'));
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
        $data=$request->all();
        
        if($request->has('section')){
            $data['section'] = implode(',', $request->section);
        }

        if($request->has('branch_id')){
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
    $attendances = [];

    if ($request->has('branch_id')) {
        // Filter sections based on branch
        $sections = Student::whereIn('campus', explode(',', $request->branch_id))
            ->select('section')
            ->distinct()
            ->get();
    }

    if ($request->has('show')) {
        if (Holiday::isHoliday($request->attendance_date, $request->branch_id, $request->attendance_timing, $request->section)) {
            return redirect()->back()->with('error', 'Holiday already exists for this date!');
        }

        $attendances = DB::table('attendance')
            ->where('attendance_date', $request->attendance_date)
            ->where('timing', $request->attendance_timing)
            ->where('branch_id', $request->branch_id)
            ->where('section', $request->section)
            ->get()
            ->keyBy('student_id');

        $students = Student::whereIn('campus', explode(',', $request->branch_id))
            ->where('section', $request->section)
            ->get();

        return view('holiday.attendance', compact('sections', 'students', 'attendances'));
    }

    return view('holiday.attendance', compact('sections', 'students', 'attendances'));
}


    public function attendance_store(Request $request){
        $attendances = DB::table('attendance')->where('attendance_date', $request->attendance_date)->where('timing', $request->attendance_timing)->where('branch_id', $request->branch_id)->where('section', $request->section)->delete();
        $data = collect($request->status)->map(function ($status, $key) use ($request) {
            return [
                'attendance_date' => $request->attendance_date,
                'timing' => $request->timing,
                'academic_year' => $request->academic_year,
                'student_id' => $request->student_id[$key],
                'status' => $status ?? 'A',
                'section' => $request->section,
                'branch_id' => $request->branch_id,
            ];
        });
        DB::table('attendance')->insert($data->toArray());
        return to_route('attendance')->with('success', 'Attendance marked successfully!');
    }



}
