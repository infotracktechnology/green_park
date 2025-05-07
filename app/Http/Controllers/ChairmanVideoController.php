<?php

namespace App\Http\Controllers;
use App\Models\AcademicYear;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Chairmanvideo;
use Illuminate\Support\Facades\DB;

class ChairmanVideoController extends Controller
{
    public function index()
{
    $academic_years = AcademicYear::all();
    $branches = Branch::all();
    $branchList = DB::table('branch')->pluck('name', 'id')->toArray();

    $chairmanvideos = Chairmanvideo::where('academic_year', $this->academic_year)
    ->when(auth()->user()->branch, function ($query) {
        $query->where('branch_id', 'like', '%' . auth()->user()->branch . '%');
    })
    ->get();



    return view('chairmanvideo.index', compact('chairmanvideos', 'branches', 'branchList'));
}

    public function create()
    {
       
        return view('chairmanvideo.create');
    }
    public function store(Request $request)
    {
        $chairmanvideos = new Chairmanvideo();
        $chairmanvideos->academic_year = $request->academic_year;
        $chairmanvideos->branch_id = implode(',', $request->branch_id);
         $chairmanvideos->coaching_type = implode(',', $request->coaching_type);
        $chairmanvideos->gender = $request->gender;
        $chairmanvideos->title = $request->title;
        $chairmanvideos->video_id = $request->video_id;
        if ($request->hasFile('attachment')) {
            $fileName = time() . '.' . $request->attachment->extension();
            $request->attachment->move(public_path('chairman/video'), $fileName);
            $chairmanvideos->attachment = 'chairman/video/' . $fileName;
        } else {
            $chairmanvideos->attachment = null;
        }
        $chairmanvideos->save();
        return redirect()->route('chairmanvideo.index')
            ->with('success', 'Chairman video created successfully.');
    }

 public function edit(Request $request, Chairmanvideo $chairmanvideo)
    {
       
        return view('chairmanvideo.edit', compact('chairmanvideo'));
 }
 public function update(Request $request, $id)
{
    $chairmanvideo = Chairmanvideo::findOrFail($id);
    $chairmanvideo->academic_year = $request->academic_year;
    $chairmanvideo->title = $request->title;
    $chairmanvideo->video_id = $request->video_id;
    $chairmanvideo->gender = $request->gender;
    $chairmanvideo->coaching_type = implode(',', $request->coaching_type);
    $chairmanvideo->branch_id = implode(',', $request->branch_id);

    if ($request->hasFile('attachment')) {
       
        if ($chairmanvideo->attachment && file_exists(public_path($chairmanvideo->attachment))) {
            unlink(public_path($chairmanvideo->attachment));
        }

        $fileName = time() . '.' . $request->attachment->extension();
        $request->attachment->move(public_path('chairman/video'), $fileName);
        $chairmanvideo->attachment = 'chairman/video/' . $fileName;
    }

  
    $chairmanvideo->save();

    return redirect()->route('chairmanvideo.index')->with('success', 'Video updated successfully!');
}
    public function destroy(Request $request, Chairmanvideo $chairmanvideo)
    {
        $chairmanvideo->delete();
        session()->flash('success', 'Video deleted successfully');
        return to_route('chairmanvideo.index');
    }
    public function chairmanvideo(Request $request)
    {
        $chairmanvideo = auth()->user()->chairmanvideo();
       
       return view('student.chairmanvideo', compact('chairmanvideo'));
    }

    public function video(Request $request, $id){
        $id = base64_decode($id);
        return view('layouts.video', compact('id'));
    }
}
