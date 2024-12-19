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
        $states = DB::table('district_list')->select('State')->distinct()->get();
        if($request->has('state')){
            $districts = DB::table('district_list')->where('State', $request->state)->select('District')->get();
            return response()->json($districts);
        }
        return view('staff.create', compact('states'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'mob_no' => ['unique:staff,mob_no', 'numeric', 'digits:10'],
            'email' => ['unique:staff,email', 'email'],
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            'experience_certificates.*' => 'file|mimes:pdf,jpeg,png|max:2048',
            'id_proof' => 'file|mimes:jpeg,png,pdf|max:2048',
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

        $districts = DB::table('district_list')->where('State', $staff->state)->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('staff.edit', compact('staff', 'districts', 'states'));
    }
    public function update(Request $request, Staff $staff)
    {

        $request->validate([
            'mob_no' => ['numeric', 'digits:10'],
            'email' => ['email'],
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            'experience_certificates.*' => 'file|mimes:pdf,jpeg,png|max:2048',
            'id_proof' => 'file|mimes:jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except(['photo', 'id_proof', 'experience_certificates']);
        if ($request->hasFile('photo')) {

            if ($staff->photo) {
                Storage::delete($staff->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }
        if ($request->hasFile('id_proof')) {

            if ($staff->id_proof) {
                Storage::delete($staff->id_proof);
            }
            $data['id_proof'] = $request->file('id_proof')->store('id_proofs', 'public');
        }
        if ($request->hasFile('experience_certificates')) {
            $certificatePaths = [];
            foreach ($request->file('experience_certificates') as $certificate) {
                $certificatePaths[] = $certificate->store('certificates', 'public');
            }
            $data['experience_certificates'] = json_encode($certificatePaths);
        }
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
