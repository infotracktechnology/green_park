<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Download;
use App\Models\Student;
use App\Models\AcademicYear;


class DownloadController extends Controller
{

    public function index() 
    { 
        $academicyear = AcademicYear::all();
        $download = Download::latest()->get();
        return view('download.index',compact('download'));
    }

    public function create()
    {
        $academicyear = AcademicYear::all();
        $branches = Branch::all();
        return view('download.create', compact('branches'));
    }
 

   
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'branch' => 'required|array',
            'coaching_type' => 'required|array',
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);
    
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
  
        $filePath = $file->move('download', $fileName);
     
        // Convert array to comma-separated string
        $branchData = implode(',', $request->branch);
        $coachingTypeData = implode(',', $request->coaching_type);
    
        Download::create([
            'title' => $request->title,
            'academic_year' => $request->academic_year,
            'branch' => $branchData,
            'coaching_type' => $coachingTypeData,
            'file_path' => $filePath,
        ]);
    
    
        return redirect()->route('download.index')->with('success', 'Answer Key added successfully!');
    }
    

    
    public function edit($id)
    {
        $download = Download::findOrFail($id);
        $branches = Branch::all();
        return view('download.edit', compact('download', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'branch' => 'required|array',
            'coaching_type' => 'required|array',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);
    
        $download = Download::findOrFail($id);
    
        if ($request->hasFile('file')) {
            $oldFilePath = storage_path('app/public/' . $download->file_path);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
    
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move('download', $fileName);
    
            $download->file_path = 'download/'.$fileName;
        }
    
        // Convert arrays to comma-separated strings
        $download->title = $request->title;
        $download->academic_year = $request->academic_year;
        $download->branch = implode(',', $request->branch);
        $download->coaching_type = implode(',', $request->coaching_type);
        $download->save();
    
        return redirect()->route('download.index')->with('success', 'Answer Key updated successfully!');
    }
    

    public function destroy($id)
    {
        $download = Download::findOrFail($id);
        $filePath = public_path($download->file_path); // Correct file path
    
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    
        $download->delete();
    
        return redirect()->route('download.index')->with('success', 'Answer Key deleted successfully!');
    }
    

    

    public function download(Student $student)
    {
        $download = auth('student')->user()->downloads();
        return view('student.download', compact('download'));
    }
     
}
