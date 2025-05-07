<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassVideo;
use App\Models\Student;
use App\Models\RevisionVideo;
use App\Models\AcademicYear;

class ClassVideoController extends Controller
{
    public function index()
    {
        $academic_years = AcademicYear::all();
        $classvideos = ClassVideo::where('academic_year', $this->academic_year)
        ->when(auth()->user()->branch, function ($query) {
          
        })
        ->get();
    
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

    public function showUploadForm()
    {
        return view('classvideo.upload');
    }
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->getRealPath();
    
            // Open and read the CSV file
            $csvData = array_map('str_getcsv', file($filePath));
            $header = array_shift($csvData); // Extract headers
    
            foreach ($csvData as $row) {
                $data = array_combine($header, $row);
    
                // $s_no = intval(trim($data['s_no'] ?? 0)); // Ensure integer value
                $subject = trim($data['subject'] ?? 'Unknown');
                $chapter = trim($data['chapter'] ?? 'Unknown');
                $period = trim($data['period'] ?? '0'); // Period can still be a string or numeric
                $video_id = trim($data['video_id'] ?? '0');
                $academic_year = $request->academic_year;
    
                ClassVideo::create([
                   
                    'subject' => $subject,
                    'chapter' => $chapter,
                    'period' => $period,
                    'video_id' => $video_id,
                    'academic_year' => $academic_year
                ]);
            }
    
            return redirect()->back()->with('success', 'Class videos uploaded successfully!');
        }
    
        return redirect()->back()->with('error', 'No file selected for upload.');
    }
    
    public function schedule(Request $request){
        $ClassVideo = ClassVideo::whereIn('id', $request->ids)->update(['start_at' => $request->start_at, 'end_at' => $request->end_at]);
        if($ClassVideo){
            return response()->json(['status' => true, 'message' => 'Class video scheduled successfully.']);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Failed to schedule class video.']);
        }
    }

    
}


