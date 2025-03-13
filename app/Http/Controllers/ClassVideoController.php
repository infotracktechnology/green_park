<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassVideo;

class ClassVideoController extends Controller
{
    public function index()
    {
        $classvideos = ClassVideo::latest()->get();
        return view('classvideo.index', compact('classvideos'));
    }
    // compact('videos')
    public function create()
    {
        return view('classvideo.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'chapter' => 'required|string',
            'period' => 'required|integer',
            'video_id' => 'required|string',
            'video_url' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
        ]);
    
        try {
            ClassVideo::create($validated);
            return redirect()->route('classvideo.index')->with('success', 'Class video added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add class video! ' . $e->getMessage());
        }
    }
    
        public function destroy($id)
    {
        $video = ClassVideo::findOrFail($id);
        $video->delete();

        return redirect()->route('classvideo.index')->with('success', 'Video deleted successfully!');
    }




    public function edit($id)
{
    $classvideo = ClassVideo::findOrFail($id);
    return view('classvideo.edit', compact('classvideo'));
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'subject' => 'required|string',
        'chapter' => 'required|string',
        'period' => 'required|integer',
        'video_id' => 'required|string',
        'video_url' => 'required|string',
        'start_at' => 'required|date',
        'end_at' => 'required|date|after:start_at',
    ]);

    try {
        $classvideo = ClassVideo::findOrFail($id);
        $classvideo->update($validated);
        return redirect()->route('classvideo.index')->with('success', 'Class video updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update class video! ' . $e->getMessage());
    }
}


    public function classvideo()
    {
        $classvideos = ClassVideo::latest()->get(); // Fetch all class videos
        return view('student.classvideo', compact('classvideos'));
    }

}

