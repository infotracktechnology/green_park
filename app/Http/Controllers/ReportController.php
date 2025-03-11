<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function section_exam(Request $request)
    {
        $sections = DB::table('student')->select('section')->distinct()->orderBy('section')->get();
        return view('report.section_exam', compact('sections'));
    }
}