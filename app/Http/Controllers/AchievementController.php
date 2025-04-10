<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\Branch;
use App\Models\AcademicYear;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::latest()->get();
        return view('achievement.index', compact('achievements'));
    }

    public function create()
    {
        $branches = Branch::all(); 
        $academicyear = AcademicYear::all(); 
        return view('achievement.create');
    }

  
    public function store(Request $request)
{
    $request->validate([
        'video' => 'nullable|file|mimes:mp4,mov,avi|max:40960', // 40MB
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'link' => 'nullable|url',
    ],
   [
        'video.max' => 'The video must not be greater than 40MB.',
        'images.*.max' => 'Each image must not be greater than 2MB.',
    ]);
    $achievement = new Achievement();

    $achievement->academic_year = $request->academic_year;
    $achievement->branch = implode(',', $request->branch);
    $achievement->coaching_type = implode(',', $request->coaching_type);
    $achievement->category = implode(',', $request->category);
    $achievement->link = $request->link;
    $achievement->content = $request->content;

    if ($request->hasFile('video')) {
        $videoFile = $request->file('video');
        $videoName = time() . '_' . $videoFile->getClientOriginalName();
        $videoFile->move('achievement', $videoName); 
        $achievement->video = 'achievement/' . $videoName;
    }
    
   
    if ($request->hasFile('images')) {
        $images = [];
        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move('achievement', $imageName);
            $images[] = 'achievement/' . $imageName;
        }
        $achievement->images = ($images);
    }

    $achievement->save();

    return redirect()->route('achievement.index')->with('success', 'Achievement added successfully!');
}

   

public function edit(Achievement $achievement)
{
    $branches = Branch::all(); 
    $academicyear = AcademicYear::all(); 

    return view('achievement.edit', compact('achievement', 'branches', 'academicyear'));
}


public function update(Request $request, Achievement $achievement)
{
    $request->validate([
        'video' => 'nullable|file|mimes:mp4,mov,avi|max:40960',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'link' => 'nullable|url',
    ]);

    $achievement->academic_year = $request->academic_year;
    $achievement->branch = implode(',', $request->branch);
    $achievement->coaching_type = implode(',', $request->coaching_type);
    $achievement->category = implode(',', $request->category ?? []);
    $achievement->content = $request->content;

    $selected = $request->category ?? [];

    // Handle Video
    if (in_array('Video', $selected) && $request->hasFile('video')) {
        if ($achievement->video && file_exists($achievement->video)) unlink($achievement->video);
        $videoName = time().'_'.$request->video->getClientOriginalName();
        $request->video->move('achievement', $videoName);
        $achievement->video = 'achievement/'.$videoName;
    } elseif (!in_array('Video', $selected)) {
        if ($achievement->video && file_exists($achievement->video)) unlink($achievement->video);
        $achievement->video = null;
    }

    // Handle Images
    if (in_array('Image', $selected) && $request->hasFile('images')) {
        if ($achievement->images) {
            foreach (($achievement->images) as $img) {
                if (file_exists($img)) unlink($img);
            }
        }
        $paths = [];
        foreach ($request->file('images') as $image) {
            $name = time().'_'.$image->getClientOriginalName();
            $image->move('achievement', $name);
            $paths[] = 'achievement/'.$name;
        }
        $achievement->images =($paths);
    } elseif (!in_array('Image', $selected)) {
        if ($achievement->images) {
            foreach (($achievement->images) as $img) {
                if (file_exists($img)) unlink($img);
            }
        }
        $achievement->images = null;
    }

   
    $achievement->link = in_array('Link', $selected) ? $request->link : null;

    $achievement->save();

    return redirect()->route('achievement.index')->with('success', 'Achievement updated successfully!');
}


    public function destroy(Achievement $achievement)
    {
        $achievement->delete();
        return redirect()->route('achievement.index')->with('success', 'Achievement deleted successfully!');
    }
}
