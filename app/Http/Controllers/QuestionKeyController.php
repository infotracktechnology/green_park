<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\QuestionKey;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;



class QuestionKeyController extends Controller
{
    public function index()
    {

        $academicyear = AcademicYear::all();
        $questionKeys = QuestionKey::where('academic_year', $this->academic_year)
        ->when(auth()->user()->branch, function ($query) {
            $query->where('branch', 'like', '%' . auth()->user()->branch . '%');
        })
        ->latest()
        ->get();
    
        //$questionKeys = QuestionKey::orderBy('id', 'desc')->get();
        // dd($questionKeys->pluck('id', 'created_at'));
        return view('questionkey.index', compact('questionKeys'));
    }

   
    public function create()
    {
      
        // Pass the branches to the create view
        return view('questionkey.create');
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
        $filePath = $file->move('questionkey', $fileName);
    
        // Convert array to comma-separated string (no quotes, just commas)
        $branchData = implode(',', $request->branch);
        $coachingTypeData = implode(',', $request->coaching_type);
   
        QuestionKey::create([
            'title' => $request->title,
            'academic_year' => $request->academic_year,
            'branch' => $branchData,
            'coaching_type' => $coachingTypeData,
            'file_path' => $filePath,
            'created_at' => now(),
        ]);
    
        return redirect()->route('questionkey.index')->with('success', 'Question Key added successfully!');
    }
    

    public function edit($id)
    {
        $questionKey = QuestionKey::findOrFail($id);
      
        return view('questionkey.edit', compact('questionKey'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'branch' => 'required|array',
            'coaching_type' => 'required|array',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);
    
        $questionKey = QuestionKey::findOrFail($id);
    
        if ($request->hasFile('file')) {
            // Delete old file if exists
            $oldFilePath = storage_path('app/public/' . $questionKey->file_path);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
    
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move('questionkey', $fileName);
            $questionKey->file_path = 'questionkey/'.$fileName;
        }
    
        // Convert array to plain comma-separated string before saving
        $questionKey->title = $request->title;
        $questionKey->branch = implode(',', $request->branch);
        $questionKey->coaching_type = implode(',', $request->coaching_type);
        $questionKey->academic_year = $request->academic_year;
        $questionKey->save();
    
        return redirect()->route('questionkey.index')->with('success', 'Question Key updated successfully!');
    }
    

    public function destroy($id)
    {
        $questionKey = QuestionKey::findOrFail($id);
        $filePath = storage_path('questionKey' . $questionKey->file_path);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $questionKey->delete();

        return redirect()->route('questionkey.index')->with('success', 'Question Key deleted successfully!');
    }

    public function questionkey(Student $student)
    {
        $questionKeys = auth('student')->user()->questionkey();
        return view('student.questionkey', compact('questionKeys'));
    }
}