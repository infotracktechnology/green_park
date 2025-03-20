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
                    'cart_no' => "C".$i,
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
                  $cart_no = "C".$i;
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
        $branch = null;
        $hostels = [];
        $students = [];

        if ($request->has('branch')) {
            $branch = $request->branch;
            $hostels = Hostel::where('branch_id', $branch)->get();
            $students = Student::where('campus', $branch)
                ->where('hostel_dayscholar', 'Hostel')
                ->get();
        }

        if ($request->has('hostel_id')) {
            $floors = HostelRoom::where('hostel_id', $request->hostel_id)
                ->select('floor_no')
                ->distinct()
                ->get();

            return response()->json($floors);
        }


        if ($request->has('type')) {
            $rooms = HostelRoom::where('hostel_id', $request->hostel)
                ->where('floor_no', $request->floor_no)
                ->where('room_type', $request->type)
                ->get();
            return response()->json($rooms);
        }

        return view('hostel.allocation', compact('branches', 'branch', 'hostels', 'students'));
    }

   public function  storeAllocation(Request $request){

    if ($request->student_ids) {
        foreach ($request->student_ids as $student_id) {
            $student = Student::find($student_id);
            $student->hostel_id = $request->hostel;
            $student->room_id = $request->room_id;
                $student->save();
            
        }
    }

    return redirect()->route('allocation.hostel')->with('success', 'Student details successfully updated');

   }
}
