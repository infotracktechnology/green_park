<?php

namespace App\Http\Controllers;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Student;
use App\Providers\FcmServiceProvider;
use App\Http\Controllers\HomeController;

class AnnouncementController extends Controller
{

    public function index(Request $request)
    {
        $academic_years = AcademicYear::all();
        $branches = Branch::all();
        $branchList = DB::table('branch')->pluck('name', 'id')->toArray();
    
        $announcements = Announcement::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, function ($query) {
                $query->where('branch', 'like' , '%'.auth()->user()->branch.'%');
            })->get();
           
    
        return view('announcement.index', compact('announcements', 'branches', 'branchList', 'academic_years'));
    }
    
    public function create()
    {
       
        return view('announcement.create');
    }

public function store(Request $request,FcmServiceProvider $fcm)
{
   $data = $request->all();

//    foreach (['course','coaching_type','branch','category','section','batch'] as $field) {
//        $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
//    }

   $data['student_ids'] = [];
   if ($request->has('attachment')) {
        $fileName = time() . '.' . $request->attachment->extension();
        $request->attachment->move(public_path('assets/attachments'), $fileName);
        $data['attachment'] = 'assets/attachments/' . $fileName;
    } else {
        $data['attachment'] = null;
    }

    $announcement = Announcement::create($data);

    //  $students = Student::whereIn('coaching_type', $request->coaching_type)->whereIn('campus', $request->branch)->whereNotNull('device_token')->get()->map(function ($student) use ($fcm) {
    //     return $student->device_token;
    // })->toArray();

    // if(!empty($students)) {
    //     $fcm->sendMulticast($students,"Announcement", $request->title);
    // }

    return to_route('announcement.index')->with('success', 'Announcement created successfully.');
}

public function edit(Request $request, Announcement $announcement)
{
    $type = HomeController::StudentFilterQuery($announcement->branch,$announcement->course,null,null,null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

    $section = HomeController::StudentFilterQuery($announcement->branch,$announcement->course,$announcement->type,$announcement->category,$announcement->batch)->select('section')->distinct()->get()->pluck('section')->toArray();

    return view('announcement.edit', compact('announcement','type','section'));
}


public function update(Request $request, Announcement $announcement)
{
    $data = $request->all();

//    foreach (['course','coaching_type','branch','category','section','batch'] as $field) {
//        $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
//    }

   $data['student_ids'] = [];
   if ($request->has('attachment')) {
        $fileName = time() . '.' . $request->attachment->extension();
        $request->attachment->move(public_path('assets/attachments'), $fileName);
        $data['attachment'] = 'assets/attachments/' . $fileName;
    } else {
        $data['attachment'] = null;
    }

   $announcement->update($data);
    
    return redirect()->route('announcement.index')->with('success', 'Announcement details successfully updated.');
}


public function destroy($id)
{
    $announcement = Announcement::findOrFail($id);
    if ($announcement->attachment && file_exists(public_path($announcement->attachment))) {
        unlink(public_path($announcement->attachment));
    }

    $announcement->delete();

    return redirect()->route('announcement.index')->with('success', 'Announcement deleted successfully.');
}


    public function notification(Request $request)
    {
    $announcements = auth()->user()->announcement();

    return view('student.notification', compact('announcements'));
    }

    


}
