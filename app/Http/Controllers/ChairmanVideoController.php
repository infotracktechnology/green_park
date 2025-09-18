<?php

namespace App\Http\Controllers;
use App\Models\AcademicYear;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Chairmanvideo;
use Illuminate\Support\Facades\DB;

class ChairmanVideoController extends Controller
{
    public function index()
{

    $chairmanvideos = Chairmanvideo::where('academic_year', $this->academic_year)
    ->when(auth()->user()->branch, function ($query) {
        $query->where('branch', 'like', '%' . auth()->user()->branch . '%');
    })
    ->get();
    return view('chairmanvideo.index', compact('chairmanvideos'));
}

    public function create()
    {
       
        return view('chairmanvideo.create');
    }
    public function store(Request $request)
    {
       $data = $request->all();

        foreach (['coaching_type','branch','category','batch'] as $field) {
         $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('attachment')) {
            $fileName = time() . '.' . $request->attachment->extension();
            $request->attachment->move(public_path('chairman/video'), $fileName);
            $chairmanvideos->attachment = 'chairman/video/' . $fileName;
        } 

        $chairmanvideo = Chairmanvideo::create($data);
        return redirect()->route('chairmanvideo.index')->with('success', 'Chairman video created successfully.');
    }

 public function edit(Request $request, Chairmanvideo $chairmanvideo)
{
    $type = Student::StudentFilterQuery($chairmanvideo->branch,$chairmanvideo->course,null,null,null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

    $section = Student::StudentFilterQuery($chairmanvideo->branch,$chairmanvideo->course,$chairmanvideo->type,$chairmanvideo->category,$chairmanvideo->batch,$chairmanvideo->gender)->select('section')->distinct()->get()->pluck('section')->toArray();

    $students = Student::StudentFilterQuery($chairmanvideo->branch,$chairmanvideo->course,$chairmanvideo->type,null,null)->get()->pluck('student_name','student_id')->toArray();
       
        return view('chairmanvideo.edit', compact('chairmanvideo','type','section','students'));
 }
 public function update(Request $request,Chairmanvideo $chairmanvideo)
{
    $data = $request->all();

     foreach (['coaching_type','branch','category','batch'] as $field) {
         $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
     }
     
    if ($request->hasFile('attachment')) {
        if ($chairmanvideo->attachment && file_exists(public_path($chairmanvideo->attachment))) {
            unlink(public_path($chairmanvideo->attachment));
        }
        $fileName = time() . '.' . $request->attachment->extension();
        $request->attachment->move(public_path('chairman/video'), $fileName);
        $chairmanvideo->attachment = 'chairman/video/' . $fileName;
    }

    $chairmanvideo->update($data);

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
        $student = Student::where('student_id',auth()->user()->student_id)->first();
        $chairmanvideo = Chairmanvideo::ForStudent($student)->latest()->first();
       return view('student.chairmanvideo', compact('chairmanvideo'));
    }

    public function video(Request $request, $id){
        $id = base64_decode($id);
        return view('layouts.video', compact('id'));
    }
}
