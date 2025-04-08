<?php
namespace App\Http\Controllers;

use App\Models\Worksheet;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class WorksheetController extends Controller
{
    public function index()
    {
        $worksheets = Worksheet::latest()->get();
        return view('worksheet.index', compact('worksheets'));
    }

    public function create()
    {
        $academicyear = AcademicYear::all();
        $branches = Branch::all();
        return view('worksheet.create' , compact('branches'));
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
  
        $filePath = $file->move('worksheet', $fileName);
     
        
        $branchData = implode(',', $request->branch);
        $coachingTypeData = implode(',', $request->coaching_type);
    
        Worksheet::create([
            'title' => $request->title,
            'academic_year' => $request->academic_year,
            'branch' => $branchData,
            'coaching_type' => $coachingTypeData,
            'file_path' => $filePath,
        ]);
    
    

        return redirect()->route('worksheet.index')->with('success', 'Worksheet created successfully.');
    }

    public function edit(Worksheet $worksheet)
    {
        $branches = Branch::all();
        $academicyear = AcademicYear::all();
    
        
    
        return view('worksheet.edit', compact('worksheet', 'branches', 'academicyear'));
    }
    

    public function update(Request $request, Worksheet $worksheet)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'branch' => 'required|array',
            'coaching_type' => 'required|array',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);
    
        $filePath = $worksheet->file_path; 
    
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move('worksheet', $fileName);
        }
    
        $branchData = implode(',', $request->branch);
        $coachingTypeData = implode(',', $request->coaching_type);
    
        $worksheet->update([
            'title' => $request->title,
            'academic_year' => $request->academic_year,
            'branch' => $branchData,
            'coaching_type' => $coachingTypeData,
            'file_path' => $filePath,
        ]);
    
        return redirect()->route('worksheet.index')->with('success', 'Worksheet updated successfully.');
    }
    

    public function destroy(Worksheet $worksheet)
    {
        $worksheet->delete();
        return redirect()->route('worksheet.index')->with('success', 'Worksheet deleted.');
    }

    // public function worksheet(Student $student)
    // {
    //     // $worksheet = auth('student')->user()->worksheet();
    //     return view('student.worksheet');
    // }



    public function worksheet()
{
    $worksheets = auth('student')->user()->worksheet();
    return view('student.worksheet', compact('worksheets'));
}

}
