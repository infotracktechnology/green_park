<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Workshift;
use App\Models\Staff;

class WorkshiftController extends Controller
{
    public function index()
    {
        $workshifts = Workshift::all();
    
        return view('workshift.index', compact('workshifts'));
    }

    public function create()
    {      
        return view('workshift.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['session1_ontime'] = Carbon::parse($data['session1_starttime'])->addMinutes($data['gracetime1'])->format('H:i:s');
        unset($data['gracetime1']);
        if(isset($data['gracetime2'])){
            $data['session2_ontime'] = Carbon::parse($data['session2_starttime'])->addMinutes($data['gracetime2'])->format('H:i:s');
            unset($data['gracetime2']);
        }
        $workshift = Workshift::create($data);
        return redirect()->route('workshift.index')->with('success', 'Workshift created successfully.');
    }

    public function edit(Workshift $workshift)
    {       
        return view('worksheet.edit', compact('workshift'));
    }
    

    public function update(Request $request, Workshift $workshift)
    {
       
        return redirect()->route('workshift.index')->with('success', 'Workshift updated successfully.');
    }
    

    public function destroy(Workshift $workshift)
    {
        $workshift->delete();
        return redirect()->route('worksheet.index')->with('success', 'Workshift deleted.');
    }

    public function assign(Request $request)
    {
        try{
        if($request->isMethod('post')) {
            $request->validate([
                'staff_ids' => 'required|array',
                'shift' => 'required',
            ]);
            $shift = Workshift::findorFail($request->shift);
            $staffs = Staff::whereIn('id', $request->staff_ids)->get();
            $staffs->each(function ($staff) use ($shift) {
                $staff->update(['shiftid' => $shift->id]);
            });
            return to_route('workshift.assign')->with('success', 'Shift assigned successfully.');
        }
    }
        catch(\Exception $e){
            return to_route('workshift.assign')->with('error', 'Error: ' . $e->getMessage());
    }
        $staffs = Staff::with('shift')->get();
        $shifts = Workshift::all();
        return view('workshift.assign', compact('staffs', 'shifts'));
    }


}
