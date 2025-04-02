<?php

namespace App\Http\Controllers;
use App\Models\Student;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $activeUsersCount = Student::where('active', 1)->count();

        if($request->has('academic_year')) {
            session()->put('academic_year', $request->academic_year);
        }       
       
        return view('home', compact('activeUsersCount'));
    }

    // public function dashboard()
    // {
    //     $activeUsersCount = Student::where('active', 1)->count();
        
    //     // Remove the debugging statement after testing
    //     // dd($activeUsersCount);
        
    //     return view('home', compact('activeUsersCount'));
    // }

}

