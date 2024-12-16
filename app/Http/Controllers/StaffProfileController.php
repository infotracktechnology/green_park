<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\staff;
use Illuminate\Support\Facades\DB;

class StaffProfileController extends Controller
{
    public function index(Request $request)
    {
        // $staffprofile = staffprofile::all();
        return view('staff.index');
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
            'mob_no' => ['unique:staff,mob_no', 'numeric', 'min:10'],
            'email' => ['unique:staff,email', 'email'],
        ]);
        $staff = staff::create($request->all());
        return to_route('staff.index');
    }

    // public function edit(Request $request, Branch $branch) {
    //     $districts = DB::table('district_list')->get();
    //     $states = DB::table('district_list')->select('State')->distinct()->get();
    //     return view('branch.edit', compact('branch', 'districts', 'states'));
    // }
    

    // public function update(Request $request, Branch $branch) {
    //     $data=$request->all();
    //     $branch->update($data);
    //     return to_route('branch.index');

    // }

    // public function destroy(Request $request, Branch $branch) {
    //     $branch->delete();
    //     session()->flash('success', 'Branch deleted successfully');
    //     return to_route('branch.index');

    // }

}

