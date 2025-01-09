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
        $data['subject_name'] = implode(',', $request->subject_name);
        $data['status'] = 'perview';
        foreach ($request->questions as $key => $question) {
            $filename = time().'-'.$key.'.'.$question->getClientOriginalExtension();
            $file = $question->move('questions', $filename);
            $data['questions'][$key] = ['question' => $key, 'image' => "questions/".$filename];
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

    function submit(Request $request){
       $student_id = auth()->guard('student')->check() ? auth()->user()->id : 0;
       $exam_answer = DB::table('exam_answer')->where('test_id', $request->test_id)->where('student_id', $student_id)->delete();
       for ($i=0;$i<$request->total_question;$i++) { 
        DB::table('exam_answer')->insert([
            'test_id' => $request->test_id,
            'q_no' => $i+1,
            'answer' => $request->question[$i] ?? 0,
        ]);
       }
       return $student_id ? to_route('studentdashboard') : to_route('exam.index');
    }


    function destroy(Request $request, Exam $exam)
    {
        foreach($exam->questions as $key => $question){
            unlink($question['image']);
        }
        $exam->delete();
        session()->flash('success', 'Test deleted successfully');
        return to_route('exam.index');
    }

    function student_instruction(Request $request,$test_id){
        return view('exam.student_instruction',compact('test_id'));
    }
    function student_preview(Request $request,$test_id){
        $exam = Exam::find($test_id);
        return view('exam.student',compact('exam'));
    }

}