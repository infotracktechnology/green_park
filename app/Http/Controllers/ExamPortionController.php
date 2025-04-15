<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use App\Models\Examportion;
use App\Models\Branch;
use App\Models\AcademicYear;

use Illuminate\Support\Facades\DB;

class ExamPortionController extends Controller
{
    public function index()
    {
        $academic_years = AcademicYear::all();
        $examportions = Examportion::all();
        $branches = Branch::all();
        $branchList = DB::table('branch')->pluck('name', 'id')->toArray();
        return view('examportion.index', compact('examportions', 'branches', 'branchList'));
    }
    public function create()
    {

        $academicyear = AcademicYear::all();
        $branches = Branch::all();
        return view('examportion.create', compact('branches'));
    }
    public function store(Request $request)
    {
        $data = $request->except('attachment');
        $data['branch_id'] = implode(',', $request->branch_id);
        $data['coaching_type'] = implode(',', $request->coaching_type);
        $data['academic_year'] = $request->academic_year; 
        if ($request->has('attachment')) {
            $fileName = time() . '.' . $request->attachment->extension();
            $request->attachment->move(public_path('assets/attachments'), $fileName);
            $data['attachment'] = 'assets/attachments/' . $fileName;
        } else {
            $data['attachment'] = null;
        }
        Examportion::create($data);
        return to_route('examportion.index')->with('success', 'Examportion created successfully');
    }
    public function destroy(Request $request, Examportion $examportion)
    {
        $examportion->delete();
        session()->flash('success', 'Examportion deleted successfully');
        return to_route('examportion.index');
    }
    public function examportion(Request $request)
    {
        $examportion = Examportion::latest()->first();
       
        return view('student.examportion', compact('examportion'));
    }
}
