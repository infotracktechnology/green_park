<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, function ($query) {
                $query->where('branch', 'like', '%' . auth()->user()->branch . '%');
            })
            ->latest()
            ->get();

        return view('achievement.index', compact('achievements'));
    }

    public function create()
    {
        return view('achievement.create');
    }


    public function store(Request $request)
    {
        $data = $request->all();
        foreach (['coaching_type', 'branch', 'category', 'batch', 'filecategory'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }

        // Video Upload
        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            $videoName = time() . '_' . $videoFile->getClientOriginalName();
            $videoFile->move('achievement', $videoName);
            $data['video'] = 'achievement/' . $videoName;
        }

        // Image Upload
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move('achievement', $imageName);
                $images[] = 'achievement/' . $imageName;
            }
            $data['images'] = $images;
        }

        if ($request->hasFile('pdf')) {
            $pdfFile = $request->file('pdf');
            $pdfName = time() . '_' . $pdfFile->getClientOriginalName();
            $pdfFile->move('achievement', $pdfName);
            $data['pdf'] = 'achievement/' . $pdfName;
        }

        $achievement = Achievement::create($data);

        return redirect()->route('achievement.index')->with('success', 'Achievement added successfully!');
    }



    public function edit(Achievement $achievement)
    {
        $type = Student::StudentFilterQuery($achievement->branch, $achievement->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($achievement->branch, $achievement->course, $achievement->type, $achievement->category, $achievement->batch, $achievement->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($achievement->branch, $achievement->course, $achievement->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        return view('achievement.edit', compact('achievement', 'type', 'section', 'students'));
    }


    public function update(Request $request, Achievement $achievement)
    {

        $data = $request->all();
        foreach (['coaching_type', 'branch', 'category', 'batch', 'filecategory'] as $field) {
            $data[$field] = isset($data[$field]) ? implode(',', $data[$field]) : null;
        }
 
        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            $videoName = time() . '_' . $videoFile->getClientOriginalName();
            $videoFile->move('achievement', $videoName);
            $data['video'] = 'achievement/' . $videoName;
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move('achievement', $imageName);
                $images[] = 'achievement/' . $imageName;
            }
            $data['images'] = $images;
        }

        if ($request->hasFile('pdf')) {
            $pdfFile = $request->file('pdf');
            $pdfName = time() . '_' . $pdfFile->getClientOriginalName();
            $pdfFile->move('achievement', $pdfName);
            $data['pdf'] = 'achievement/' . $pdfName;
        }

        $achievement->update($data);

        return redirect()->route('achievement.index')->with('success', 'Achievement updated successfully!');
    }


    public function destroy(Request $request, $id = null)
    {
        if($request->has('ids')) {
        $achievements = Achievement::whereIn('id', $request->ids)->get();
        foreach ($achievements as $achievement) {
        if ($achievement->video && file_exists($achievement->video)) unlink($achievement->video);
        if ($achievement->images) {
            foreach (($achievement->images) as $img) {
                if (file_exists($img)) unlink($img);
            }
        }
        if ($achievement->pdf && file_exists($achievement->pdf)) unlink($achievement->pdf);
        $achievement->delete();
        }
        }
        
        return redirect()->back()->with('success', 'Achievement deleted successfully!');
    }
}