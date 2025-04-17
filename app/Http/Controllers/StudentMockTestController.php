<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentMockTestController extends Controller
{
    public function index()
{
    return view('student.mocktest'); 
}

}
