<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Exam;
use App\Models\Student;
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

    public function submit(Request $request)
    {
        $student_id = $request->student_id ?? 0;


        DB::table('exam_answer')->where('test_id', $request->test_id)->where('student_id', $student_id)->delete();

        for ($i = 1; $i <= $request->total_question; $i++) {
                $status = $request->status[$i] ?? null;
                $answer = $status == 'answer' || $status == 'answer & mark' ? $request->question[$i] : 0;

                DB::table('exam_answer')->insert([
                    'test_id' => $request->test_id,
                    'student_id' => $student_id,
                    'q_no' => $i,
                    'answer' => $answer,
                    'status' => $status,

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
        $exam_answer = DB::table('exam_answer')->where('test_id', base64_decode($test_id))->where('student_id', auth()->user()->id)->first();
        if ($exam_answer) {
            abort(403, 'You have already attempted this test');
        }
        return view('student.instruction', compact('test_id'));
    }
    function student_exam(Request $request, $test_id)
    {
        $exam = Exam::findOrFail(base64_decode($test_id));
        $second = now()->diffInSeconds(Carbon::parse($exam->end_at), false);

        $exam_answer = DB::table('exam_answer')
            ->where('test_id', base64_decode($test_id))
            ->where('student_id', auth()->user()->id)
            ->first();

        if ($second < 0) {
            abort(404);
        }

        return view('student.exam', compact('exam', 'second'));
    }


    public function clearLog(Request $request)
    {
        DB::table('clear_log')->insert([
            'student_id' => auth()->user()->id,
            'test_id' => $request->input('test_id'),
            'q_no' => $request->input('q_no'),
            'action' => 'clear',
        ]);

        return response()->json(['message' => 'Log cleared successfully']);
    }
    
    
    public function enable(Request $request)
    {
        $tests = Exam::where('end_at', '>', Carbon::now())->get();
        $students = collect();
        $testId = $request->input('test_id');

        if ($testId) {
            $students = DB::table('student')  // Corrected table name
            ->join('exam_answer', 'student.id', '=', 'exam_answer.student_id')
            ->where('exam_answer.test_id', $testId)
            ->distinct()
            ->select('student.id', 'student.user_name')
            ->get();

            // If it's an AJAX request, return students as JSON
            if ($request->ajax()) {
                return response()->json($students);
            }
        }

        return view('exam.enable', compact('tests', 'students', 'testId'));
    }

    public function enableExam(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:exam,id',
            'student_id' => 'required|exists:student,id',
        ]);
    
        $studentId = $request->student_id;
        $testId = $request->test_id;
    
        // Delete previous exam answers for the student if any
        DB::table('exam_answer')
            ->where('student_id', $studentId)
            ->where('test_id', $testId)
            ->delete();
    
        return redirect()->back()->with('success', 'Exam enabled successfully!');
    }


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
