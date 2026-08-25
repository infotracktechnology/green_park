<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\Branch;
use App\Models\Student;
use App\Models\AcademicYear;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $achievements = Achievement::where('academic_year', $this->academic_year)
            ->when(auth()->user()->branch, function ($query) {
                $query->where('branch', 'like', '%' . auth()->user()->branch . '%');
            })
            ->when($request->coaching_type, function ($query, $coaching_type) {
                $query->where('coaching_type', 'like', '%' . $coaching_type . '%');
            })
            ->when($request->course, function ($query, $course) {
                $query->where('course', 'like', '%' . $course . '%');
            })
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'achievements' => $achievements]);
        }

        return view('achievement.index', compact('achievements'));
    }

    public function show(Request $request, Achievement $achievement)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'achievement' => $achievement]);
        }
        return redirect()->route('achievement.index');
    }

    public function achievement()
    {
        $achievements = Achievement::ForStudent(auth()->user());
        return view('student.achievement', compact('achievements'));
    }

    public function create()
    {
        return view('achievement.create');
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token', '_method', 'existing_images']);
        foreach (['coaching_type', 'branch', 'category', 'batch', 'filecategory'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
            } else {
                $data[$field] = null;
            }
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

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Achievement added successfully!', 'data' => $achievement], 200);
        }

        return redirect()->route('achievement.index')->with('success', 'Achievement added successfully!');
    }

    public function edit(Request $request, Achievement $achievement)
    {
        $type = Student::StudentFilterQuery($achievement->branch, $achievement->course, null, null, null)->select('coaching_type')->distinct()->get()->pluck('coaching_type')->toArray();

        $section = Student::StudentFilterQuery($achievement->branch, $achievement->course, $achievement->type, $achievement->category, $achievement->batch, $achievement->gender)->select('section')->distinct()->orderBy('section')->get()->pluck('section')->toArray();

        $students = Student::StudentFilterQuery($achievement->branch, $achievement->course, $achievement->type, null, null)->get()->pluck('student_name', 'student_id')->toArray();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'achievement' => $achievement, 'type' => $type, 'section' => $section, 'students' => $students]);
        }

        return view('achievement.edit', compact('achievement', 'type', 'section', 'students'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $data = $request->except(['_token', '_method', 'existing_images']);
        foreach (['coaching_type', 'branch', 'category', 'batch', 'filecategory'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field]) ? implode(',', $data[$field]) : $data[$field];
            } else {
                $data[$field] = null;
            }
        }
 
        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            $videoName = time() . '_' . $videoFile->getClientOriginalName();
            $videoFile->move('achievement', $videoName);
            $data['video'] = 'achievement/' . $videoName;
        } elseif ($request->has('video')) {
            $data['video'] = $request->input('video') ?: null;
        }

        $images = [];
        if ($request->has('existing_images')) {
            $existing = $request->input('existing_images');
            if (is_array($existing)) {
                $images = array_values(array_filter($existing));
            } else if (is_string($existing) && !empty($existing)) {
                $decoded = json_decode($existing, true);
                $images = is_array($decoded) ? array_values(array_filter($decoded)) : array_values(array_filter(explode(',', $existing)));
            }
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move('achievement', $imageName);
                $images[] = 'achievement/' . $imageName;
            }
        }
        if (!empty($images)) {
            $data['images'] = $images;
        } else if ($request->has('existing_images') || $request->hasFile('images')) {
            $data['images'] = null;
        }

        if ($request->hasFile('pdf')) {
            $pdfFile = $request->file('pdf');
            $pdfName = time() . '_' . $pdfFile->getClientOriginalName();
            $pdfFile->move('achievement', $pdfName);
            $data['pdf'] = 'achievement/' . $pdfName;
        } elseif ($request->has('pdf')) {
            $data['pdf'] = $request->input('pdf') ?: null;
        }

        if ($request->has('link')) {
            $data['link'] = $request->input('link') ?: null;
        }

        $achievement->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'Achievement updated successfully!', 'data' => $achievement], 200);
        }

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