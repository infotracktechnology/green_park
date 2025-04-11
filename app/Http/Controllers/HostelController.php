<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use App\Models\HostelRoom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;
use App\Models\Staff;
use App\Models\Student;
use App\Http\Controllers\ImportController;
use App\Models\HostelAttendance;

class HostelController extends Controller
{
    public function index(Request $request)
    {
        $hostels = Hostel::all();
        $branches = Branch::all();
        $staffs = Staff::all();
        return view('hostel.index', compact('hostels', 'branches', 'staffs'));
    }



    public function create(Request $request)
    {
        $branches = DB::table('branch')->select('id', 'name')->get(); 
        $staffs = DB::table('staff')->select('id', 'name')->get();
        $states = DB::table('district_list')->select('State')->distinct()->orderby('State')->get(); 
        if ($request->has('state')) {
            $districts = DB::table('district_list')->where('State', $request->state)->select('District')->distinct()->orderby('District')->get();
            return response()->json($districts);
        }

        return view('hostel.create', compact('branches', 'states', 'staffs')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branch,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'warden_name' => 'required|string',
            'room_type' => 'required|string',
        ]);

        $hostel = Hostel::create($request->only(['branch_id','name','type','warden_name','room_type']));

        if ($request->has('rooms')) {
            foreach ($request->rooms as $room) {
                for ($i=1; $i <= $room['no_of_cots'] ; $i++) { 
                   $hostel->rooms()->create([
                    'floor' => $room['floor'],
                    'room_no' => $room['room_no'],
                    'no_of_cots' => $room['no_of_cots'],
                    'cart_no' => "C-".$i,
                ]); 
                }
                
            }
        }

        return redirect()->route('hostel.index')->with('success', 'Hostel and Room details saved successfully.');
    }


    public function edit(Request $request, $id)
    {
        $hostel = Hostel::with('rooms')->findOrFail($id);
        $branches = Branch::all();
        $staffs = Staff::all();
        $rooms = HostelRoom::where('hostel_id', $id)->groupBy('room_no')->orderBy('id')->get()->toArray();
        return view('hostel.edit', compact('hostel',  'branches', 'staffs', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required|exists:branch,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'warden_name' => 'required|string',
            'room_type' => 'required|string',
        ]);

        $hostel = Hostel::findOrFail($id);
        $hostel->update($request->only([
            'branch_id',
            'name',
            'type',
            'warden_name',
            'room_type'
        ]));

        // $hostel->rooms()->delete();

        if ($request->has('rooms')) {
            foreach ($request->rooms as $row) {
                for ($i=1; $i <= $room['no_of_cots'] ; $i++) { 
                  $cart_no = "C-".$i;
                  $room  = HostelRoom::where('hostel_id', $id)->where('room_no', $room['room_no'])->where('cart_no', $cart_no)->first();
                  if($room){
                    $room->update([
                      'floor' => $room['floor'],
                      'room_no' => $room['room_no'],
                      'no_of_cots' => $room['no_of_cots'],
                      'cart_no' => $cart_no,
                  ]);
                  }
                  else{
                    $hostel->rooms()->create([
                      'floor' => $row['floor'],
                      'room_no' => $row['room_no'],
                      'no_of_cots' => $row['no_of_cots'],
                      'cart_no' => $cart_no,
                  ]);
                  }
                }
                
            }
        }

        return redirect()->route('hostel.index')->with('success', 'Hostel and Room details updated successfully.');
    }


    public function destroy(Request $request, Hostel $hostel)
    {
        $hostel->delete();
        $hostel->rooms()->delete();
        session()->flash('success', 'Hostel deleted successfully');
        return to_route('hostel.index');
    }

    public function show(Request $request, Hostel $hostel)
    {
        $rooms = HostelRoom::where('hostel_id', $hostel->id)->selectRaw('room_no,floor,no_of_cots,group_concat(cart_no order by id) as cart_no')->groupBy('room_no')->orderBy('id')->get();
        return view('hostel.show', compact('hostel', 'rooms'));
    }

    public function deleteRoom(Request $request)
    {
        $room = HostelRoom::where('room_no', $request->room_no)->where('hostel_id', $request->hostel_id)->delete();
        return redirect()->back()->with('success', 'Room deleted successfully.');
    }

    public function allocation(Request $request)
    {
        $branches = Branch::all();
        $hostels = [];
    
        if ($request->has('branch')) {
            $branch = $request->branch;
            $hostels = Hostel::where('branch_id', $branch)->get();
            $students = Student::where('campus', $branch)
                ->where('hostel_dayscholar', 'Hostel')
                ->get();
        }

        if ($request->has('hostel_id')) {
            $rooms = HostelRoom::where('hostel_id', $request->hostel_id)->get();
            return response()->json($rooms);
        }

        return view('hostel.allocation', compact('branches', 'hostels'));
    }
    public function storeAllocation(Request $request,ImportController $import)
    {
       
        $csvFile = $request->file('file');
        $csvData = $import->parseCSV($csvFile->getRealPath());

        $totalRooms = HostelRoom::where('hostel_id', $request->hostel)->count();
        $dataRowCount = count($csvData) - 1;

        if ($dataRowCount > $totalRooms) {
            return redirect()->back()->with('error', "The number of rows in the CSV file ($dataRowCount) exceeds the total number of rooms in the selected hostel ($totalRooms).");
        }
       
       foreach ($csvData as $key => $row) {
            $no = $key + 1;

            if (!isset($row['stuid'])) {
                return redirect()->back()->with('error', "CSV file is missing student ID ($no) row in one or more rows.");
            }

            $room  = HostelRoom::where('hostel_id', $request->hostel)->where('room_no', $row['room_no'])->where('cart_no', $row['cot_no'])->first();
            if($room){
                $stuid = $row['stuid'] ?? 0;
                $student = Student::where('student_id', $stuid)->first();
                if($student){
                $student->update(['hostel_id' => $request->hostel,'room_no' => $row['room_no'], 'cots_no' => $row['cot_no'],'hostel_dayscholar' => 'Hostel']);
                }
            }
       }

        return redirect()->route('allocation.hostel')->with('success', 'Hostel Allocation Successfully Updated.');
    }
    


    public function attendanceEntry(Request $request)
    {
        $hostels = DB::table('hostel')->select('id', 'branch_id', 'name')->get();
        $rooms = DB::table('hostel_rooms')->select('hostel_id', 'room_no')->distinct()->get();
        $students = [];
        $attendances = [];
        $branches = DB::table('branch')->select('id', 'name')->get();
        $academicyear = DB::table('student')->select('academic_year')->distinct()->get();
    
        if ($request->has('show')) {
            $students = Student::where('academic_year', $request->academic_year)
                ->where('hostel_id', $request->hostel)
                ->where('room_no', $request->room_no)
                ->select('student_id', 'student_name', 'academic_year', 'coaching_type')
                ->get();
        }
    
        return view('hostel.hostelattendance', compact(
             'hostels', 'rooms', 'students', 'attendances', 'branches', 'academicyear'
        ));
    }
    
  
    public function storeAttendance(Request $request)
    {
    
    HostelAttendance::where('attendance_date', $request->attendance_date)->where('timing', $request->timing)->where('hostel', $request->hostel)->where('room_no', $request->room_no)->delete();

        foreach ($request->student_id as $key => $student_id) {
            $status = $request->status[$key] ?? 'A';
            HostelAttendance::create([
                'academic_year'    => $request->academic_year,
                'branch_id'        => $request->branch_id,
                'attendance_date'  => $request->attendance_date,
                'timing'           => $request->timing,
                'student_id'       => $student_id,
                'hostel'           => $request->hostel,
                'room_no'          => $request->room_no,
                'status'           => $status,
            ]);
        }
    
        return redirect()->route('hostelattendance')->with('success', 'Attendance saved successfully.');
    }

}
    
 
