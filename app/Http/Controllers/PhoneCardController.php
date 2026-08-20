<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\Student;
use App\Models\PhoneCard;

class PhoneCardController extends Controller
{
    public function create(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('branch')) {
                $hostels = Hostel::where('branch_id',$request->branch)->get();
                return response()->json($hostels);
            }
            if ($request->has('hostel')) {
                $rooms = HostelRoom::where('hostel_id',$request->hostel)->distinct()->pluck('room_no');
                return response()->json($rooms);
            }
        }
        $branches = Branch::all();
        $students = collect();
        if ($request->has('show')) {
            $students = Student::where('campus', $request->branch_id)->where('hostel_id', $request->hostel_id)->where('room_no', $request->room_no)->where('academic_year', $this->academic_year)->where('hostel_dayscholar', 'Hostel')->get();
        }

        $phoneTurnStudents = PhoneCard::where('phone_date',$request->phone_date)->whereIn('student_id', $students->pluck('student_id'))->pluck('student_id');

        return view('phoneturn.create', compact('branches', 'students', 'phoneTurnStudents'));
    }

    public function store(Request $request)
    {
        $selectedStudents = $request->student_id ?? [];

        $existingEntries = PhoneCard::where('branch_id', $request->branch_id)->where('hostel_id', $request->hostel_id)->where('room_no', $request->room_no)->where('phone_date', $request->phone_date)->where('academic_year', $this->academic_year)->get();
        foreach ($existingEntries as $entry) {

            if (!in_array($entry->student_id, $selectedStudents)) {
                $student = Student::where('student_id', $entry->student_id)->where('academic_year', $this->academic_year)->where('hostel_dayscholar', 'Hostel')->first();
                if ($student) {
                    $student->deposit = $student->deposit + $entry->expense;
                    $student->save();
                }
                $entry->delete();
            }
        }

        foreach ($selectedStudents as $studentId) {
            $existingEntry = $existingEntries->firstWhere('student_id', $studentId);
            if ($existingEntry) {
                $oldExpense = $existingEntry->expense;
                $newExpense = $request->expense;
                if ($oldExpense != $newExpense) {
                    $student = Student::where('student_id', $studentId)->where('academic_year', $this->academic_year)->where('hostel_dayscholar', 'Hostel')->first();

                    if ($student) {
                        $student->deposit = $student->deposit + $oldExpense - $newExpense;
                        $student->save();
                    }
                    $existingEntry->update([ 'expense' => $newExpense, ]);
                }
                continue;
            }
            $student = Student::where('student_id', $studentId)->where('academic_year', $this->academic_year)->where('hostel_dayscholar', 'Hostel')->first();

            if (!$student) {
                continue;
            }
            $student->deposit = $student->deposit - $request->expense;
            $student->save();

            PhoneCard::create([
                'academic_year' => $this->academic_year,
                'branch_id'  => $request->branch_id,
                'hostel_id'  => $request->hostel_id,
                'student_id' => $studentId,
                'room_no'    => $request->room_no,
                'phone_date' => $request->phone_date,
                'expense'    => $request->expense,
            ]);
        }

        return redirect()->route('phoneturn.create')->with('success', 'Phone Turn Entry updated successfully.');
    }
}
