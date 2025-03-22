<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

use Session;

class AcademicYearController extends Controller
{

    public function index(Request $request)
    {
        $academicyears = AcademicYear::all();
        return view('academicyear.index', compact('academicyears'));
    }

    public function create(Request $request)
    {
        
        return view('academicyear.create');
    }

    public function store(Request $request)
    {
        $data = $request->except('enable');
        $data['enable'] = $request->enable ?? 0;
        $branch = AcademicYear::create($data);
        return to_route('academicyear.index')->with('success', 'Academic Year created successfully');
    }

    public function edit(Request $request, AcademicYear $academicyear) {
        return view('academicyear.edit', compact('academicyear'));
    }
    

    public function update(Request $request, AcademicYear $academicyear) {
        $data = $request->except('enable');
        $data['enable'] = $request->enable ?? 0;
        $academicyear->update($data);
        return to_route('academicyear.index')->with('success', 'Academic Year updated successfully');

    }

    public function destroy(Request $request, AcademicYear $academicyear) {
        $academicyear->delete();
        session()->flash('success', 'Branch deleted successfully');
        return to_route('branch.index');

    }
}
