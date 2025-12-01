<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use App\Models\Student;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name'      => 'required',
            'branch_id' => 'required',
            'name'      => Rule::unique('segments', 'name')
                                ->where('branch_id', $request->branch_id),
        ]);

        $segment = new Segment();
        $segment->name = $request->name;
        $segment->branch_id = $request->branch_id;
        $segment->is_active = $request->is_active;
        $segment->save();

        if($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Segment created successfully!', 'data' => $segment]);
        }

        return redirect()->route('bank.create')->with('success', 'Segment created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function show(Segment $segment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function edit(Segment $segment)
    {
        $branchselect = Branch::when(auth()->user()->branch, function ($query) {
            $query->where('id', auth()->user()->branch);
        })->pluck('name', 'id');
        return view('finance.segmentedit', compact('segment', 'branchselect'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Segment $segment)
    {
        $request->validate([
            'name'      => 'required',
            'branch_id' => 'required',
            'name'      => Rule::unique('segments', 'name')
                                ->where('branch_id', $request->branch_id)
                                ->ignore($segment->id),
        ]);

        $segment->update($request->all());
        return redirect()->route('bank.create')->with('success', 'Segment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Segment $segment)
    {
        //
    }

    public function assign(Request $request)
    {
        $branches = [];
        $courses = [];
        $batches = [];
        $sections = [];
        $students = [];
        $segments = null;
        $selectedbranch = null;
        $selectedcourse = null;
        $selectedbatch = null;
        $selectedsection = null;
        $sectionsbybranch = [];
        $coursesbybranch = [];
        $batchesbybranch = [];
        if(auth()->user()->type == 2 && auth()->user()->status == 1) {
            $branches = Branch::select('id', 'name')->get();
            $courses = Student::select('campus','course')
                ->whereNotNull('course')
                ->distinct()->get();
            $batches = Student::select('campus','course','batch')->whereNotNull('batch')->distinct()->get();
            $sections = Student::select('campus','course','batch','section')
                ->whereNotNull('section')
                ->distinct()->get();
            if($request->has('branch') && $request->has('course') && $request->has('batch') && $request->has('section')) {
                $selectedbranch = $request->branch;
                $selectedcourse = $request->course;
                $selectedbatch = $request->batch;
                $selectedsection = $request->section;
                $coursesbybranch = Student::where('campus', $request->branch)->select('campus','course')->whereNotNull('course')->distinct()->orderBy('course', 'asc')->get();
                $batchesbybranch = Student::where('campus', $request->branch)->where('course', $request->course)->select('campus','course','batch')->whereNotNull('batch')->distinct()->orderBy('batch', 'asc')->get();
                $sectionsbybranch = Student::where('campus', $request->branch)->where('course', $request->course)->where('batch', $request->batch)->select('campus','course','batch','section')->whereNotNull('section')->distinct()->orderBy('section', 'asc')->get();
                $students = DB::table('student')->where('campus', $request->branch)->where('course', $request->course)->where('batch', $request->batch)->where('section', $request->section == '' ? '' : $request->section)
                ->join('branch', 'student.campus', '=', 'branch.id')
                ->select('student.*', 'branch.name as campus')
                ->get();
                $segments = Segment::where('branch_id', $request->branch)->get();
            }
        } elseif (auth()->user()->branch) {
            $branches = Branch::where('id', auth()->user()->branch)->select('id', 'name')->orderBy('name', 'asc')->get();
            $courses = Student::where('campus', auth()->user()->branch)->select('campus','course')->whereNotNull('course')->distinct()->orderBy('course', 'asc')->get();
            $batches = Student::where('campus', auth()->user()->branch)->select('campus','course','batch')->whereNotNull('batch')->distinct()->orderBy('batch', 'asc')->get();
            $sections = Student::where('campus', auth()->user()->branch)->select('campus','course','batch','section')->whereNotNull('section')->distinct()->orderBy('section', 'asc')->get();
            $sectionsbybranch = $sections;
            $coursesbybranch = $courses;
            $batchesbybranch = $batches;
            $selectedbranch = auth()->user()->branch;
            $selectedcourse = $request->course;
            $selectedbatch = $request->batch;
            $selectedsection = $request->section;
            if($request->has('branch') && $request->has('course') && $request->has('batch') && $request->has('section')) {
                $batchesbybranch = $batches->where('course', $request->course)->sortBy('batch');
                $sectionsbybranch = $sections->where('course', $request->course)->where('batch', $request->batch)->sortBy('section');
                $students = DB::table('student')->where('campus', auth()->user()->branch)->where('course', $request->course)->where('batch', $request->batch)->where('section', $request->section == '' ? '' : $request->section)
                ->join('branch', 'student.campus', '=', 'branch.id')
                ->select('student.*', 'branch.name as campus')
                ->get();
                $segments = Segment::where('branch_id', auth()->user()->branch)->get();
            }
        }

        return view('finance.segmentassign', compact('students', 'branches', 'sections','courses','batches', 'selectedbranch', 'selectedsection','sectionsbybranch','coursesbybranch','batchesbybranch','selectedcourse','selectedbatch','segments'));
    }

    public function assignSegment(Request $request)
    {
        $segments = implode(",", $request->segment);
        foreach ($request->student_ids as $student_id) {
            $student = Student::find($student_id);
            $student->segments = $segments;
            $student->save();
        }
        return redirect()->route('assignsegment')->with('success', 'Segment assigned successfully!');
    }
}
