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
        $worksheets = Worksheet::when($this->academic_year, function ($query) {
            $query->where('academic_year', $this->academic_year);
        })->when(auth()->user()->branch, function ($query) {
            $query->where('branch', 'like', '%' . auth()->user()->branch . '%');
        })->latest()->get();

        return view('worksheet.index', compact('worksheets'));
    }

    public function create()
    {

        return view('worksheet.create');
    }

    public function store(Request $request)
    {
        $data = $request->except('file');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('file')) {
            $originalName = $request->file('file')->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            $request->file('file')->move('worksheet', $fileName);
            $data['file_path'] = 'worksheet/' . $fileName;
        }

        Worksheet::create($data);

        return redirect()->route('worksheet.index')->with('success', 'Worksheet created successfully.');
    }

    public function edit(Worksheet $worksheet)
    {
        $type = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, $worksheet->type, $worksheet->category, $worksheet->batch, $worksheet->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($worksheet->branch, $worksheet->course, $worksheet->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();


        return view('worksheet.edit', compact('worksheet'));
    }


    public function update(Request $request, Worksheet $worksheet)
    {
        $data = $request->except('file');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        if ($request->hasFile('file')) {
            $originalName = $request->file('file')->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            $request->file('file')->move('worksheet', $fileName);
            $data['file_path'] = 'worksheet/' . $fileName;
        }

        $worksheet->update($data);

        return redirect()->route('worksheet.index')->with('success', 'Worksheet updated successfully.');
    }


    public function destroy(Worksheet $worksheet)
    {
        $worksheet->delete();
        return redirect()->route('worksheet.index')->with('success', 'Worksheet deleted.');
    }

    public function worksheet()
    {
        $student = Student::where('student_id', auth('student')->user()->student_id)->first();
        $worksheets = Worksheet::ForStudent($student);
        return view('student.worksheet', compact('worksheets'));
    }
}
