<?php

namespace App\Http\Controllers;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;

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

    public function store(Request $request)
{
    $announcement = new Announcement();
    $announcement->academic_year = $request->academic_year;
    $announcement->branch = implode(',', $request->branch);
    $announcement->coaching_type = implode(',', $request->coaching_type);
    $announcement->gender = $request->gender;
    $announcement->title = $request->title;
    $announcement->content = $request->content;

    if ($request->has('attachment')) {
        $fileName = time() . '.' . $request->attachment->extension();
        $request->attachment->move(public_path('assets/attachments'), $fileName);
        $announcement->attachment = 'assets/attachments/' . $fileName;
    } else {
        $announcement->attachment = null;
    }
    $announcement->category = in_array('Offline', $request->coaching_type) ? $request->category : null;
    $announcement->save();

    return to_route('announcement.index')->with('success', 'Announcement created successfully.');
}

public function edit(Request $request, Announcement $announcement)
{
   
    $academicyear = AcademicYear::all(); 
    $selectedCoachingTypes = explode(',', $announcement->coaching_type);

    return view('announcement.edit', compact('announcement',  'academicyear', 'selectedCoachingTypes'));
}


public function update(Request $request, Announcement $announcement)
{
    $announcement->academic_year = $request->academic_year;
    $announcement->branch = implode(',', $request->branch);
    $announcement->coaching_type = implode(',', $request->coaching_type);
    $announcement->gender = $request->gender;
    $announcement->title = $request->title;
    $announcement->content = $request->content;

    if ($request->hasFile('attachment')) {
        if ($announcement->attachment && file_exists(public_path($announcement->attachment))) {
            unlink(public_path($announcement->attachment));
        }

        $file = $request->file('attachment');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/attachments'), $filename);
        $announcement->attachment = 'assets/attachments/' . $filename;
    }

    if (in_array('Offline', $request->coaching_type)) {
        $announcement->category = $request->category;
    } else {
        $announcement->category = null;
    }

    $announcement->save();

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
