<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Exam;


class ExamController extends Controller
{

    public function index(Request $request)
    {
        $tests = Exam::all();
        return view('exam.index', compact('tests'));
    }
    public function create()
    {
        $branches = Branch::all();
        return view('exam.create', compact('branches'));
    }


    public function store(Request $request)
    {
        $data = $request->except('question');
        $exam = Exam::create($data);
        session()->flash('success', 'Test created successfully');
        return to_route('exam.index');
    }

    public function destroy(Request $request, Exam $exam)
    {
        $exam->delete();
        session()->flash('success', 'Test deleted successfully');
        return to_route('exam.index');
    }
 
  

}
