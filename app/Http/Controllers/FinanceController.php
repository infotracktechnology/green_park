<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class FinanceController extends Controller
{
    public function index()
    {
        return view('finance.index', compact('finances'));
    }

    public function create()
    {
        return view('finance.create', compact('branches'));
    }


    public function store(Request $request)
    {

        return redirect()->route('finance.index')->with('success', 'Answer Key added successfully!');
    }


    public function edit($id)
    {
        return view('finance.edit', compact('finance', 'branches'));
    }

    public function update(Request $request, $id)
    {
        

        return redirect()->route('finance.index')->with('success', 'Answer Key updated successfully!');
    }


    public function destroy($id)
    {

        return redirect()->route('finance.index')->with('success', 'Answer Key deleted successfully!');
    }



    public function finance()
    {
        $finances = auth('student')->user()->finance();
        return view('student.finance', compact('finances'));
    }
}
