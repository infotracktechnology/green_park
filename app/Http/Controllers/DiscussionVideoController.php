<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscussionVideo;
use App\Models\Branch;
use App\Models\AcademicYear;

class DiscussionVideoController extends Controller
{
   
    public function index()
    {
        $academic_years = AcademicYear::all();
        $discussionvideos = DiscussionVideo::all();
        return view('discussionvideo.index', compact('discussionvideos'));
    }
    
 
    public function create()
    {
        $academicyear = AcademicYear::all();
        $branches = Branch::all();
        return view('discussionvideo.create' , compact('branches'));
    }

   
    public function store(Request $request)
{
    $request->validate([
        'branch' => 'required|array'
        
    ]);
    $branches = implode(',', $request->branch);
    $coachingTypes = implode(',', $request->coaching_type);
    $academic_year = $request->academic_year;

    $video = DiscussionVideo::create([
        'academic_year' => $academic_year,
        'video_id' => $request->video_id,
        'branch' => $branches,
        'coaching_type' => $coachingTypes,
        'subject' => $request->subject,
        'part' => $request->part,
        'start_at' => $request->start_at,
        'end_at' => $request->end_at,
        
    ]);


    return redirect()->route('discussionvideo.index')->with('success', 'Discussion video added successfully!');
}

    public function edit($id)
    {
        $video = DiscussionVideo::findOrFail($id);
        $branches = Branch::all(); 
        return view('discussionvideo.edit', compact('video', 'branches'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
           
            'branch' => 'required|array',
           
        ]);
    
        $branches = implode(',', $request->branch);
        $coachingTypes = implode(',', $request->coaching_type);
    
        $video = DiscussionVideo::findOrFail($id);
        $video->update([
            'academic_year' => $request->academic_year,
            'part' => $request->part,
            'video_id' => $request->video_id,
            'subject' => $request->subject,
            'branch' => $branches,
            'coaching_type' => $coachingTypes,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ]);
    
        return redirect()->route('discussionvideo.index')->with('success', 'Discussion video updated successfully!');
    }
    
   
    public function destroy($id)
    {
        $video = DiscussionVideo::findOrFail($id);
        $video->delete();

        return redirect()->route('discussionvideo.index')->with('success', 'Discussion video deleted successfully!');
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids; // Retrieve the selected IDs
    
        // Check if IDs are provided and not empty
        if (!$ids) {
            return response()->json(['message' => 'No videos selected'], 400);
        }
    
        // Convert the comma-separated string of IDs into an array and delete the videos
        DiscussionVideo::whereIn('id', explode(",", $ids))->delete();
    
        return response()->json(['message' => 'Selected discussion videos deleted successfully!'], 200);
    }
    
    
    

}
