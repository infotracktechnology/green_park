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
            ->where('ea.student_id', '>', 0)
            ->groupBy('e.id')
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
        $request->validate([
            'id' => 'required|unique:exam,id',
        ]);

        $data = $request->except(['physics_files', 'chemistry_files', 'botany_files', 'zoology_files']);
        $data['subject_name'] = implode(',', $request->subject_name);
        $data['branch_id'] = implode(',', $request->branch_id);
        $data['coaching_type'] = implode(',', $request->coaching_type);
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

    public function edit(Request $request, Exam $exam)
    {
        $branches = Branch::all();
        return view('exam.edit', compact('exam', 'branches'));
    }


    public function update(Request $request, Exam $exam)
    {
        $data = $request->all();
        $data['branch_id'] = implode(',', $request->branch_id);
        $data['coaching_type'] = implode(',', $request->coaching_type);
        $exam->update($data);
        session()->flash('success', 'Test updated successfully');
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

        $exam_answers =  DB::table('exam_answer')->where('test_id', $request->test_id)->where('student_id', $student_id)->get()->keyBy('q_no');

        for ($i = 1; $i <= $request->total_question; $i++) {
            $status = $request->status[$i] ?? null;
            $subject = $request->subject[$i] ?? null;
            $answer = $status == 'que-save' || $status == 'que-save-mark' ? $request->question[$i] : 0;

            $data = ['test_id' => $request->test_id,'student_id' => $student_id,'subject' => $subject,'q_no' => $i,
            'answer' => $answer,'status' => $status];

            if(!isset($exam_answers[$i])){ 
                DB::table('exam_answer')->insert($data);
            }

        }
        

     return $student_id ? redirect()->route('studentdashboard')->with('success', 'Exam submitted successfully') : to_route('exam.index');
    }




    function destroy(Request $request, Exam $exam)
    {  
        foreach ($exam->questions as $key => $question) {
            unlink($question['image']);
        }
        $exam->delete();
        DB::table('exam_answer')->where('test_id', $exam->id)->delete();
        session()->flash('success', 'Test deleted successfully');
        return to_route('exam.index');
    }

    function student_instruction(Request $request, $test_id)
    {
        $exam = Exam::findOrFail(base64_decode($test_id));
        $exam_answer = DB::table('exam_answer')->where('test_id', base64_decode($test_id))->where('student_id', auth()->user()->id)->selectRaw('count(q_no) as total_question')->first();
        if ($exam_answer && $exam_answer->total_question >= $exam->total_questions) {
            return redirect()->route('studentdashboard')->with('error', 'You have already attempted this Exam!');
        }
        
        return view('student.instruction', compact('test_id'));
    }
    function student_exam(Request $request, $test_id)
{
    $exam = Exam::findOrFail(base64_decode($test_id));
    $answers = DB::table('exam_answer')->where('test_id', base64_decode($test_id))->where('student_id', auth()->user()->id)->orderBy('updated_at', 'desc')->get();
    $maxQuestions = $answers->first()->q_no ?? 0;
    $answers = $answers->keyBy('q_no');
    $second = now()->diffInSeconds(Carbon::parse($exam->end_at), false);

    if ($second < 0) {
       return redirect()->route('studentdashboard')->with('error', 'Exam Time is up!');
    }

    return view('student.exam', compact('exam', 'second', 'answers', 'maxQuestions'));
}
 
    function Save(Request $request){
        $data = ['test_id' => $request->test_id,'student_id' => auth()->user()->id,'subject' => $request->subject,'q_no' => $request->q_no,'answer' => $request->answer,'status' => $request->status];
        $answer = DB::table('exam_answer')->where('test_id', $request->test_id)->where('student_id', auth()->user()->id)->where('q_no', $request->q_no)->first();
        if($answer){
            DB::table('exam_answer')->where('test_id', $request->test_id)->where('student_id', auth()->user()->id)->where('q_no', $request->q_no)->update($data);
        }else{
            DB::table('exam_answer')->insert($data);
        }

        return response()->json(['message' => 'Answer saved successfully']);
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

    public function offlineUpload(Request $request, ImportController $import)
    {
    
        $request->validate([
            'offline' => 'required|mimes:csv,txt|max:1024',
        ]);

        $answers = $import->parseCSV($request->file('offline')->getRealPath());
         if (!isset($answers[0]['test_id']) && !isset($answers[0]['student_id'])) {
            return back()->with('error', 'File is not in the template format.');
        }
        
        foreach ($answers as $answer) {
        $exam = Exam::find($answer['test_id']);

        if (!$exam) {
            return back()->with('error', 'Exam with ID ' . $answer['test_id'] . ' not found.');
        }

        // if($answer['MODE'] != "OMR"){
        //     continue;
        // }

        $total_questions = $exam->total_questions;

        $phy_start = is_null($exam->phy_start) ? 0 : $exam->phy_start;
        $chem_start = is_null($exam->chem_start) ? 0 : $exam->chem_start;
        $bot_start = is_null($exam->bot_start) ? 0 : $exam->bot_start;
        $zoo_start = is_null($exam->zoo_start) ? 0 : $exam->zoo_start;

        $phy_end = is_null($exam->phy_end) ? 0 : $exam->phy_end;
        $chem_end = is_null($exam->chem_end) ? 0 : $exam->chem_end;
        $bot_end = is_null($exam->bot_end) ? 0 : $exam->bot_end;
        $zoo_end = is_null($exam->zoo_end) ? 0 : $exam->zoo_end;

            for ($i = 1; $i <= $total_questions; $i++) {
                $q_no = $answer["Q$i"] ?? 0;
                $subject = $this->determineSubject($i, $phy_start, $phy_end, $chem_start, $chem_end, $bot_start, $bot_end, $zoo_start, $zoo_end);
                DB::table('exam_answer')->insert([
                    'student_id' => $answer['student_id'],
                    'test_id' => $answer['test_id'],
                    'q_no' => $i,
                    'subject' => $subject,
                    'answer' => $q_no,
                    'mode' => "OMR",
                ]);
            
            }
        }

        return back()->with('success', 'File uploaded successfully.');
    }

    public function determineSubject($question, $phyStart, $phyEnd, $chemStart, $chemEnd, $botStart, $botEnd, $zooStart, $zooEnd){

        if ($phyStart <= $question && $question <= $phyEnd && $phyEnd != 0) {
            return 'physics';
        } elseif ($chemStart <= $question && $question <= $chemEnd && $chemEnd != 0) {
            return 'chemistry';
        } elseif ($botStart <= $question && $question <= $botEnd && $botEnd != 0) {
            return 'botany';
        } elseif ($zooStart <= $question && $question <= $zooEnd && $zooEnd != 0) {
            return 'zoology';
        } else {
            return 'NULL';
        }
    }

    public function answerKey()
    {
        $answerkey_logs = DB::table('key_log')->where('type', 'answer_key')->latest()->take(10)->get();
        return view('exam.answerkey', compact('answerkey_logs'));
    }

    public function uploadAnswerKey(Request $request, ImportController $import)
    {
       
        $request->validate([
            'answer_key' => 'required|max:1024',
        ]);

        $answers = $import->parseCSV($request->file('answer_key')->getRealPath());
     if (!isset($answers[0]['test_id'])) {
            return back()->with('error', 'File is not in the correct format.');
        }
    
       
        $originalFileName = $request->file('answer_key')->getClientOriginalName();
        $uploadTime = Carbon::now()->format('Y-m-d H:i:s');
        foreach ($answers as $answer) {
           $exam_answers = DB::table('exam_answer')->where('test_id', $answer['test_id'])->where('answer', '>', 0)->get();
        foreach ($exam_answers as $row) {
                $ans = $answer["a$row->q_no"];
                $ans_key = explode('|', $ans);
                $mark = in_array($row->answer, $ans_key) && array_sum($ans_key) > 0 ? 4 : -1;
           DB::table('exam_answer')->where('id', $row->id)->update(['mark' => $mark, 'answer_key' => $ans]);
            }
        }
        $filename = date('Y-m-d H-i-s').$originalFileName;
        $request->answer_key->move('answer_key',$filename);
        $path = 'answer_key/'.$filename;

        DB::table('key_log')->insert([
                'file_name' => $originalFileName,
                'upload_time' => $uploadTime,
                'test_name' => implode(',', array_unique(array_column($answers, 'test_name'))),
                'path' => $path,
                'test_id' => implode(',', array_unique(array_column($answers, 'test_id'))),
                'type' => 'answer_key',
        ]);

     return redirect()->back()->with('success', 'Answer key uploaded successfully.');
    }

    function Dump_Report(Request $request){
        $testId = $request->test_id ?? 0;
        $tests = Exam::all();
        $results = DB::select("SELECT test_id,student_id,mode as stmode,GROUP_CONCAT(DISTINCT subject)subjects,sum(mark)mark,b.student_name,c.name,b.coaching_type,b.gender,b.section FROM `exam_answer` a join student b on a.student_id=b.id join branch c on b.campus=c.id where test_id=$testId group by student_id order by mark desc");
        return view('exam.dump_report',compact('testId','results','tests'));
    }

    function deleteAnswerKey($id,$test_id){
        DB::table('key_log')->where('id', $id)->delete();
        return redirect()->route('exam.answerkey')->with('success', 'Answer key log deleted successfully.');
    }


}
