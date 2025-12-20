<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Referencevideo;
use App\Models\Student;

class ReferencevideoController extends Controller
{
    public function index()
    {
        $referencevideos = Referencevideo::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, fn($query) => $query->where('branch', 'like', '%' . auth()->user()->branch . '%'))
            ->get();

        return view('referencevideo.index', compact('referencevideos'));
    }

    public function create()
    {
        return view('referencevideo.create');
    }

    public function store(Request $request)
    {
        $data = $request->except('file', '_token');

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $csvData = array_map('str_getcsv', file($request->file('file')->getRealPath()));

        if (empty($csvData)) {
            return back()->with('error', 'CSV file is empty.');
        }

        $header = array_map('trim', array_shift($csvData));
        $referencevideos = [];

        foreach ($csvData as $row) {
            $row = array_map('trim', $row);
            $record = array_combine($header, $row);

            if (empty($record['subject']) || empty($record['video_id'])) {
                return back()->with('error', 'Subject and Video ID fields are required.');
            }

            $referencevideos[] = array_merge($data, [
                'subject' => $record['subject'],
                'video_id' => $record['video_id'],
                'period' => $record['period'] ?? '0',
                'chapter' => $record['chapter'] ?? 'Unknown',
                'day' => $record['day'] ?? '',
                'date' => $record['date'] ?? ''
            ]);
        }

        Referencevideo::insert($referencevideos);
        return redirect()->route('referencevideo.index')->with('success', 'Class videos uploaded successfully.');
    }

    public function destroy(Referencevideo $referencevideo)
    {
        $referencevideo->delete();
        return redirect()->route('referencevideo.index')->with('success', 'Revision Video deleted successfully!');
    }

    public function update(Request $request, Referencevideo $referencevideo)
    {
        $data = $request->all();

        foreach (['coaching_type', 'branch', 'category', 'batch'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        $referencevideo->update($data);
        return redirect()->route('referencevideo.index')->with('success', 'Video updated successfully.');
    }

    public function studentReferencevideos()
    {
        $student = Student::where('student_id', auth()->user()->student_id)->first();
        $referencevideos = Referencevideo::ForStudent($student);
        return view('student.referencevideo', compact('referencevideos'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = explode(",", $request->ids);
        if (empty($ids)) {
            return response()->json(['message' => 'No videos selected'], 400);
        }

        Referencevideo::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Videos deleted successfully'], 200);
    }
}
