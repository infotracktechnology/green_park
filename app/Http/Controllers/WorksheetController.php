<?php
namespace App\Http\Controllers;

use App\Models\Worksheet;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class WorksheetController extends Controller
{
    public function index(Request $request)
    {
        $worksheets = Worksheet::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($q) => $q->where('branch','like','%'.auth()->user()->branch.'%'))
            ->when($request->coaching_type, fn($q) => $q->where('coaching_type','like','%'.$request->coaching_type.'%'))
            ->latest()->get();

        return view('worksheet.index', compact('worksheets'));
    }

    public function create()
    {

        return view('worksheet.create');
    }

    public function store(Request $request)
    {
        $data = $request->except('file');
        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }
         
        $file_path = [];
        if ($request->hasFile('file')) {
            foreach($request->file('file')as $file){
                $originalName = $file->getClientOriginalName();
                $fileName = time().'_'.$originalName;
                $file->move('worksheet',$fileName);
                $file_path[] = 'worksheet/'.$fileName;
            }
        }
        $data['file_path'] = $file_path ?: null;
        Worksheet::create($data);

        return redirect()->route('worksheet.index')->with('success', 'Worksheet created successfully.');
    }

    public function edit(Worksheet $worksheet)
    {
        $type = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, $worksheet->type, $worksheet->category, $worksheet->batch, $worksheet->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, $worksheet->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();


        return view('worksheet.edit', compact('worksheet', 'type', 'section', 'students'));
    }


    public function update(Request $request, Worksheet $worksheet)
    {
        $data = $request->except('file');
        $data['is_schedule'] = $request->has('is_schedule') ? 1 : 0;

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $file_path = [];
        if ($request->hasFile('file')) {
            foreach($request->file('file')as $file){
                $originalName = $file->getClientOriginalName();
                $fileName = time().'_'.$originalName;
                $file->move('worksheet',$fileName);
                $file_path[] = 'worksheet/'.$fileName;
            }
        }
        $data['file_path'] = $file_path ?: null;
        $worksheet->update($data);

        return redirect()->route('worksheet.index')->with('success', 'Worksheet updated successfully.');
    }


    public function destroy(Request $request, $id=null)
    {
        if($request->has('ids')) {
            $worksheets = Worksheet::whereIn('id', $request->ids)->get();
            foreach ($worksheets as $worksheet) {
                if(!empty($worksheet->file_path)){
                    foreach($worksheet->file_path as $file_path){
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }

                    }
                } 
                $worksheet->delete();
            }
        }
        return redirect()->back()->with('success', 'Worksheet deleted.');
    }

    public function worksheet()
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $worksheets = Worksheet::ForStudent($student);
        return view('student.worksheet', compact('worksheets'));
    }
}
