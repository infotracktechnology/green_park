<?php

namespace App\Http\Controllers;
use App\Models\Student;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $activeUsersCount = Student::where('active', 1)->count();

        

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

