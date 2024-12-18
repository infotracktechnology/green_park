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

        $districts = DB::table('district_list')->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('staff.create', compact('districts', 'states'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'mob_no' => ['numeric', 'digits:10'],
            'email' => ['email'],
            'photo' => 'image|mimes:jpeg,png,jpg|max:1024',
            'experience_certificates.*' => 'file|mimes:pdf,jpeg,png|max:1024',
            'id_proof' => 'file|mimes:jpeg,png|max:1024',
        ]);

        $data = $request->except(['photo', 'id_proof', 'experience_certificates']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }
        if ($request->hasFile('id_proof')) {
            $data['id_proof'] = $request->file('id_proof')->store('id_proofs', 'public');
        }
        if ($request->hasFile('experience_certificates')) {
            $certificatePaths = [];
            foreach ($request->file('experience_certificates') as $certificate) {
                $certificatePaths[] = $certificate->store('certificates', 'public');
            }
            $data['experience_certificates'] = json_encode($certificatePaths);
        }
        Staff::create($data);

        return redirect()->route('staff.index')->with('success', 'Staff created successfully');
    }
    public function edit(Request $request, Staff $staff)
    {

        $districts = DB::table('district_list')->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('staff.edit', compact('staff', 'districts', 'states'));
    }
    public function update(Request $request, Staff $staff)
    {

        $request->validate([
            'mob_no' => ['numeric', 'digits:10'],
            'email' => ['email'],
            'photo' => 'image|mimes:jpeg,png,jpg|max:1024',
            'experience_certificates.*' => 'file|mimes:pdf,jpeg,png|max:1024',
            'id_proof' => 'file|mimes:jpeg,png|max:1024',
        ]);

        $data = $request->except(['photo', 'id_proof', 'experience_certificates']);
        if ($request->hasFile('photo')) {
            $filename= rand().'.jpg';
            $data['photo'] = $request->file('photo')->storeAs('photos', $filename, 'public');
        }
        if ($request->hasFile('id_proof')) {
            $filename= rand().'.jpg';
            $data['id_proof'] = $request->file('id_proof')->storeAs('id_proofs', $filename, 'public');
        }
        if ($request->hasFile('experience_certificates')) {
          $data['experience_certificates'] = $request->file('experience_certificates')->store('certificates', 'public');
        }
        $staff->update($data);

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully')->withImageName($data['photo']);

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
