<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AnnouncementController extends Controller
{

    public function index(Request $request)
    {
        $announcements = Announcement::all();
        return view('announcement.index', compact('announcements'));
    }
    public function create()
    {
        $branches = DB::table('branch')->select('id', 'name')->get();
        return view('announcement.create', compact('branches'));
    }


    public function store(Request $request)
    {
        
        $announcement = new Announcement();
        $announcement->branch = $request->branch;
        $announcement->coaching_type = $request->coaching_type;
        $announcement->gender = $request->gender;
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        if ($request->hasFile('attachment')) {
            $fileName = time() . '.' . $request->attachment->extension();
            $request->attachment->move(public_path('assets/attachments'), $fileName);
            $announcement->attachment = 'assets/attachments/' . $fileName;
        } else {
            $announcement->attachment = null;
        }
        $announcement->category = $request->coaching_type === 'Offline' ? $request->category : null;
    
        $announcement->save();
        return to_route('announcement.index');
    }

    public function edit(Request $request, Announcement $announcement)
    {
        $branches = DB::table('branch')->select('id', 'name')->get();

        return view('announcement.edit', compact('announcement', 'branches'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement->update($request->all());
        return to_route('announcement.index')->with('success', 'Announcement details successfully updated.');
    }

    public function destroy(Request $request, Announcement $announcement)
    {
        $announcement->delete();
        session()->flash('success', 'Announcement deleted successfully');
        return to_route('announcement.index');
    }
    
    public function notification(Request $request)
    {
        $announcements=Announcement::all();
        return view('student.notification', compact('announcements'));
    }
    


}
