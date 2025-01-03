<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{

    public function index(Request $request)
    {
        $tests = Exam::all();
        if ($request->has('test_id')) {
            $test = Exam::find($request->test_id);
            $test->update([
                'start_at' => Carbon::parse($request->start_at),
                'end_at' => Carbon::parse($request->end_at),
                'duration' => Carbon::parse($request->end_at)->diffInSeconds($request->start_at),
            ]);
            session()->flash('success', 'Test Scheduled successfully');
            return to_route('exam.index');
        }
        return view('exam.index', compact('tests'));
    }
    public function create()
    {
        $branches = Branch::all();
        return view('exam.create', compact('branches'));
    }


    public function store(Request $request)
    {
        $data = $request->except('questions');
        $data['status'] = 'perview';
        foreach ($request->questions as $key => $question) {
            $filename = $data['name'].$question->getClientOriginalName();
            $file = $question->storeAs('questions', $filename, 'public');
            $data['questions'][$key] = ['question' => $key, 'image' => $file];
        }
        $exam = Exam::create($data);
        session()->flash('success', 'Test created successfully');
        return to_route('exam.index');
    }
    function show(Request $request, Exam $exam){
       
       return view('exam.preview',compact('exam'));
    }
    function instruction(Request $request,$test_id){
        return view('exam.instruction',compact('test_id'));
    }

    public function destroy(Request $request, Exam $exam)
    {
        foreach($exam->questions as $key => $question){
            Storage::disk('public')->delete($question['image']);
        }
        $exam->delete();
        session()->flash('success', 'Test deleted successfully');
        return to_route('exam.index');
    }
 
  

}
