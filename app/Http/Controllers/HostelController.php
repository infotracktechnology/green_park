<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\HostelRoom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;
use App\Models\Staff;
use App\Models\Student;
use App\Http\Controllers\ImportController;
use App\Models\HostelAttendance;
use App\Models\InOutRegister;
use App\Models\HostelCourier;

class HostelController extends Controller
{
    public function index(Request $request)
    {
        $hostels = Hostel::when(auth()->user()->branch, function ($query) {
            $query->where('branch_id', 'like', '%' . auth()->user()->branch . '%');
        })
            ->get();



        $branches = Branch::all();
        $staffs = Staff::all();

        return view('hostel.index', compact('hostels', 'branches', 'staffs'));
    }


    public function create(Request $request)
    {
        // $branches = DB::table('branch')->select('id', 'name')->get(); 
        $staffs = DB::table('staff')->select('id', 'name')->get();
        $states = DB::table('district_list')->select('State')->distinct()->orderby('State')->get();
        if ($request->has('state')) {
            $districts = DB::table('district_list')->where('State', $request->state)->select('District')->distinct()->orderby('District')->get();
            return response()->json($districts);
        }

        return view('hostel.create', compact('states', 'staffs'));
    }

    public function store(Request $request)
    {
       

        $hostel = Hostel::create($request->only(['branch_id', 'name', 'type','room_type']));

        if ($request->has('rooms')) {
            foreach ($request->rooms as $room) {
                for ($i = 1; $i <= $room['no_of_cots']; $i++) {
                    $cart_no = $room['cot_type'].$i;
                    $hostel->rooms()->create([
                        'floor' => $room['floor'],
                        'room_no' => $room['room_no'],
                        'no_of_cots' => $room['no_of_cots'],
                        'cot_type' => $room['cot_type'],
                        'cart_no' => $cart_no,
                    ]);
                }
            }
        }

        return redirect()->route('hostel.index')->with('success', 'Hostel and Room details saved successfully.');
    }


    public function edit(Request $request, $id)
    {
        $hostels = Hostel::with('rooms')->findOrFail($id);

        $staffs = Staff::all();
        $rooms = HostelRoom::where('hostel_id', $id)->select('floor', 'room_no', 'cot_type', DB::raw('MAX(id) as id'), DB::raw('MAX(no_of_cots) as no_of_cots'))
            ->groupBy('floor', 'room_no', 'cot_type')
            ->orderBy('room_no')
            ->orderBy('cot_type')
            ->get()
            ->toArray();
        return view('hostel.edit', compact('hostels',  'staffs', 'rooms'));
    }

    public function update(Request $request, $id)
    {
      
        $hostel = Hostel::findOrFail($id);
        $hostel->update($request->only(['branch_id','name','type','room_type']));

        if ($request->has('rooms')) {
        foreach ($request->rooms as $row) {
            $cotCount = (int) $row['no_of_cots'];
            for ($i = 1; $i <= $cotCount; $i++) {
                $cart_no = $row['cot_type'] . $i;
                $room = HostelRoom::where('hostel_id', $id)->where('room_no', $row['room_no'])->where('cart_no', $cart_no)->first();
                if ($room) {
                    $room->update([
                        'floor'      => $row['floor'],
                        'room_no'    => $row['room_no'],
                        'no_of_cots' => $cotCount,   
                        'cot_type'   => $row['cot_type'],
                        'cart_no'    => $cart_no,
                    ]);
                } else {
                    $hostel->rooms()->create([
                        'floor'      => $row['floor'],
                        'room_no'    => $row['room_no'],
                        'no_of_cots' => $cotCount,
                        'cot_type'   => $row['cot_type'],
                        'cart_no'    => $cart_no,
                    ]);
                }
            }
            HostelRoom::where('hostel_id', $id)->where('room_no', $row['room_no'])->where('cot_type', $row['cot_type'])->whereRaw('CAST(SUBSTRING(cart_no, 3) AS UNSIGNED) > ?', [$cotCount])->delete();
        }

    }
        

    return redirect()->route('hostel.index')->with('success', 'Hostel and Room details updated successfully.');
    }


    public function destroy(Request $request,$id)
    {
        $hostels = Hostel::findOrFail($id);
        $hostels->delete();
        $hostels->rooms()->delete();
        session()->flash('success', 'Hostel deleted successfully');
        return to_route('hostel.index');
    }


    public function show(Request $request,$id)
    {
        $hostels = Hostel::with('rooms')->findOrFail($id);
        $rooms = HostelRoom::where('hostel_id', $hostels->id)
        ->selectRaw("room_no,
        floor,
         SUM(CASE WHEN cot_type = 'U-' THEN 1 ELSE 0 END) as upper_cots,
            SUM(CASE WHEN cot_type = 'L-' THEN 1 ELSE 0 END) as lower_cots,
            COUNT(*) as no_of_cots,
            group_concat(cart_no order by id) as cart_no")
        ->groupBy('room_no')
        ->orderBy('id')->get();

        foreach ($rooms as $room) {
            $cots = HostelRoom::where('hostel_id', $hostels->id)->where('room_no', $room->room_no)->get();

            $occupiedCots = Student::where('hostel_id', $hostels->id)
            ->where('room_no', $room->room_no)
            ->where('academic_year', $this->academic_year)
            ->pluck('cots_no')->toArray();

            foreach ($cots as $cot) {
               $cot->status = in_array($cot->cart_no, $occupiedCots) ? 'occupied' : 'free';
            }
            $room->cots = $cots;
        }
        

        return view('hostel.show', compact('hostels', 'rooms'));
    }

    public function deleteRoom(Request $request)
    {
        $room = HostelRoom::where('room_no', $request->room_no)->where('hostel_id', $request->hostel_id)->delete();
        return redirect()->back()->with('success', 'Room deleted successfully.');
    }

    public function allocation(Request $request)
    {
        return view('hostel.allocation');
    }
    public function storeAllocation(Request $request, ImportController $import)
    {

        $csvFile = $request->file('file');
        $csvData = $import->parseCSV($csvFile->getRealPath());
        $hostel_name = array_column($csvData, 'hosname');
        $hostels = Hostel::where('branch_id', $request->branch)->whereIn('name', array_unique($hostel_name))->get()->keyBy('name');
        try {
        foreach ($csvData as $key => $row) {
            $no = $key + 1;

            if (!isset($row['stuid']) || !isset($row['hosname']) || !isset($row['room_no']) || !isset($row['cot_no'])) {
              return redirect()->back()->with('error', "CSV file is missing required fields in row ($no) & column (stuid, hosname, room_no, cot_no).");
            }

            $hostel = $hostels[$row['hosname']] ?? null;

            if(!$hostel){
              return redirect()->back()->with('error', "CSV file is missing hostel name ($no) row in one or more rows.");
            }

            $room  = HostelRoom::firstWhere(['hostel_id' => $hostel->id, 'room_no' => $row['room_no'], 'cart_no' => $row['cot_no']]);
            if($room){
                $student = Student::firstWhere('student_id', $row['stuid']);
                if($student){
                    $student->update(['hostel_id' => $hostel->id, 'room_no' => $row['room_no'], 'cots_no' => $row['cot_no'],'section' => $row['sec']]);
                }
            }
        }

        return redirect()->route('allocation.hostel')->with('success', 'Hostel Allocation Successfully Updated Check Hostel Room Wise Report.');
        }
        catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function RoomReallocation(Request $request)
    {
        $branchId = $request->branch;
        $hostelId = $request->hostel;
        $roomNo   = $request->room;


        if ($request->isMethod('post')) {
            $data = $request->except(['_token', 'student_id']);
            $student = Student::where('student_id', $request->student_id)->update($data);
            return back()->with('success', "New students allocated successfully for student $request->student_id in room $request->room_no");
        }

        if ($request->reason && $request->datetime) {
            $student = Student::where('student_id', $request->student_id)->update(['hostel_id' => '', 'room_no' => '', 'cots_no' => '', 'hostel_dayscholar' => 'DAYSCHOLAR', 'ac_nonac' => '']);
            $vacate = DB::table('vacate_log')->insert($request->all());
            return back()->with('success', "Room vacated successfully for student $request->student_id");
        }


        $hostels = $branchId ? Hostel::where('branch_id', $branchId)->get() : collect();

        $room = $hostelId ? HostelRoom::where('hostel_id', $hostelId)->distinct()->pluck('room_no') : collect();

        $availableStudents = Student::where('hostel_dayscholar', 'HOSTEL')->whereNull('cots_no')->get();

        $allocatedStudents = ($hostelId && $roomNo) ? Student::where('hostel_id', $hostelId)->where('room_no', $roomNo)->get() : collect();

        $carts = HostelRoom::where('hostel_id', $hostelId)->where('room_no', $roomNo)->whereNotIn('cart_no',fn($q) => $q->select('cots_no')->from('student')->where('hostel_id', $hostelId)->where('room_no', $roomNo))->get()->pluck('cart_no');

        return view('hostel.reallocation', compact('hostels', 'room', 'availableStudents', 'allocatedStudents', 'carts'));
    }

public function RoomTransfer(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'student_id'   => 'required|exists:student,student_id',
                'to_hostel_id' => 'required|exists:hostel,id',
                'to_room_no'   => 'required',
                'to_cot_no'    => 'required',
            ]);

            $isOccupied = Student::where('hostel_id', $request->to_hostel_id)
                ->where('academic_year', $this->academic_year)
                ->where('room_no', $request->to_room_no)
                ->where('cots_no', $request->to_cot_no)
                ->exists();

            if ($isOccupied) {
                return redirect()->back()->with('error', 'Selected cot is already occupied. Please choose another.');
            }

            $student = Student::where('student_id', $request->student_id)->firstOrFail();
            $student->update([
                'hostel_id' => $request->to_hostel_id,
                'room_no'   => $request->to_room_no,
                'cots_no'   => $request->to_cot_no,
            ]);

            return redirect()->back()->with('success', 'Student room transferred successfully!');
        }

        if ($request->ajax()) {
            if ($request->has('get_student_details')) {
                $student = Student::where('student_id', $request->student_id)->first();
                if ($student) {
                    $hostel = Hostel::find($student->hostel_id);
                    return response()->json([
                        'success' => true,
                        'hostel_name' => $hostel ? $hostel->name : 'N/A',
                        'room_no' => $student->room_no,
                        'cot_no' => $student->cots_no,
                    ]);
                }
                return response()->json(['success' => false]);
            }

            if ($request->has('branch_id')) {
                $hostels = Hostel::where('branch_id', $request->branch_id)->get();
                return response()->json($hostels);
            }

            if ($request->has('hostel_id') && !$request->has('room_no')) {
                $rooms = HostelRoom::where('hostel_id', $request->hostel_id)
                    ->distinct()
                    ->pluck('room_no');
                return response()->json($rooms);
            }

            if ($request->has('hostel_id') && $request->has('room_no')) {
                $hostelId = $request->hostel_id;
                $roomNo = $request->room_no;

                $occupiedCots = Student::where('hostel_id', $hostelId)
                    ->where('room_no', $roomNo)
                    ->where('academic_year', $this->academic_year)
                    ->whereNotNull('cots_no')
                    ->pluck('cots_no')
                    ->toArray();

                $freeCots = HostelRoom::where('hostel_id', $hostelId)
                    ->where('room_no', $roomNo)
                    ->whereNotIn('cart_no', $occupiedCots)
                    ->pluck('cart_no');

                return response()->json($freeCots);
            }
        }
        $students = Student::when($this->academic_year, fn($q) => $q->where('academic_year', $this->academic_year))
            ->when(auth()->user()->branch, fn($q) => $q->where('campus', 'like', '%' . auth()->user()->branch . '%'))
            ->where('hostel_dayscholar', 'HOSTEL')    
            ->whereNotNull('hostel_id')
            ->whereNotNull('room_no')
            ->whereNotNull('cots_no')
            ->get();

        $branches = Branch::all();
        return view('hostel.room_transfer', compact('students', 'branches'));
    }

    public function attendanceEntry(Request $request)
    {
        $hostels = $request->branch_id ? Hostel::where('branch_id', $request->branch_id)->get() : collect();
        
        $section = $request->hostel_id ? Student::where('hostel_id', $request->hostel_id)->distinct()->pluck('section') : collect();

        $students = $request->section ? Student::where('hostel_id', $request->hostel_id)->when($request->section, fn($q) => $q->where('section', $request->section))->get() : collect();
        $attendance = $request->attendance_date ? HostelAttendance::where('hostel_id', $request->hostel_id)->where('section', $request->section)->where('attendance_date', $request->attendance_date)->get() : collect();

        if ($request->has('delete')) {
            $attendance = HostelAttendance::where('hostel_id', $request->hostel_id)->where('section', $request->section)->where('attendance_date', $request->attendance_date)->whereIn('timing', explode(',', $request->timing))->delete();
            return response()->json(['success' => 'Attendance deleted successfully!']);
        }

        return view('hostel.hostelattendance', compact('hostels', 'section', 'students', 'attendance'));
    }


    public function storeAttendance(Request $request)
    {
         $attendanceData = [];
        foreach ($request->status as $key => $status) {
            $room_no = $request->room_no[$key];
            foreach ($status as $time => $value) {
                if ($request->has('attendance_id')) {
                    $attendance_id = $request->attendance_id[$key][$time];
                    $attendanceData[] = ['id' => $attendance_id, 'status' => $value ?? 'A'];
                } else {
                    $student_id = $request->student_id[$key][$time];
                    $attendanceData[] = [
                        'academic_year' => $this->academic_year,
                        'branch_id' => $request->branch_id,
                        'attendance_date' => $request->attendance_date,
                        'hostel_id' => $request->hostel_id,
                        'timing' => $time,
                        'student_id' => $student_id,
                        'section' => $request->section,
                        'room_no' => $room_no,
                        'status' => $value ?? 'A',
                    ];
                }
            }
        }

        $attendance = HostelAttendance::upsert($attendanceData, ['id'], ['status']);
        return redirect()->back()->with('success', 'Hostel Attendance saved successfully.');
    }

    public function InOutRegister(Request $request)
    {
        $register = InOutRegister::with(['hostel', 'student'])->latest()->get();

        if ($request->isMethod('post')) {
            $data = $request->except(['_token', 'branch']);
            $outer = InOutRegister::create($data);
            return back()->with('success', "Register Outer entry added successfully");
        }

        if ($request->update) {
            $update = InOutRegister::find($request->update)->update(['datetime_in' => $request->datetime_in]);
            return back()->with('success', "Register In entry updated successfully");
        }

        if ($request->ajax()) {
            if ($request->has('room')) {
                $students = Student::where('hostel_id', $request->hostel)->where('room_no', $request->room)->get();
                return response()->json($students);
            }
            if ($request->has('hostel')) {
                $rooms = HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no');
                return response()->json($rooms);
            }
            if ($request->has('branch')) {
                $hostels = Hostel::where('branch_id', $request->branch)->get();
                return response()->json($hostels);
            }
        }

        return view('hostel.inoutregister', compact('register'));
    }
     public function HostelCourier(Request $request)
     {
        if ($request->isMethod('post')) {
            $data = $request->except(['_token', 'branch']);
            $courier = HostelCourier::create($data);
            return back()->with('success', "Courier entry added successfully");
        }
        if($request->delete) {
           $courier = HostelCourier::find($request->delete)->delete();
           return back()->with('success', "Courier entry deleted successfully");
        }
        $hostelcourier = HostelCourier::with(['hostel', 'student'])->latest()->get();
        return view('hostel.hostelcourier', compact('hostelcourier'));
     }
     public function Topup(Request $request) {
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

            if ($request->isMethod('post')) {
                foreach ($request->student_id as $studentId) {
                    $student = Student::where('student_id', $studentId)->where('academic_year', $this->academic_year)->where('hostel_dayscholar', 'HOSTEL')->first();
                    if ($student) {
                        $student->deposit = ($student->deposit) + $request->amount;
                        $student->save();
                    }
                }

                return redirect()->route('hostel.topup')->with('success', 'Top Up completed successfully.');
            }

            $branches = Branch::orderBy('name')->get();
            $hostels = $request->branch ? Hostel::where('branch_id', $request->branch)->get() : collect();
            $rooms = $request->hostel ? HostelRoom::where('hostel_id', $request->hostel)->distinct()->pluck('room_no') : collect();
            $students = collect();

            if ($request->has('show')) {

                $students = Student::where('academic_year', $this->academic_year)->where('hostel_dayscholar', 'HOSTEL')->where('campus', $request->branch_id)->where('hostel_id', $request->hostel_id)->when( $request->room_no && $request->room_no != 'all', function ($query) use ($request) { $query->where('room_no', $request->room_no); } )->orderBy('student_name')->get();
            }
            return view('hostel.topup', compact('branches','hostels','rooms','students'));
        }
     }

