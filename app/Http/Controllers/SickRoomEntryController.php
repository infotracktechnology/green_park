<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{SickRoomEntry, Hostel, HostelRoom,Student, Branch};

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
        $entries = SickRoomEntry::with(['hostel', 'student'])->when(auth()->user()->branch, function ($q) { $q->whereHas('student', function ($studentQuery) { $studentQuery->where( 'campus', 'like', '%' . auth()->user()->branch . '%' );}); })->latest()->get();
        return view('sickroom.index', compact('entries'));
    }

    public function create(Request $request)
    {
        if ($request->ajax() && $request->has('student')) {
            $student = Student::where('student_id', $request->student)->where('academic_year', $this->academic_year)->first();

            $hostel = Hostel::find($student->hostel_id);
            return response()->json([
                'success'     => true,
                'branch_id'   => $hostel?->branch_id,
                'hostel_id'   => $student->hostel_id,
                'hostel_name' => $hostel?->name,
                'room_no'     => $student->room_no,
                'sections'    => $student->section,
            ]);
        }

        $students = Student::where('academic_year', $this->academic_year)->when(auth()->user()->branch, fn($q) => $q->where('campus', 'like', '%' . auth()->user()->branch . '%'))->where('hostel_dayscholar', 'HOSTEL')->whereNotNull('hostel_id')->whereNotNull('room_no')->orderBy('student_name')->get();
        $branches = Branch::all();
        return view('sickroom.create', compact('students', 'branches'));
    }  


    public function store(Request $request)
    {
            
        $sickroom = SickRoomEntry::create($request->all()); 
            
        return redirect()->route('sickroom.index')->with('success', 'Sick Room Entry added successfully!');
    }



   public function edit(SickRoomEntry $sickroom, Request $request)
    {
        $student = Student::where('academic_year', $this->academic_year)->whereNotNull('hostel_id')->whereNotNull('room_no')->orderBy('student_name')->get();

        $hostels = Hostel::where('branch_id', $sickroom->branch_id)->get();

        $room = HostelRoom::where('hostel_id', $sickroom->hostel_id)->distinct()->pluck('room_no');

        return view('sickroom.edit', compact('student','hostels','room','sickroom'));
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

    public function topUp()
{
    $students = Student::where('academic_year', $this->academic_year)
        ->where('hostel_dayscholar', 'HOSTEL')
        ->orderBy('student_name')
        ->get();

    return view('hostel.topup', compact('students'));
}


public function storeTopUp(Request $request)
{
    $request->validate([
        'student_id' => 'required|array|min:1',
        'student_id.*' => 'required',
        'top_up' => 'required|numeric|min:1',
    ]);

    $students = Student::whereIn('student_id', $request->student_id)->where('academic_year', $this->academic_year)->get();

    foreach ($students as $student) {
        $student->deposit = ($student->deposit ?? 0) + $request->top_up;
        $student->save();
    }

    return redirect()
        ->route('hostel.topup')
        ->with('success', 'Top Up added successfully.');
}
}
