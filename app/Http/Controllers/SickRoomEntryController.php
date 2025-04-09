<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SickRoomEntry;
use App\Models\Student;

class SickRoomEntryController extends Controller
{
    public function index()
    {
        $entries = SickRoomEntry::latest()->get();
        return view('sickroom.index' , compact('entries'));
    }

    public function create()
    {
        $sections = Student::select('section')->distinct()->pluck('section');
        $students = Student::select('student_id', 'user_name', 'section')->get();
        return view('sickroom.create', compact('sections', 'students' ));

       
    }

    public function store(Request $request)
    {
        $request->validate([
            'class' => 'required|string',
            'section' => 'required|string',
            'student_id' => 'required|exists:student,id',
            'room_no' => 'required|string',
           
            'reason' => 'nullable|string',
        ]);
    
        $data = $request->all();
        $data['in_time'] = date('Y-m-d H:i:s', strtotime($request->in_time));
        $data['out_time'] = $request->out_time ? date('Y-m-d H:i:s', strtotime($request->out_time)) : null;
    
        SickRoomEntry::create($data);
    
        return redirect()->route('sickroom.index')->with('success', 'Sick Room Entry added successfully!');
    }


    public function edit(SickRoomEntry $sickroom)
    {
        $sections = Student::select('section')->distinct()->pluck('section');
        $students = Student::select('student_id', 'user_name', 'section')->get(); // Needed for dropdown
    
        return view('sickroom.edit', compact('sickroom', 'sections', 'students'));

    }

    public function update(Request $request, SickRoomEntry $sickroom)
{
    $request->validate([
        'class' => 'required|string',
        'section' => 'required|string',
        'student_id' => 'required|exists:student,student_id',
        'room_no' => 'required|string',
    ]);

    $sickroom->update($request->all());

    return redirect()->route('sickroom.index')->with('success', 'Entry updated successfully!');
   }

    public function destroy(SickRoomEntry $sickroom)
    {
        $sickroom->delete();
        return redirect()->route('sickroom.index')->with('success', 'Entry deleted successfully!');
    }
}
