<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamAnswer;
use App\Models\ExamSubjectReport;
use App\Models\Options;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ImportController;

class ExamController extends Controller
{

    public function index(Request $request)
    {
        $tests = Exam::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, function ($query) {
                $query->where('branch_id', 'like', '%' . auth()->user()->branch . '%');
            })->latest()->get();

        if ($request->has('test_id')) {
            $test = Exam::find($request->test_id);
            $test->update([
                'start_at' => Carbon::parse($request->start_at),
                'end_at' => Carbon::parse($request->end_at),
                'status' => 'scheduled',
                'duration' => Carbon::parse($request->end_at)->diffInSeconds($request->start_at),
            ]);
            return to_route('exam.index')->with('success', 'Test Scheduled Successfully');
        }

        return view('exam.index', compact('tests'));
    }

    public function create()
    {
        $testcategory = Options::where('type', 'testcategory')->first()->value;
        return view('exam.create', compact('testcategory'));
    }


    public function store(Request $request)
    {
        $data = $request->except(['physics_files', 'chemistry_files', 'botany_files', 'zoology_files', 'maths_files']);
        foreach (['coaching_type', 'branch', 'category', 'batch', 'subject_name'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }
        $data['status'] = 'preview';
        try {
            $questions = [];
            $q_no = 1;
            foreach (['physics', 'chemistry', 'botany', 'zoology', 'maths'] as $subject) {
                if ($request->hasFile($subject . "_files")) {
                    foreach ($request->file($subject . "_files") as $key => $file) {
                        $filename = $data['name'] . "-" . $q_no . '.' . $file->getClientOriginalExtension();
                        $file->move('questions', $filename);
                        $questions[] = ['subject' => strtoupper($subject), 'image' => "questions/".$filename];
                        $q_no++;
                    }
                }
            }

            $data['questions'] = $questions;
            Exam::create($data);
        } catch (\Exception $e) {
            return to_route('exam.index')->with('error', $e->getMessage());
        }
        return to_route('exam.index')->with('success', 'Test Created Successfully');
    }

    public function edit(Request $request, Exam $exam)
    {
        $type = Student::StudentFilterQuery($exam->branch, $exam->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($exam->branch, $exam->course, $exam->type, $exam->category, $exam->batch, $exam->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($exam->branch, $exam->course, $exam->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        $testcategory = Options::where('type', 'testcategory')->first()->value;

        return view('exam.edit', compact('exam', 'type', 'section', 'students', 'testcategory'));
    }


    public function update(Request $request, Exam $exam)
    {
        $data = $request->all();
        if ($request->hasFile('images')) {
            $questions = $exam->questions;
            foreach ($request->file('images') as $key => $file) {
                $q_no = (int)$request->q_no[$key];
                $filename = $exam->name . '-' . $q_no . '.' . $file->getClientOriginalExtension();
                $file->move('questions', $filename);
                $questions[$q_no - 1]['image'] = "questions/" . $filename;
                $exam->update(['questions' => $questions]);
            }
            return redirect()->back()->with('success', 'Questions Images Replaced Successfully');
        }

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }
        $exam->update($data);
        return to_route('exam.index')->with('success', 'Exam updated successfully');
    }
    public function TestCategory(Request $request)
    {
        $category = Options::where('type', 'testcategory')->first()->value;
        if($request->isMethod('POST')) {
            array_push($category, $request->category);
            $update = Options::where('type', 'testcategory')->update(['value' => $category]);
            return redirect()->back()->with('success', 'Category Added Successfully');
        }
        if($request->isMethod("DELETE")) {
            $category = array_diff($category, [$request->category]);
            $update = Options::where('type', 'testcategory')->update(['value' => $category]);
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        }
        return view('exam.testcategory', compact('category'));
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

        $exam_answers =  ExamAnswer::where('testname',$request->testname)->where('student_id', $student_id)->get()->keyBy('q_no');

        for ($i = 1; $i <= $request->total_question; $i++) {
            $status = $request->status[$i] ?? null;
            $subject = $request->subject[$i] ?? null;
            $answer = $request->question[$i] ?? 0;

            $data = [
                'test_id' => $request->test_id,
                'testname' => $request->testname,
                'student_id' => $student_id,
                'subject' => $subject,
                'q_no' => $i,
                'answer' => ($status == 'que-save' || $status == 'que-save-mark') ? $answer : 0,
                'status' => $status,
                'academic_year' => $this->academic_year
            ];


            if (!isset($exam_answers[$i])) {
                ExamAnswer::insert($data);
            } else {
                ExamAnswer::where('test_id', $request->test_id)->where('student_id', $student_id)->where('q_no', $i)->update($data);
            }
        }

        return $student_id ? redirect()->route('studentdashboard')->with('success', 'Exam submitted successfully') : to_route('exam.index');
    }


    function destroy(Request $request, $id = null)
    {
        if ($request->has('ids')) {
            $exams = Exam::whereIn('id', $request->ids)->get();
            foreach ($exams as $exam) {
                foreach ($exam->questions as $key => $question) {
                    if (file_exists($question['image'])) unlink($question['image']);
                }
                $exam->delete();
            }
        }
        return redirect()->back()->with('success', 'Exams deleted successfully');
    }

    function student_instruction(Request $request, $test_id)
    {
        $exam = Exam::findOrFail(base64_decode($test_id));
        $exam_answer = ExamAnswer::where('test_id', base64_decode($test_id))->where('student_id', auth()->user()->student_id)->selectRaw('count(q_no) as total_question')->first();
        if ($exam_answer && $exam_answer->total_question >= $exam->total_questions) {
            return redirect()->route('studentdashboard')->with('error', 'You have already attempted this Exam!');
        }

        return view('student.instruction', compact('test_id'));
    }
    function student_exam(Request $request, $test_id)
    {
        $exam = Exam::findOrFail(base64_decode($test_id));
        $answers = ExamAnswer::where('testname', $exam->name)->where('student_id', auth()->user()->student_id)->orderBy('updated_at', 'desc')->get();
        $maxQuestions = $answers->first()->q_no ?? 0;
        $answers = $answers->keyBy('q_no');
        $second = now()->diffInSeconds(Carbon::parse($exam->end_at), false);

        if ($second < 0) {
            return redirect()->route('studentdashboard')->with('error', 'Exam Time is up!');
        }

        return view('student.exam', compact('exam', 'second', 'answers', 'maxQuestions'));
    }

    function Save(Request $request)
    {
        $data = ['test_id' => $request->test_id, 'student_id' => auth()->user()->student_id, 'subject' => $request->subject, 'q_no' => $request->q_no, 'answer' => $request->answer, 'status' => $request->status, 'academic_year' => $this->academic_year, 'testname' => $request->testname];
        $answer = ExamAnswer::where('test_id', $request->test_id)->where('student_id', auth()->user()->student_id)->where('q_no', $request->q_no)->first();
        if ($answer) {
            ExamAnswer::where('test_id', $request->test_id)->where('student_id', auth()->user()->student_id)->where('q_no', $request->q_no)->update($data);
        } else {
            ExamAnswer::create($data);
        }

        return response()->json(['message' => 'Answer saved successfully']);
    }

    public function clearLog(Request $request)
    {
        DB::table('clear_log')->insert([
            'student_id' => auth()->user()->student_id,
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
        $studentId = $request->student_id;
        $testId = $request->test_id;

        ExamAnswer::where('student_id', $studentId)->where('test_id', $testId)->delete();

        return redirect()->back()->with('success', 'Exam enabled successfully!');
    }

    public function OnlineResponse(Request $request)
    {
        $category = Options::where('type', 'testcategory')->first()->value ?? [];
        $exams = [];

        if ($request->has('testcategory')) {
            $exams = Exam::where('testcategory', $request->testcategory)->where("academic_year", $this->academic_year)->groupBy('name')->get();
        }

        return view('exam.onlineresponse', compact('category', 'exams'));
    }


    public function OnlineResponseDownload(Request $request)
    {
        $examname = $request->examname;
        $reportData = DB::table('exam_answer as ea')->join('exam as e', 'e.testid', '=', 'ea.test_id')->join('student as s', 's.student_id', '=', 'ea.student_id')->select('s.coaching_type', 's.user_name as username', 's.student_name', 's.section', 'ea.student_id', 'ea.test_id', 'e.name as exam_name', DB::raw('DATE_FORMAT(e.start_at, "%Y-%m-%d") as exam_date'), 'ea.q_no', 'ea.answer')->where('e.name', $examname)->where('ea.mode', '!=', 'OMR')->get()->groupBy('student_id');

        $headers = [
            'Coaching Type',
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

        $filename = "OnlineResponse_".$examname.".csv";
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
        $offline_logs = DB::table('key_log')->where('type', 'offline_key')->latest()->take(10)->get();
        return view('exam.offline', compact('offline_logs'));
    }

    public function offlineUpload(Request $request, ImportController $import)
    {
        $request->validate([
            'offline' => 'required|mimes:csv,txt|max:4096',
        ]);

        $answers = $import->parseCSV($request->file('offline')->getRealPath());

        if (empty($answers) || !isset($answers[0]['test_id'], $answers[0]['student_id'])) {
            return back()->with('error', 'File is not in the template format.');
        }

        foreach ($answers as $answer) {
            $exam = Exam::where('academic_year', $this->academic_year)->where('testid', $answer['test_id'])->first();

            if (!$exam) {
                return back()->with('error', "Exam with ID {$answer['test_id']} not found.");
            }

            $exists = ExamAnswer::where('test_id', $answer['test_id'])->where('student_id', $answer['student_id'])->exists();

            if ($exists) {
                continue;
            }

            $record = [];

            for ($i = 1; $i <= $exam->total_questions; $i++) {
                $subject = $this->determineSubject($i, $exam->phy_start, $exam->phy_end, $exam->chem_start, $exam->chem_end, $exam->bot_start, $exam->bot_end, $exam->zoo_start, $exam->zoo_end);
                $record[] = ['academic_year' => $this->academic_year, 'test_id' => $answer['test_id'], 'testname' => $exam->name, 'student_id' => $answer['student_id'], 'subject' => $subject, 'q_no' => $i, 'answer' => $answer["q$i"] ?? null, 'mode' => 'OMR'];
            }
            $exam_answer = ExamAnswer::insert($record);
        }

        $file = $request->file('offline');
        $originalName = $file->getClientOriginalName();
        $filename = now()->format('Y-m-d H-i-s').'-'.$originalName;
        $file->move('answer_key', $filename);

        DB::table('key_log')->insert([
            'file_name' => $originalName,
            'upload_time' => now(),
            'test_name' => implode(',', array_unique(array_column($answers, 'exam name'))),
            'path' => 'answer_key/'.$filename,
            'test_id' => implode(',', array_unique(array_column($answers, 'test_id'))),
            'no_rows' => count($answers),
            'type' => 'offline_key',
        ]);

        return back()->with('success', 'Offline file uploaded successfully.');
    }

    public function determineSubject($question, $phyStart, $phyEnd, $chemStart, $chemEnd, $botStart, $botEnd, $zooStart, $zooEnd)
    {
        return match (true) {
            $phyStart && $question >= $phyStart && $question <= $phyEnd => 'PHYSICS',
            $chemStart && $question >= $chemStart && $question <= $chemEnd => 'CHEMISTRY',
            $botStart && $question >= $botStart && $question <= $botEnd => 'BOTANY',
            $zooStart && $question >= $zooStart && $question <= $zooEnd => 'ZOOLOGY',
            default => NULL,
        };
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

        try {
            $file = $request->file('answer_key');
            $answers = $import->parseCSV($file->getRealPath());

            if (empty($answers) || empty($answers[0]['test_id'])) {
                return back()->with('error', 'File is not in the correct format.');
            }

            $uniqueTests = [];
            $uploadTime = now()->format('Y-m-d H:i:s');

            foreach ($answers as $answer) {
                $testId = $answer['test_id'];
                $uniqueTests[$testId] = $answer['test_name'] ?? '';

                ExamAnswer::where('test_id', $testId)->where('academic_year', $this->academic_year)->orderBy('id')
                    ->chunk(20000, function ($rows) use ($answer) {
                        $bulkData = [];
                        foreach ($rows as $row) {
                            $key = 'a'.$row->q_no;
                            $ans = $answer[$key] ?? '';
                            $ansKey = array_filter(explode('|', $ans));
                            $answerKey = count($ansKey) ? $ans : 'DEL';
                            $mark = 0;

                            if($answerKey === 'DEL') {
                               $mark = null;
                            }

                            if (count($ansKey) && $row->answer) {
                                $mark = in_array($row->answer, $ansKey) ? 4 : -1;
                            }

                         $bulkData[] = [ 'id' => $row->id,'answer_key' => $answerKey,'mark' => $mark];
                        }

                        $this->executeBatchUpdate($bulkData);
                    });
            }

            $filename = now()->format('Y-m-d_H-i-s').'_'.$file->getClientOriginalName();
            $file->move('answer_key', $filename);

            DB::table('key_log')->insert([
                'file_name'  => $file->getClientOriginalName(),
                'upload_time' => $uploadTime,
                'test_name'  => implode(',', array_unique($uniqueTests)),
                'test_id'    => implode(',', array_keys($uniqueTests)),
                'path'       => 'answer_key/'.$filename,
                'no_rows'    => count($answers),
                'type'       => 'answer_key',
            ]);

            return back()->with('success', "Answer key validated successfully.");
        } catch (\Throwable $e) {
            \Log::error('Answer Key Upload Error', ['error' => $e->getMessage()]);
            return back()->with('error', 'An error occurred during upload. Check logs for details.');
        }
    }


    private function executeBatchUpdate(array $bulkData): void
    {
        $batchSize = 500;

        foreach (array_chunk($bulkData, $batchSize) as $chunk) {
            DB::table('exam_answer')->upsert(
                $chunk,
                ['id'],
                ['answer_key', 'mark']
            );
        }
    }


    public function deleteAnswerKey($id, $test_id)
    {
        DB::table('key_log')->where('id', $id)->delete();
        return redirect()->route('exam.answerkey')->with('success', 'Answer key log deleted successfully.');
    }

    public function deleteOfflineKey($id, $test_id)
    {
        DB::table('key_log')->where('id', $id)->delete();
        DB::table('exam_answer')->where('test_id', $test_id)->where('academic_year', $this->academic_year)->where('mode', 'OMR')->delete();
        return redirect()->route('exam.offline.index')->with('success', 'Answer key log deleted successfully.');
    }


    public function Publish(Request $request)
    {
        $exams = [];
        if ($request->start_date && $request->end_date) {
            $exams = Exam::whereBetween('exam_date', [$request->start_date, $request->end_date])->selectRaw("group_concat(testid) as testid,name,testcategory,total_questions,publish,markrange_file")->groupBy('name')->get();
        }
        if ($request->delete && $request->batch) {
            $exam = Exam::where('name', $request->delete)->where('academic_year', $this->academic_year)->first();
            $markrange_file = $exam->markrange_file;
            if (file_exists($markrange_file[$request->batch])) {
                unlink($markrange_file[$request->batch]);
            }
            unset($markrange_file[$request->batch]);
            Exam::where('name', $request->delete)->where('academic_year', $this->academic_year)->update(['markrange_file' => $markrange_file]);
            return redirect()->back()->with('success', "Markrange File Deleted Successfully.");
        }

        if ($request->isMethod('post')) {
            foreach ($request->publish as $name => $publish) {
                $exam['publish'] = $publish ?? 'No';
                $batch = Student::select('batch')->whereNotNull('batch')->where('batch', '!=', '')->distinct()->get()->pluck('batch');
                foreach ($batch as $b) {
                    if ($request->hasFile("batch.$name.$b")) {
                        $file = $request->file("batch.$name.$b");
                        $file->move('assets/markrange', $file->getClientOriginalName());
                        $exam['markrange_file'][$b] = 'assets/markrange/'.$file->getClientOriginalName();
                    }
                }
                Exam::where('name', $name)->where('academic_year', $this->academic_year)->update($exam);
                $this->MovePervious();
            }
            return redirect()->back()->with('success', "Exams Publish Updated Successfully.");
        }
        return view('exam.publish', compact('exams'));
    }
    public function MovePervious()
    {
        $sub = Exam::select('name')->groupBy('name')->orderByRaw('max(id) desc')->limit(3);
        $exams = Exam::leftJoinSub($sub, 't', fn($j) => $j->on('exam.name', '=', 't.name'))->whereNull('t.name')->select('exam.*')->get();

        foreach ($exams as $exam) {
            $subjects = array_map('strtolower', explode(',', $exam->subject_name));
            $expr = collect($subjects)->map(function ($s) {
                $sr = substr($s, 0, 1);
                return "sum(if(subject='$s' and mark=4,1,0)) as `{$sr}r`,sum(if(subject='$s' and mark=-1,1,0)) as `{$sr}w`,sum(if(subject='$s' and mark=0,1,0)) as `{$sr}l`,sum(if(subject='$s',mark,0)) as `{$sr}tot`";
            })->implode(',');
            $answers = ExamAnswer::join('student as s', 'exam_answer.student_id', '=', 's.student_id')->where('testname', $exam->name)->selectRaw("s.student_id as stuid,s.student_name as sname,s.batch,test_id as testid,s.section as sec,sum(mark) totmark,$expr")->groupBy('stuid')->get()->map(function ($answer) use ($exam) {
                return array_merge($answer->toArray(), ['category' => $exam->testcategory, 'subject' => $exam->name, 'exdate' => date('d-m-Y', strtotime($exam->exam_date))]);
            })->toArray();
            foreach (array_chunk($answers, 500) as $row) {
                ExamSubjectReport::insert($row);
            }
            Exam::where('name', $exam->name)->delete();
            ExamAnswer::where('testname', $exam->name)->delete();
        }
    }

    public function PerviousExamResult(Request $request)
    {
        $exams = collect();
        $headers = [];
        $category = ExamSubjectReport::where('category', '!=', '')->pluck('category')->unique();
        $exam = [];
        if ($request->testcategory) {
            $exam = ExamSubjectReport::where('subject', 'like', "%{$request->testcategory}%")->select('subject')->distinct()->orderByRaw("STR_TO_DATE(exdate, '%d-%m-%Y') desc")->get();
        }

        if ($request->testcategory && $request->examname) {
            $exams = ExamSubjectReport::where('subject', $request->examname)->get();
        }

        return view('exam.perviousexamresult', compact('exams', 'category', 'exam'));
    }

    public function PreviousExamUpload(Request $request, ImportController $import)
    {

        if ($request->isMethod('post')) {
            $rows = $import->parseCSV($request->file('perviousexamfile')->getRealPath());
            try {
                $chunks = array_chunk($rows, 500);
                foreach ($chunks as $chunk) {
                    ExamSubjectReport::insert($chunk);
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            return redirect()->back()->with('success', "Previous Exam Result Uploaded Successfully.");
        }

        return view('exam.previousexamupload');
    }
}
