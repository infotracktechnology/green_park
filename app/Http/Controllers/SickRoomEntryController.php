<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{SickRoomEntry, Hostel, HostelRoom,Student};

class SickRoomEntryController extends Controller
{
    public function sickroom(Request $request)
    {
        $student = auth('student')->user();
        $entries = SickRoomEntry::where('student_id', $student->student_id)->latest()->get();
        return view('student.sickroom', compact('entries'));
    }

    public function index(Request $request)
    {
        $entries = SickRoomEntry::with(['hostel', 'student'])->latest()->get();
        return view('sickroom.index', compact('entries'));
    }

    public function create(Request $request)
    {
        return view('sickroom.create');
    }


    public function store(Request $request)
    {

        $sickroom = SickRoomEntry::create($request->all());
        return redirect()->route('sickroom.index')->with('success', 'Sick Room Entry added successfully!');
    }



    public function edit(SickRoomEntry $sickroom, Request $request)
    {
        $hostel = Hostel::where('branch_id', $sickroom->branch_id)->get();

        $room = HostelRoom::where('hostel_id', $sickroom->hostel_id)->distinct()->pluck('room_no');

        $student = Student::where('hostel_id', $sickroom->hostel_id)->where('room_no', $sickroom->room_no)->get();

        return view('sickroom.edit', compact('hostel', 'room', 'student', 'sickroom'));
    }


    public function update(Request $request, SickRoomEntry $sickroom)
    {
        $data = $request->all();
        
        $sickroom->update($data);

        return redirect()->route('sickroom.index')->with('success', 'Entry updated successfully!');
    }

    public function destroy(SickRoomEntry $sickroom)
    {
        $sickroom->delete();
        return redirect()->route('sickroom.index')->with('success', 'Entry deleted successfully!');
    }
}
