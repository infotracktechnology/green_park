<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;

class ExamController extends Controller
{

    public function index(Request $request)
    {
        $tests = [];
        return view('exam.index', compact('tests'));
    }
    public function create()
    {
        $branches = Branch::all();
        return view('exam.create', compact('branches'));
    }


    public function store(Request $request)
    {
      
        session()->flash('success', 'Test created successfully');
        return to_route('announcement.index');
    }

    public function destroy(Request $request, Announcement $announcement)
    {
        $announcement->delete();
        session()->flash('success', 'Test deleted successfully');
        return to_route('announcement.index');
    }
 
  

}
