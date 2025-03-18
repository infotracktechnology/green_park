<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscussionVideo;
use App\Models\Branch;

class DiscussionVideoController extends Controller
{
   
    public function index()
    {
        $discussionvideos = DiscussionVideo::all();
        return view('discussionvideo.index', compact('discussionvideos'));
    }
    
 
    public function create()
    {
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

    $video = DiscussionVideo::create([
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
}
