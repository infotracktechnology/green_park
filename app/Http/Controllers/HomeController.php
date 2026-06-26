<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Chairmanvideo;
use App\Models\Options;
use App\Models\Student;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\FcmServiceProvider;
use App\Models\Staff;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\ParentConcern;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('academic_year')) {
            session()->put('academic_year', $request->academic_year);
            return redirect()->back()->with('success', 'Academic year changed successfully.');
        }

        $branchId = auth()->user()->branch;
        $today = date('Y-m-d');
        $academic_years = AcademicYear::all();
        $active_year = $this->academic_year;

        $data = Branch::with(['student' => function ($query) {
            $query->where('academic_year', $this->academic_year);
        }, 'attendance' => function ($query) use ($today) {
            $query->where('attendance_date', $today)
                ->whereIn('student_id', function ($q) {
                    $q->select('student_id')->from('student')->where('academic_year', $this->academic_year);
                });
        }])->when($branchId, fn($q) => $q->whereIn('id', explode(',', $branchId)))->get();

        $students = Student::where('academic_year', $this->academic_year)->when($branchId, fn($q) => $q->whereIn('campus', explode(',', $branchId)))->get();

        $boys = $students->filter(fn($student) => strtoupper(trim($student->gender)) == 'MALE')->count();
        $girls = $students->filter(fn($student) => strtoupper(trim($student->gender)) == 'FEMALE')->count();
        $total = $students->count();

        $present = Attendance::when($branchId, fn($q) => $q->whereIn('branch_id', explode(',', $branchId)))
            ->where('status', 'P')
            ->where('attendance_date', $today)
            ->whereIn('student_id', function ($q) {
                $q->select('student_id')->from('student')->where('academic_year', $this->academic_year);
            })
            ->distinct('student_id')
            ->count();

        $staffs = collect(Staff::select('department')->when($branchId, fn($q) => $q->whereIn('branch_id', explode(',', $branchId)))->get())->groupBy('department');

        $concerns = ParentConcern::all();

        $announcement = $data->map(fn($item) => [
            'branch' => $item->name,
            'count'  => Announcement::where('academic_year', $this->academic_year)->where('branch', 'like', "%{$item->id}%")->count()
        ]);

        $chairman = $data->map(fn($item) => [
            'branch' => $item->name,
            'count'  => Chairmanvideo::where('academic_year', $this->academic_year)->where('branch', 'like', "%{$item->id}%")->count()
        ]);

        return view('home', compact('data', 'boys', 'girls', 'total', 'staffs', 'present', 'concerns', 'announcement', 'chairman', 'academic_years', 'active_year'));
    }


    public function parent_concern(Request $request)
    {

        $parentconcerns = ParentConcern::where('status', '!=', 'Closed')->get();

        if ($request->isMethod('post')) {
            $parentconcern = ParentConcern::findOrFail($request->id);

            $updateData = ['status' => $request->status];
            if ($request->hasFile('file')) {
                if ($parentconcern->file && file_exists($parentconcern->file)) {
                    unlink($parentconcern->file);
                }
                $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
                $path = $request->file('file')->move('uploads/concern', $fileName);
                $updateData['file'] = $path;

                $updateData['remark'] = $request->remark;
            }
            $parentconcern->update($updateData);

            return redirect()->route('parent_concern')->with('success', 'Status updated successfully!');
        }

        return view('announcement.parent_concern', compact('parentconcerns'));
    }





    public function chat(Request $request)
    {
        $users =  \App\Models\User::where('id', '!=', auth()->user()->id)->get();

        if ($request->has('submit')) {
            $parentconcern = DB::table('parent_concern')->where('id', $request->id)->update(['status' => $request->status]);
            return redirect()->route('parent_concern')->with('success', 'Status updated successfully!');
        }

        return view('chat', compact('users'));
    }


    public function studentmenu_type(Request $request)
    {
        $menus = Options::where('type', 'student menu')->first();
        $menus = $menus->value ?? [];

        $types = $request->filled('branch')
            ? Student::where('campus', $request->branch)->distinct()->pluck('coaching_type')
            : collect();

        $menu_type = [];
        if ($request->filled('branch') && $request->filled('type')) {
            $menu_type = Options::where('type', "{$request->course}{$request->branch_name}{$request->type} menu")
                ->value('value') ?? [];
            $menu_type = collect($menu_type)->pluck('title')->toArray();
        }

        if ($request->has('assign')) {
            $menu = collect($request->fields)->map(fn($m) => json_decode($m, true))->toArray();
            Options::updateOrCreate(['type' => "{$request->course}{$request->branch_name}{$request->type} menu"], ['value' => $menu]);
            Student::where('course', $request->course)->where('campus', $request->branch)->where('coaching_type', $request->type)->update(['menu' => $menu]);
            return redirect()->route('studentmenu.type')->with('success', 'Menu updated successfully!');
        }

        return view('studentmenu.type', compact('menus', 'menu_type', 'types'));
    }
    public function studentmenu_student(Request $request)
    {
        $menus = Options::where('type', 'student menu')->first();
        $menus = $menus->value ?? [];
        $coachingtype = [];
        $menu_student = [];
        $students = [];

        if ($request->has('branch')) {
            $coachingtype = Student::where('campus', $request->branch)
                ->distinct()
                ->pluck('coaching_type')
                ->toArray();
        }

        if ($request->has('type')) {
            $students = Student::where('campus', $request->branch)
                ->where('coaching_type', $request->type)
                ->get();
        }

        if ($request->has('student')) {
            $student_record = Student::where('student_id', $request->student)->first();
            $menu_student = collect($student_record->menu ?? [])->map(function ($menu) {
                return $menu['title'] ?? '';
            })->toArray();
        }

        if ($request->has('assign')) {
            $menu = collect($request->fields ?? [])->map(function ($menu) {
                return json_decode($menu, true);
            })->toArray();

            Student::where('student_id', $request->student)->update(['menu' => $menu]);

            return redirect()->route('studentmenu.student', ['branch' => $request->branch,'type' => $request->type,'student' => $request->student])->with('success', 'Menu updated successfully!');
        }
        // Return view passing 'coachingtype'
        return view('studentmenu.student', compact('menus', 'menu_student', 'coachingtype', 'students'));
    }


    public function Filter(Request $request)
    {
        if ($request->has('gender')) {
            $section = Student::StudentFilterQuery($request->branch, $request->course, $request->type, $request->category, $request->batch, $request->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section');
            return response()->json($section);
        }

        if ($request->has('type')) {
            $students = Student::StudentFilterQuery($request->branch, $request->course, $request->type, null, null)->get()->pluck('student_name', 'student_id');
            return response()->json($students);
        }

        if ($request->has('branch')) {
            $type = Student::StudentFilterQuery($request->branch, $request->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type');
            return response()->json($type);
        }
    }

    public function ExaminationFilter(Request $request)
    {
        if ($request->has('testcategory')) {
            $exams = Exam::where('testcategory', $request->testcategory)->select('name')->distinct()->get()->pluck('name');
            return $request->ajax() ? response()->json($exams) : $exams;
        }
    }

    public function dashboardGender(Request $request)
    {
        $user = auth()->user();
        $query = Student::with('branch')->select('student_id', 'student_name', 'section', 'coaching_type', 'gender', 'campus')
            ->where('academic_year', $this->academic_year)
            ->where('campus', $request->campus);

        if ($request->has('section')) {
            if ($request->section == '-') {
                $query->where('section', '=', '');
            } else {
                $query->where('section', $request->section);
            }
        }
        if ($request->has('gender')) {
            if ($request->gender == 'all') {
            } else {
                $query->where('gender', $request->gender);
            }
        }

        $students = $query->get();


        return response()->json(['students' => $students]);
    }

    public function dashboardStaff(Request $request)
    {
        $user = auth()->user();
        $query = Staff::with('branch')->select('name', 'gender', 'department', 'school_initial', 'designation', 'branch_id')->when(auth()->user()->branch, function ($query) {
            $query->where('branch_id', auth()->user()->branch);
        });
        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        $staffs = $query->get();
        return response()->json(['staffs' => $staffs]);
    }

    public function dashboardAnnouncement(Request $request)
    {
        $start_date = $request->start_date ? Carbon::createFromFormat('Y-m-d H:i', $request->start_date)->format('Y-m-d H:i:s') : Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
        $end_date = $request->end_date ? Carbon::createFromFormat('Y-m-d H:i', $request->end_date)->endOfDay()->format('Y-m-d H:i:s') : Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        $query = Announcement::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, function ($query) {
                $query->whereRaw('FIND_IN_SET(?, branch)', [auth()->user()->branch]);
            });
        $announcements = $query->get();
        return response()->json(['announcements' => $announcements]);
    }

    public function Setting(Request $request)
    {
        $setting = Setting::where('academic_year', $this->academic_year)->get();
        $category = Options::where('type', 'testcategory')->first()->value ?? [];
        $documents = Options::where('type', 'Document Option')->first()->value ?? [];

        if ($request->isMethod('POST')) {
            $row = Setting::find($request->id)->update(['value' => $request->value]);
            return redirect()->back()->with('success', 'Setting new value updated successfully!');
        }

        return view('setting', compact('setting', 'category', 'documents'));
    }
}
