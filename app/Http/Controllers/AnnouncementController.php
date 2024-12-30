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
        Announcement::create($request->all());

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
