<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\HostelRoom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HostelController extends Controller
{
    public function index(Request $request)
    {
        $hostels = Hostel::all();
        return view('hostel.index', compact('hostels'));
    }

    public function create(Request $request)
    {
        $districts = DB::table('district_list')->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('hostel.create', compact('districts', 'states'));
    }

    public function store(Request $request)
    {
        // Validate main hostel details
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'warden_name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'nullable|numeric',
            'phone_no' => 'required|numeric|digits:10|unique:hostel,phone_no',
        ]);
    
        // Save hostel details
        $hostel = Hostel::create($request->only([
            'name', 'type', 'warden_name', 'address', 'city', 'state', 'pincode', 'phone_no'
        ]));
    
        // Save room details
        if ($request->has('rooms')) {
            foreach ($request->rooms as $room) {
                $hostel->rooms()->create([
                    'floor_no' => $room['floor_no'],
                    'room_no' => $room['room_no'],
                    'room_type' => $room['room_type'],
                    'no_of_beds' => $room['no_of_beds'],
                ]);
            }
        }
    
        return redirect()->route('hostel.index')->with('success', 'Hostel and Room details saved successfully.');
    }
    

    public function edit(Request $request, $id)
    {
        $hostel = Hostel::with('rooms')->findOrFail($id); 
        $districts = DB::table('district_list')->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('hostel.edit', compact('hostel', 'states', 'districts'));

    }

    public function update(Request $request, $id)
{
    // Validate the hostel details
    $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|string',
        'warden_name' => 'required|string',
        'address' => 'required|string',
        'city' => 'required|string',
        'state' => 'required|string',
        'pincode' => 'nullable|numeric',
        'phone_no' => 'required|numeric|digits:10',
    ]);

    // Update hostel details
    $hostel = Hostel::findOrFail($id);
    $hostel->update($request->only([
        'name', 'type', 'warden_name', 'address', 'city', 'state', 'pincode', 'phone_no'
    ]));

    $hostel->rooms()->delete();
    // Update room details
    if ($request->has('rooms')) {
        foreach ($request->rooms as $room) {
            $hostel->rooms()->create([
                'floor_no' => $room['floor_no'],
                'room_no' => $room['room_no'],
                'room_type' => $room['room_type'],
                'no_of_beds' => $room['no_of_beds'],
            ]);
        }
    }

    return redirect()->route('hostel.index')->with('success', 'Hostel and Room details updated successfully.');
}


    public function destroy(Request $request, Hostel $hostel)
    {
        DB::transaction(function () use ($hostel) {
            // Delete room details
            $hostel->rooms()->each(function ($room) {
                $room->delete();
            });

            // Delete hostel
            $hostel->delete();
        });

        session()->flash('success', 'Hostel deleted successfully');
        return to_route('hostel.index');
    }
    public function deleteRoom($id)
    {
        // Find the room by its ID
        $room = HostelRoom::findOrFail($id);
        
        // Log the room being deleted for debugging purposes
        \Log::info('Deleting room:', ['room' => $room]);
        
        // Delete the room only
        $room->delete();
        
        // Log the success for debugging purposes
        \Log::info('Room deleted successfully.', ['room_id' => $id]);
        
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Room deleted successfully.');
    }
    
       
}
