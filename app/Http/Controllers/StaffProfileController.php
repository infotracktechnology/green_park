<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StaffProfileController extends Controller
{

    public function index(Request $request)
    {
        $staff = Staff::all();
        return view('staff.index', compact('staff'));
    }
    public function create(Request $request)
    {
        $states = DB::table('district_list')->select('State')->distinct()->orderBy('State')->get();
        if($request->has('state')){
            $districts = DB::table('district_list')->where('State', $request->state)->select('District')->distinct()->orderBy('District')->get();
            return response()->json($districts);
        }
        return view('staff.create', compact('states'));
    }
    public function store(Request $request)
    {
       
    
        // Handle the file upload if a photo is provided
        $photoPath = null;
        $path='';
        $children_details=[];
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->move('uploads/staff', $request->file('photo')->getClientOriginalName());
            $path = 'uploads/staff/'.$request->file('photo')->getClientOriginalName();
        }
    
        // Save the staff details along with children details
        $staff = new Staff($request->except('children'));
        $staff->photo = $path;
    
        // Convert children to JSON before saving
        if ($request->has('children')) {
            
           foreach ($request->children as $child) {
               $children_details[] = ['name' => $child['name'], 'class' => $child['class'], 'section' => $child['section']];
           }
        }
        $staff->children_details =  $children_details;
    
        $staff->save();
    
        return redirect()->route('staff.index')->with('success', 'Staff details saved successfully!');
    }
    
    

    
    public function edit(Request $request, Staff $staff)
    {
        $districts = DB::table('district_list')->where('State', $staff->state)->select('District')->distinct()->orderBy('District')->get();
        $states = DB::table('district_list')->select('State')->distinct()->orderBy('State')->get();
    
        return view('staff.edit', compact('staff', 'districts', 'states'));
       
       
    }
    
    public function update(Request $request, Staff $staff)
    {
      
        $data = $request->except(['photo', 'children']);
    
            $path='';
            $children_details=[];
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->move('uploads/staff', $request->file('photo')->getClientOriginalName());
                $path = 'uploads/staff/'.$request->file('photo')->getClientOriginalName();
            }
           
            $data['photo'] = $path;
            if ($request->has('children')) {
            
                foreach ($request->children as $child) {
                    $children_details[] = ['name' => $child['name'], 'class' => $child['class'], 'section' => $child['section']];
                }
             }
             $data['children_details'] =  $children_details;

        $staff->update($data);
    
        return redirect()->route('staff.index')->with('success', 'Staff updated successfully');
    }
    
    public function destroy(Request $request, Staff $staff)
    {

        if ($staff->photo) {
            Storage::delete($staff->photo);
        }
        if ($staff->id_proof) {
            Storage::delete($staff->id_proof);
        }
        if ($staff->experience_certificates) {
            $certificates = json_decode($staff->experience_certificates, true);
            foreach ($certificates as $certificate) {
                Storage::delete($certificate);
            }
        }
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully');
    }

  



}
