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
        $student = Student::where('student_id', $request->student_id)->first(); 

            if ($request->expense > 0) {
                if ($request->expense > $student->deposit) {
                     return redirect()->back()->with('error', 'Insufficient Deposit Balance');
                } 
            $student->deposit = $student->deposit - $request->expense; $student->save();
            }
            
            $sickroom = SickRoomEntry::create($request->all()); 
            if ($student->deposit <= 500) { 
                return redirect()->route('sickroom.index') ->with('warning', 'Student deposit balance is only ₹' . $student->deposit . '. Please recharge.');
                } 
                
                return redirect()->route('sickroom.index')->with('success', 'Sick Room Entry added successfully!');
    }



    public function edit(SickRoomEntry $sickroom, Request $request)
    {
        $hostels = Hostel::where('branch_id', $sickroom->branch_id)->get();

        $room = HostelRoom::where('hostel_id', $sickroom->hostel_id)->distinct()->pluck('room_no');

        $student = Student::where('hostel_id', $sickroom->hostel_id)->where('room_no', $sickroom->room_no)->get();

        return view('sickroom.edit', compact('hostels', 'room', 'student', 'sickroom'));
    }


    public function update(Request $request, SickRoomEntry $sickroom)
    {
        $data = $request->all();
        $student = Student::where('student_id', $sickroom->student_id)->firstOrFail();
        $oldExpense = $sickroom->expense;
        $newExpense = $request->expense ;
        $difference = $newExpense - $oldExpense;
        if ($difference > 0) {
            if ($difference > $student->deposit) {
                return redirect()->back()
                    ->with('error', 'Insufficient Deposit Balance');
            }
            $student->deposit -= $difference;
        }
        elseif ($difference < 0) {
            $student->deposit += abs($difference);
        }
        $student->save();
        
        $sickroom->update($data);

        return redirect()->route('sickroom.index')->with('success', 'Entry updated successfully!');
    }

    public function destroy(SickRoomEntry $sickroom)
    {
        $student = Student::where('student_id', $sickroom->student_id)->first();
       if ($sickroom->expense > 0) {
            $student->deposit = $student->deposit + $sickroom->expense;
            $student->save();
        }
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
