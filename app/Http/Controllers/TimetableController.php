<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Student;
use App\Models\TimetableAssign;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{

    public function index(Request $request)
    {
        $timetables = Timetable::all();
        $sections = [];

        if($request->has('submit')) {
            $timetable = Timetable::find($request->id);
            $periods=[];
            foreach($timetable->structure as $key => $section) {
                $periods[$key] = array_map(function($item) {
                    return [
                        'period' => $item['period'],
                        'subject' => '',
                    ];
                }, $section);
        }

        $assign = collect($request->section)->map(function ($section, $key) use ($periods, $request) { 
            return [
                'branch_id' => $request->branch_id,
                'coaching_type' => $request->coaching_type,
                'section' => $section,
                'periods' => $periods,
            ];
        })->toArray();

        $timetable->assign()->delete();
        $timetable->assign()->createMany($assign);
        return to_route('timetable.index')->with('success', 'Timetable Assigned Section successfully');
        }

        if($request->has('coaching_type') && $request->has('branch_id')){
            $sections = DB::table('student')->where('campus', $request->branch_id)->where('coaching_type', $request->coaching_type)->distinct()->select('section')->get();
            return response()->json($sections);
        }
            
        return view('timetable.index', compact('timetables', 'sections'));
    }

    public function create(Request $request)
    {
        
        return view('timetable.create');
    }

    public function store(Request $request)
    {
        $timetableData = [];
        $baseStartTime = Carbon::createFromFormat('H:i', $request->start_time);

        foreach ($request->days as $day) {
            $currentTime = $baseStartTime->copy();
            $daySchedule = [];
            $periodCounter = 1;
            foreach ($request->structure as $row) {
                $periodCount = $row['name'] === 'Break' ? 1 : (int)$row['period'];
                $duration = (int)$row['duration'];
                for ($i = 1; $i <= $periodCount; $i++) {
                    $session = [
                        'name' => $row['name'],
                        'period' => $row['name'] === 'Break' ? "break" : "period".$periodCounter,
                        'start_time' => $currentTime->format('H:i'),
                        'end_time' => $currentTime->copy()->addMinutes($duration)->format('H:i'),
                        'duration' => $duration,
                        'subject' => '',
                        'type' => $row['name'] === 'Break' ? 'break' : 'academic'
                    ];
                    
                    $daySchedule[] = $session;
                    $currentTime->addMinutes($duration);
                    $periodCounter++;
                }
            }
            
            $timetableData[$day] = $daySchedule;
        }


        return to_route('timetable.index')->with('success', 'Timetable created successfully');
            
    }
      

    public function edit(Request $request, Timetable $timetable) {
        $periods=[];
        $sections = [];
        if($request->has('coaching_type') && $request->has('branch_id')){
            $sections = DB::table('student')->where('campus', $request->branch_id)->where('coaching_type', $request->coaching_type)->distinct()->select('section')->get();
        }
        if($request->has('show')) {
            $periods = TimetableAssign::where('coaching_type', $request->coaching_type)->where('branch_id', $request->branch_id)->where('section', $request->section)->first();
        }
        return view('timetable.edit', compact('timetable', 'periods', 'sections'));
    }
    

    public function update(Request $request, Timetable $timetable) {
        $timetable_assign = TimetableAssign::find($request->assign_id);
        $structure =  $timetable_assign->periods;
        foreach($request->subject as $key => $subject) {
            $structure[$request->day[$key]][$request->index[$key]]['subject'] = $subject;
        }
        $timetable_assign->periods = $structure;
        $timetable_assign->save();
        return to_route('timetable.index')->with('success', 'Timetable updated successfully');

    }

    public function destroy(Request $request, timetable $timetable) {
        $timetable->delete();
        $timetable->assign()->delete();
        session()->flash('success', 'Timetable deleted successfully');
        return to_route('timetable.index');
    }
   
  
    public function timetable()
    {
        $student = Auth::user();
       
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $currentDay = Carbon::now()->format('l');
        return view('student.timetable' , compact('student', 'days', 'currentDay'));
    }
    
       
    
}
