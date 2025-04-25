<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $activeUsersCount = Student::where('active', 1)->count();

        if($request->has('academic_year')) {
            DB::table('academic_year')->update(['active' => 0]);
            AcademicYear::where('academic_year', $request->academic_year)->update(['active' => 1]);
        }       
       
        return view('home', compact('activeUsersCount'));
    }
    public function parent_concern(Request $request)
    {
        $parentconcerns = DB::table('parent_concern')->where('status', '!=', 'Closed')->get();

        if($request->has('submit')) {
            $parentconcern = DB::table('parent_concern')->where('id', $request->id)->update(['status' => $request->status]);
            return redirect()->route('parent_concern')->with('success', 'Status updated successfully!');
        }       
       
        return view('announcement.parent_concern', compact('parentconcerns'));
    }

    // public function dashboard()
    // {
    //     $activeUsersCount = Student::where('active', 1)->count();
        
    //     // Remove the debugging statement after testing
    //     // dd($activeUsersCount);
        
    //     return view('home', compact('activeUsersCount'));
    // }

}

