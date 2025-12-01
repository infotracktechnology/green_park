<?php

namespace App\Http\Controllers;

use App\Models\Concession;
use App\Models\FeesPlanItem;
use App\Models\Student;
use Illuminate\Http\Request;

class ConcessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $concessions = Concession::all();
        return view('concession.index', compact('concessions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $academic_years = Student::distinct('academic_year')->pluck('academic_year');
        $financial_years = FeesPlanItem::distinct('financial_year')->pluck('financial_year');
        $typeselect = ['percentage', 'fixed'];
        return view('bank.create', compact('academic_years', 'financial_years', 'typeselect'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:concession,name',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric',
        ]);

        try {
            Concession::create($request->all());
            return redirect()->route('bank.create')->with('success', 'Concession created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('bank.create')->with('error', 'Failed to create concession: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Concession  $concession
     * @return \Illuminate\Http\Response
     */
    public function show(Concession $concession)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Concession  $concession
     * @return \Illuminate\Http\Response
     */
    public function edit(Concession $concession)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Concession  $concession
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Concession $concession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Concession  $concession
     * @return \Illuminate\Http\Response
     */
    public function destroy(Concession $concession)
    {
        //
    }
}
