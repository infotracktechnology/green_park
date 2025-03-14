<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Exam; // Import the Exam model
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Announcement;



class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request)
    {
        $students = DB::table('student')
            ->join('branch', 'student.campus', '=', 'branch.id')
            ->select('student.*', 'branch.name as campus')
            ->get();
        return view('student.index', compact('students'));
    }


    public function create(Request $request)
    {
        $branches = DB::table('branch')->select('id', 'name')->get();
        if ($request->has('city')) {
            $pincodes = DB::table('district_list')->where('District', $request->city)->select('Pincode')->get();
            return response()->json($pincodes);
        }
        return view('student.create', compact('branches'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'student_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'father_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'mother_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'ph_no1' => ['unique:student,ph_no1', 'numeric', 'regex:/^[6-9]\d{9}$/'],
            'ph_no2' => ['unique:student,ph_no2', 'numeric', 'regex:/^[6-9]\d{9}$/'],
            'father_ph_no' => ['unique:student,father_ph_no', 'numeric', 'regex:/^[6-9]\d{9}$/'],
            // 'mother_ph_no' => ['unique:student,mother_ph_no', 'numeric', 'regex:/^[6-9]\d{9}$/'],
        ]);

        Student::create($request->all());

        return redirect()->route('student.index')->with('success', 'Student created successfully.');
    }


    public function edit(Request $request, Student $Student)
    {
        $branches = DB::table('branch')->select('id', 'name')->get();
        $districts = DB::table('district_list')->where('State', $Student->state)->distinct()->orderBy('District')->get();
        $states = DB::table('district_list')->select('State')->distinct()->orderBy('State')->get();
        $pincodes = DB::table('district_list')->where('District', $Student->district)->select('Pincode')->get();

        return view('student.edit', compact('branches',  'districts', 'states', 'pincodes', 'Student'));
    }


    public function update(Request $request, Student $student)
    {
        $data = $request->all();
        $data['hostel_dayscholar'] = $data['hostel_dayscholar'] ?? null;
        $data['ac_nonac'] = $data['ac_nonac'] ?? null;
        $data['password']  = bcrypt($request->password_1);

        $student->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('student.index')->with('success', 'Student details successfully updated.');
    }


    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('student.index')->with('success', 'Student deleted successfully.');
    }


    public function section()
    {
        $students = DB::table('student')
            ->join('branch', 'student.campus', '=', 'branch.id')
            ->select('student.*', 'branch.name as campus')
            ->get();

        return view('student.section', compact('students'));
    }


    public function update_section(Request $request)
    {
        if ($request->student_ids) {
            foreach ($request->student_ids as $student_id) {
                $student = Student::find($student_id);
                if ($student) {
                    $student->section = $request->section;
                    $student->save();
                }
            }
        }

        return redirect()->route('section.student')->with('success', 'Student details successfully updated.');
    }
    public function profile()
    {
        return view('student.profile');
    }
    public function home()
    {
        return view('student.home');
    }


   
public function dashboard()
{
    $coachingType = auth()->user()->coaching_type;
    $branchId = auth()->user()->branch_id;

    $exam = Exam::where('start_at', '>', now())
        ->where('coaching_type', 'LIKE', "%$coachingType%")
        ->where('branch_id', 'LIKE', "%$branchId%")
        ->orderBy('start_at', 'asc')
        ->first();

    $examStartTime = $exam ? $exam->start_at->toIso8601String() : null; 

    return view('dashboards.studentdashboard', compact('examStartTime'));
}
    function marksheet(Request $request){
        $sid = auth()->user()->id;
        $tests = DB::select("SELECT DATE_FORMAT(b.start_at, '%d-%m-%Y')exam_date,b.name,test_id,sum(mark)mark,(count(q_no)*4)total FROM `exam_answer` a join exam b on a.test_id=b.id where student_id=$sid and b.publish='Yes' group by test_id order by b.updated_at desc limit 5");
       return view('student.marksheet',compact('tests'));
    }

    function mark_subject(Request $request, $test_id){
        $sid = auth()->user()->id;
        $test = DB::select("SELECT sum(mark=4)r,sum(mark=-1)w,sum(mark=0)l,sum(mark)tot,(count(q_no)*4)total,subject FROM `exam_answer` where test_id=$test_id and student_id=$sid group by subject");
        return view('student.mark_subject',compact('test'));
        
    }
    function mark_download(Request $request, $test_id){
        $sid = auth()->user()->id;
        $answers = DB::table('exam_answer')->where('test_id', $test_id)->where('student_id', $sid)->orderBy('q_no')->get();
        $answers = $answers->chunk(45);
        $exam = DB::table('exam')->where('id', $test_id)->selectRaw("name,id,DATE_FORMAT(start_at, '%d-%m-%Y')exam_date")->first();
        return view('student.mark_download',compact('answers','exam'));
    }



    public function getExamStartTime()
    {
        $exam = Exam::latest()->first(); // Get the latest exam (modify as needed)
        
        if (!$exam || !$exam->start_at) {
            return response()->json(['error' => 'Exam start time not found'], 404);
        }

        return response()->json([
            'start_at' => Carbon::parse($exam->start_at)->toISOString() // Convert to JS format
        ]);
    }



}



