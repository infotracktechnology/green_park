<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnswerKey;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class AnswerKeyController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::all();
        $answerKeys = AnswerKey::latest()->get();
        return view('answerkey.index', compact('answerKeys'));
    }

    public function create()
    {
        $academicYears = AcademicYear::all();
        $branches = Branch::all();
        return view('answerkey.create', compact('branches'));
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
        $filePath = $file->move('answerkey', $fileName);


        // Convert array to comma-separated string
        $branchData = implode(',', $request->branch);
        $coachingTypeData = implode(',', $request->coaching_type);

        AnswerKey::create([
            'title' => $request->title,
            'academic_year' => $request->academic_year,
            'branch' => $branchData,
            'coaching_type' => $coachingTypeData,
            'file_path' => $filePath,
        ]);

        return redirect()->route('answerkey.index')->with('success', 'Answer Key added successfully!');
    }


    public function edit($id)
    {
        $answerKey = AnswerKey::findOrFail($id);
        $branches = Branch::all();
        return view('answerkey.edit', compact('answerKey', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'branch' => 'required|array',
            'coaching_type' => 'required|array',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $answerKey = AnswerKey::findOrFail($id);

        if ($request->hasFile('file')) {
            $oldFilePath = storage_path('app/public/' . $answerKey->file_path);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move('answerkey', $fileName);

            $answerKey->file_path = 'answerkey/' . $fileName;
        }

        // Convert arrays to comma-separated strings
        $answerKey->title = $request->title;
        $answerKey->branch = implode(',', $request->branch);
        $answerKey->coaching_type = implode(',', $request->coaching_type);
        $answerKey->academic_year = $request->academic_year;
        $answerKey->save();

        return redirect()->route('answerkey.index')->with('success', 'Answer Key updated successfully!');
    }


    public function destroy($id)
    {
        $answerKey = AnswerKey::findOrFail($id);

        $filePath = storage_path('answerKey' . $answerKey->file_path);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $answerKey->delete();

        return redirect()->route('answerkey.index')->with('success', 'Answer Key deleted successfully!');
    }



    public function answerkey()
    {
        $answerKeys = auth('student')->user()->answerkey();
        return view('student.answerkey', compact('answerKeys'));
    }
}
