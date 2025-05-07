<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RevisionVideo;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Http\Controllers\ImportController;


class RevisionVideoController extends Controller
{
    public function index()
    {
        $academic_years = AcademicYear::all();
        
        $revisionvideos = RevisionVideo::when($this->academic_year, function ($query) {
            $query->where('academic_year', $this->academic_year);
        })
        ->latest()
        ->get();
    
        return view('revisionvideo.index', compact('revisionvideos'));
    }

    public function create()
    {
        return view('revisionvideo.create');
    }

    public function store(Request $request,ImportController $import)
    {
        if ($request->hasFile('file')) {
           
            $csvData = $import->parseCSV($request->file->getRealPath());
            
    
            foreach ($csvData as $data) {
                
                if(!isset($data['subject'])){
                    return redirect()->back()->with('error', 'Subject Field is required.');
                }
    
    
                $subject = trim($data['subject'] ?? 'Unknown');
                $chapter = trim($data['chapter'] ?? 'Unknown');
                $expire_at = $request->expire_at;
                $video_id = trim($data['video_id'] ?? '0');
                $academic_year = $request->academic_year;
    
                RevisionVideo::create([
                    'subject' => $subject,
                    'chapter' => $chapter,
                    'expire_at' => $expire_at,
                    'video_id' => $video_id,
                    'academic_year' => $academic_year,
                ]);
            }
    
            return redirect()->back()->with('success', 'Revision videos uploaded successfully!');
        }
    
        return redirect()->back()->with('error', 'No file selected for upload.');
        // try {
        //     RevisionVideo::create($request->all());
        //     return redirect()->route('RevisionVideo.index')->with('success', 'Class video added successfully!');
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'Failed to add class video! ' . $e->getMessage());
        // }
    }
    
        public function destroy($id){
        $video = RevisionVideo::findOrFail($id);
        $video->delete();

        return redirect()->route('revisionvideo.index')->with('success', 'Revision Video deleted successfully!');
    }



    public function edit($id){
    $video = RevisionVideo::findOrFail($id);
    return view('revisionvideo.edit', compact('video'));
}

public function update(Request $request, $id)
{
    
    try {
        $RevisionVideo = RevisionVideo::findOrFail($id);
        $RevisionVideo->update($request->all());
        return redirect()->route('revisionvideo.index')->with('success', 'Revision video updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update class video! ' . $e->getMessage());
    }
}


    public function revisionvideo(Request $request,Student $student)
    {
        $datetime = date('Y-m-d H:i:s');
        $revisionvideos = RevisionVideo::where('expire_at', '>=', $datetime)->get();
        return view('student.revisionvideo', compact('revisionvideos'));
    }

  
    public function bulkDelete(Request $request)
{
    $ids = explode(",", $request->ids);

    if (empty($ids)) {
        return response()->json(['message' => 'No videos selected'], 400);
    }

    RevisionVideo::whereIn('id', $ids)->delete();

    return response()->json(['message' => 'Selected videos deleted successfully!'], 200);
}

    
    
}


