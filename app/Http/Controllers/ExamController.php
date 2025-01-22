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
            $filename = time() . '-' . $key . '.' . $question->getClientOriginalExtension();
            $file = $question->move('questions', $filename);
            $data['questions'][$key] = ['question' => $key, 'image' => "questions/" . $filename];
        }
        Exam::create($data);
        session()->flash('success', 'Test created successfully');
        return to_route('exam.index');
    }
    function show(Request $request, Exam $exam)
    {
        return view('exam.preview', compact('exam'));
    }
    function instruction(Request $request, $test_id)
    {
        return view('exam.instruction', compact('test_id'));
    }

    function submit(Request $request)
    {
        $student_id = auth()->guard('student')->check() ? auth()->user()->id : 0;
        $exam_answer = DB::table('exam_answer')->where('test_id', $request->test_id)->where('student_id', $student_id)->delete();
        for ($i = 0; $i < $request->total_question; $i++) {
            DB::table('exam_answer')->insert([
                'test_id' => $request->test_id,
                'q_no' => $i + 1,
                'answer' => $request->question[$i] ?? 0,
            ]);
        }
        return $student_id ? to_route('studentdashboard') : to_route('exam.index');
    }


    function destroy(Request $request, Exam $exam)
    {
        foreach ($exam->questions as $key => $question) {
            unlink($question['image']);
        }
        $exam->delete();
        session()->flash('success', 'Test deleted successfully');
        return to_route('exam.index');
    }

    function student_instruction(Request $request, $test_id)
    {
        return view('student.instruction', compact('test_id'));
    }
    function student_exam(Request $request, $test_id)
    {
        $exam = Exam::findorFail(base64_decode($test_id));
        $second = now()->diffInSeconds(Carbon::parse($exam->end_at), false);
    
        if($second < 0){
            abort(404);
        }
        return view('student.exam', compact('exam', 'second'));
    }

public function storeData(Request $request)
{
    
    $this->validate($request, [
        'id' => 'required',
        'student_id' => 'required',
        'test_id' => 'required',
        'q_no' => 'required',
        'action' => 'required'
    ]);

    Exam::create([
        'id' => $request->input('id'),
        'student_id' => $request->input('student_id'),
        'test_id' => $request->input('test_id'),
        'q_no' => $request->input('q_no'),
        'action' => $request->input('action')
    ]);

    return redirect()->back();
}

    // public function enable(Request $request)
    // {
    //     // Logic to re-enable the exam for a student
    //     // Example:
    //     $studentId = $request->input('student_id');
    //     $examId = $request->input('exam_id');

    //     // Fetch the exam and student records and update status
    //     DB::table('exam_student')
    //         ->where('student_id', $studentId)
    //         ->where('exam_id', $examId)
    //         ->update(['status' => 'enabled']);

    //     return redirect()->back()->with('success', 'Exam re-enabled for the student.');
    // }


    // public function examCompleted()
    // {
    //     // Example logic to determine if the exam is completed
    //     $examCompleted = auth()->user()->exam_completed; // or any condition you use
    //     return view('your-view-name', ['examCompleted' => $examCompleted]);
    // }
    
    // public function dashboard()
    // {
    //     $exam = Exam::where('student_id', auth()->guard('student')->user()->id)->first();
        
    //     $examStatus = 'not_ongoing'; // Default status
    //     if ($exam) {
    //         $currentTime = now();
    //         $examStartTime = $exam->start_at;
    //         $examEndTime = $exam->end_at;

    //         if ($currentTime >= $examStartTime && $currentTime <= $examEndTime) {
    //             $examStatus = 'ongoing';
    //         }
    //     }

    //     return view('dashboard', compact('exam', 'examStatus'));
    // }
    
}
