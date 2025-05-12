<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

use Session;

class BranchController extends Controller
{

    public function index(Request $request)
    {
        $branches = Branch::all();
        return view('branch.index', compact('branches'));
    }

    public function create(Request $request)
    {
        $districts = DB::table('district_list')->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('branch.create', compact('districts', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mob_no' => ['unique:branch,mob_no', 'numeric', 'min:10'],
            'email' => ['unique:branch,email', 'email'],
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $data = $request->except('file'); // exclude file for now
    
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move('branch', $fileName); // store in public/branch
            $data['file'] = 'branch/' . $fileName;
        }
    
        branch::create($data);
    
        return to_route('branch.index');
    }
    

    public function edit(Request $request, Branch $branch) {
        $districts = DB::table('district_list')->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('branch.edit', compact('branch', 'districts', 'states'));
    }
    

    public function update(Request $request, Branch $branch)
{
    // $request->validate([
    //     'mob_no' => ['unique:branch,mob_no,' . $branch->id, 'numeric', 'min:10'],
    //     'email' => ['unique:branch,email,' . $branch->id, 'email'],
    //     'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    // ]);

    
    $data = $request->except('file');

    if ($request->hasFile('file')) {
       
        if ($branch->file && file_exists(public_path($branch->file))) {
            unlink(public_path($branch->file));
        }

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('branch'), $fileName);

      
        $data['file'] = 'branch/' . $fileName;
    }

    $branch->update($data); 
    return to_route('branch.index');
}



    public function destroy(Request $request, Branch $branch) {
        $branch->delete();
        session()->flash('success', 'Branch deleted successfully');
        return to_route('branch.index');

    }
}
