<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassVideo;
use App\Models\Student;

class ClassVideoController extends Controller
{
    public function index()
    {
        $classvideos = ClassVideo::latest()->get();
        return view('classvideo.index', compact('classvideos'));
    }

    public function create()
    {
        return view('classvideo.create');
    }

    public function store(Request $request)
    {
    
        try {
            ClassVideo::create($request->all());
            return redirect()->route('classvideo.index')->with('success', 'Class video added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add class video! ' . $e->getMessage());
        }
    }
    
        public function destroy($id){
        $video = ClassVideo::findOrFail($id);
        $video->delete();

        return redirect()->route('classvideo.index')->with('success', 'Video deleted successfully!');
    }




    public function edit($id){
    $classvideo = ClassVideo::findOrFail($id);
    return view('classvideo.edit', compact('classvideo'));
}

public function update(Request $request, $id)
{
    
    try {
        $classvideo = ClassVideo::findOrFail($id);
        $classvideo->update($request->all());
        return redirect()->route('classvideo.index')->with('success', 'Class video updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update class video! ' . $e->getMessage());
    }
}


    public function classvideo(Request $request,Student $student)
    {
        $subject = $request->subject ?? 0;
        $classvideos = $student->classvideo($subject);
        $classvideos = $classvideos->groupBy('period');
        
        return view('student.classvideo', compact('classvideos', 'subject'));
    }

}

