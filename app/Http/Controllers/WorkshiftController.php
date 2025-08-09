<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Workshift;

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


}
