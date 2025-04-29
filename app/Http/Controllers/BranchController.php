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

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->move('branch', $fileName);
        $branch = branch::create($request->all());
        return to_route('branch.index');
    }

    public function edit(Request $request, Branch $branch) {
        $districts = DB::table('district_list')->get();
        $states = DB::table('district_list')->select('State')->distinct()->get();
        return view('branch.edit', compact('branch', 'districts', 'states'));
    }
    

    public function update(Request $request, Branch $branch)
{
    $request->validate([
        'mob_no' => ['unique:branch,mob_no,' . $branch->id, 'numeric', 'min:10'],
        'email' => ['unique:branch,email,' . $branch->id, 'email'],
        'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $data = $request->all();

    

    if ($request->hasFile('file')) {
        $oldFilePath = storage_path('app/public/' . $branch->file);
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->move('branch', $fileName);

        $branch->file = 'branch/' . $fileName;
    }


    $branch->update($data); // Update the branch record with new data
    return to_route('branch.index');
}


    public function destroy(Request $request, Branch $branch) {
        $branch->delete();
        session()->flash('success', 'Branch deleted successfully');
        return to_route('branch.index');

    }
}
