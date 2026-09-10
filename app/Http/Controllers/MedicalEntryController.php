<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Medical, Hostel, HostelRoom,Student, Branch};

class MedicalEntryController extends Controller
{
    public function medical(Request $request)
    {
        $student = auth('student')->user();
        $entries = Medical::where('student_id', $student->student_id)->latest()->get();
        return view('student.medical', compact('entries'));
    }

    public function index(Request $request)
    {
        $entries = Medical::with(['hostel', 'student'])->when(auth()->user()->branch, function ($q) { $q->whereHas('student', function ($studentQuery) { $studentQuery->where( 'campus', 'like', '%' . auth()->user()->branch . '%' );}); })->latest()->get();
        return view('medical.index', compact('entries'));
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
        return view('medical.create', compact('students', 'branches'));
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
            
            $medical = medical::create($request->all()); 
            if ($student->deposit <= 500) { 
                return redirect()->route('medical.index') ->with('warning', 'Student deposit balance is only ₹' . $student->deposit . '. Please recharge.');
                } 
                
                return redirect()->route('medical.index')->with('success', 'Sick Room Entry added successfully!');
    }



   public function edit(Medical $medical, Request $request)
    {
        $student = Student::where('academic_year', $this->academic_year)->whereNotNull('hostel_id')->whereNotNull('room_no')->orderBy('student_name')->get();

        $hostels = Hostel::where('branch_id', $medical->branch_id)->get();

        $room = HostelRoom::where('hostel_id', $medical->hostel_id)->distinct()->pluck('room_no');

        return view('medical.edit', compact('student','hostels','room','medical'));
    }


    public function update(Request $request, Medical $medical)
    {
        $data = $request->all();
        $student = Student::where('student_id', $medical->student_id)->firstOrFail();
        $oldExpense = $medical->expense;
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
        
        $medical->update($data);

        return redirect()->route('medical.index')->with('success', 'Entry updated successfully!');
    }

    public function destroy(Medical $medical)
    {
        $student = Student::where('student_id', $medical->student_id)->first();
       if ($medical->expense > 0) {
            $student->deposit = $student->deposit + $medical->expense;
            $student->save();
        }
        $medical->delete();
        return redirect()->route('medical.index')->with('success', 'Entry deleted successfully!');
    }
}
