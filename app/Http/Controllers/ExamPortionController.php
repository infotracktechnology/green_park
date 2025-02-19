<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use App\Models\Examportion;
use App\Models\Branch;

class ExamPortionController extends Controller
{
    public function index()
    {
        $examportions = Examportion::all();
        return view('examportion.index', compact('examportions'));
    }
    public function create()
    {
        $branches = Branch::all();
        return view('examportion.create', compact('branches'));
    }
    public function store(Request $request)
    {
        $data = $request->except('attachment');
        $attachment = $request->attachment;
        $filename = time().'.'.$attachment->getClientOriginalExtension();
        $file = $attachment->move('examportions', $filename);
        $data['attachment'] = 'examportions/'.$filename;
        Examportion::create($data);
        return to_route('examportion.index');
    }
    public function destroy(Request $request, Examportion $examportion)
    {
        $examportion->delete();
        session()->flash('success', 'Examportion deleted successfully');
        return to_route('examportion.index');
    }
    public function examportion(Request $request)
    {
        $examportion = auth()->user()->examportion()->latest()->first();
       
        return view('student.examportion', compact('examportion'));
    }
}
