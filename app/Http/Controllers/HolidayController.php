<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::latest()->get();
        return view('holiday.index', compact('holidays'));
    }

    public function create()
    {
        return view('holiday.create');
    }


    public function store(Request $request)
    {
        if($request->type == "Week Of"){
            $date = Carbon::now()->month($request->month)->startOfMonth()->nthOfMonth($request->week_of, $request->day);
            $data = $request->except('day');
            $data['day'] = $date->format('l');
            $data['date'] = $date->format('Y-m-d');
            Holiday::create($data);
        }
        else{
            $start_date = Carbon::parse($request->start_date);
            $dates = collect(range(0, $request->no_of_days - 1))
                ->map(function ($day) use ($start_date, $request) {
                    $date = $start_date->copy()->addDays($day);
                    return [
                        'date' => $date->format('Y-m-d'),
                        'type' => $request->type,
                        'name' => $request->name,
                        'academic_year' => $request->academic_year,
                        'month' => $date->format('m'),
                        'day' => $date->format('l'),
                    ];
                });

            Holiday::insert($dates->toArray());
         
        }

        return redirect()->route('holiday.index')->with('success', 'Holiday added successfully!');
    }


    public function edit(Holiday $holiday)
    {
        return view('holiday.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        if($request->type == "Week Of"){
            $date = Carbon::now()->month($request->month)->startOfMonth()->nthOfMonth($request->week_of, $request->day);
            $data = $request->except('day');
            $data['day'] = $date->format('l');
            $data['date'] = $date->format('Y-m-d');
            $holiday->update($data);
        }
        else{
            $data = $request->all();
            $holiday->update($data);
        }
        return redirect()->route('holiday.index')->with('success', 'Holiday updated successfully!');
    }


    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('holiday.index')->with('success', 'Holiday deleted successfully!');
    }



}
