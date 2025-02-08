<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Exam;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ImportController;

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
                'status' => 'scheduled',
                'duration' => Carbon::parse($request->end_at)->diffInSeconds($request->start_at),
            ]);

            session()->flash('success', 'Test Scheduled successfully');
            return to_route('exam.index');
        }

        $exams = DB::table('exam as e')
            ->leftJoin('exam_answer as ea', 'e.id', '=', 'ea.test_id')
            ->select('e.id as exam_id', 'e.name', DB::raw('COUNT(DISTINCT ea.student_id) as student_count'))
            ->groupBy('e.id', 'e.name')
            ->get();

  
        $tests = $tests->map(function ($test) use ($exams) {
            $exam = $exams->firstWhere('exam_id', $test->id);
            $test->student_count = $exam ? $exam->student_count : 0;
            return $test;
        });

        return view('exam.index', compact('tests'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('exam.create', compact('branches'));
    }


    public function store(Request $request)
    {
        $data = $request->except(['physics_files', 'chemistry_files', 'botany_files', 'zoology_files']);
        $data['subject_name'] = implode(',', $request->subject_name);
        $data['status'] = 'preview';
    
        $questions = [];
    
        foreach (['physics', 'chemistry', 'botany', 'zoology'] as $subject) {
            if ($request->hasFile($subject."_files")) {
                foreach ($request->file($subject."_files") as $key => $file) {
                    $q_no = $key + 1;
                    $filename = $request->id.'-'.$subject.'-'.$q_no.'.'.$file->getClientOriginalExtension();
                    $file->move('questions', $filename);
                    $questions[] = ['subject' => $subject, 'image' => "questions/" . $filename];
                }
            }
        }
    
        $data['questions'] = $questions;
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
            $subject = $request->subject[$i] ?? null;
            $answer = $status == 'answer' || $status == 'answer & mark' ? $request->question[$i] : 0;

            DB::table('exam_answer')->insert([
                'test_id' => $request->test_id,
                'student_id' => $student_id,
                'subject' => $subject,
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
            $students = DB::table('student')
                ->join('exam_answer', 'student.id', '=', 'exam_answer.student_id')
                ->where('exam_answer.test_id', $testId)
                ->distinct()
                ->select('student.id', 'student.user_name')
                ->get();


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

    public function test()
    {
        $tests = Exam::all();
        return view('exam.test', compact('tests'));
    }


    public function downloadTestReport(Request $request)
    {
        $testId = $request->input('test_id');
        $reportData = DB::table('exam_answer as ea')
            ->join('exam as e', 'e.id', '=', 'ea.test_id')
            ->join('branch as b', 'b.id', '=', 'e.branch_id')
            ->join('student as s', 's.id', '=', 'ea.student_id')
            ->select(
                's.coaching_type',
                'b.name as branch_name',
                's.user_name as username',
                's.student_name',
                's.section',
                'ea.student_id',
                'ea.test_id',
                'e.name as exam_name',
                DB::raw('DATE_FORMAT(e.start_at, "%Y-%m-%d") as exam_date'), //Format date as string
                'ea.q_no',
                'ea.answer'
            )
            ->where('ea.test_id', $testId)
            ->get()
            ->groupBy('student_id');

        $headers = [
            'Coaching Type',
            'Branch Name',
            'Username',
            'Student Name',
            'Section',
            'Student ID',
            'Test ID',
            'Exam Name',
            'Exam Date',
        ];

        $maxQuestions = $reportData->flatten()->max('q_no');

        for ($i = 1; $i <= $maxQuestions; $i++) {
            $headers[] = "A{$i}";
        }

        $csvData = [$headers];

        foreach ($reportData as $studentId => $answers) {
            $student = $answers->first();
            $row = [
                $student->coaching_type,
                $student->branch_name,
                $student->username,
                $student->student_name,
                $student->section,
                $student->student_id,
                $student->test_id,
                $student->exam_name,
                $student->exam_date,
            ];

            for ($i = 1; $i <= $maxQuestions; $i++) {
                $row[] = $answers->firstWhere('q_no', $i)->answer ?? '';
            }

            $csvData[] = $row;
        }

        $filename = "test_report_{$testId}.csv";
        return response()->stream(function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $line) {
                fputcsv($file, $line);
            }
            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }



    public function offline()
    {
        return view('exam.offline');
    }

    public function offlineUpload(Request $request)
    {
    
        $request->validate([
            'offline' => 'required|mimes:csv|max:1024', // Only CSV files with a maximum size of 1 MB
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    public function answerKey()
    {
        return view('exam.answerkey');
    }

    public function uploadAnswerKey(Request $request,ImportController $import)
    {
        $request->validate([
            'answer_key' => 'required|max:1024', 
        ]);

        $answers = $import->parseCSV($request->file('answer_key')->getRealPath());
        dd($answers);
        //return back()->with('success', 'File uploaded successfully.');
    }


}
