<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{

    public function index(Request $request)
    {
        $timetables = Timetable::all();
        return view('timetable.index', compact('timetables'));
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

        Timetable::create([
            'academic_year' => $request->academic_year,
            'name' => $request->timetable_name,
            'start_time' => $request->start_time,
            'structure' => $timetableData
        ]);

        return to_route('timetable.index')->with('success', 'Timetable created successfully');
            
    }
      
       
    

    public function edit(Request $request, Timetable $timetable) {
        $sections = DB::table('student')->distinct()->select('section')->get();
        return view('timetable.edit', compact('timetable', 'sections'));
    }
    

    public function update(Request $request, Timetable $timetable) {
        $structure = $timetable->structure;
       foreach($request->subject as $key => $subject) {
        $structure[$request->day[$key]][$request->index[$key]]['subject'] = $subject;
       }
       $timetable->structure = $structure;
       $timetable->section = implode(',', $request->section);
       $timetable->save();

       return to_route('timetable.index')->with('success', 'Timetable updated successfully');

    }

    public function destroy(Request $request, timetable $timetable) {
        $timetable->delete();
        session()->flash('success', 'Timetable deleted successfully');
        return to_route('timetable.index');
    }
}
