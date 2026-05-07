<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

class Announcement extends Model
{
    public $table = 'announcement';

    protected $guarded = [];

     protected $casts = [
        'student_ids' => 'json',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
    function branch()
    {
        return $this->belongsTo(Branch::class, 'branch', 'id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function branchNames()
    {
        return Branch::whereIn('id', explode(',', $this->branch))->get()->implode('name', '/');
    }
    
public static function ForStudent(Student $student)
{
    return self::query()
        ->where(function($mainQuery) use ($student) {
            $mainQuery->where(function($q) use ($student) {
                $q->where('usertype', 'INDIVIDUAL')
                  ->where('students', $student->student_id);
            })
            ->orWhere(function ($query) use ($student) {
                $query->where('academic_year', $student->academic_year)
                    ->where('course', $student->course)
                    ->where('branch', 'like', "%{$student->campus}%")
                    ->where('coaching_type', 'like', "%{$student->coaching_type}%")
                    ->when($student->coaching_type === 'OFFLINE', function ($q) use ($student) {
                        if (in_array($student->course, ['NEET', 'JEE'])) {
                            if (in_array($student->campus, [1, 4, 5])) {
                                $q->where('category', 'like', "%{$student->hostel_dayscholar}%");
                            }
                            $q->where('batch', 'like', "%{$student->batch}%");
                        }
                        $q->where('section', 'like', "%{$student->section}%");
                    })->whereIn('gender', [$student->gender, 'All']);
            });
        })
        ->where(function($q) {
            $q->where('is_schedule', 0)
              ->orWhere(function($q2) {
                  $q2->where('is_schedule', 1)
                     ->where('start_at', '<=', date('Y-m-d H:i:s'));
              });
        });
}

public function StudentList()
{
    $query = Student::query();

    if ($this->usertype === 'INDIVIDUAL') {
        return $query
            ->where('academic_year', $this->academic_year)
            ->where('student_id', $this->students)
            ->whereNotNull('device_token')
            ->get();
    }

    $coachingTypes = explode(',', $this->coaching_type ?? '');
    $branches = explode(',', $this->branch ?? '');
    $sections = explode(',', $this->section ?? '');
    $category = explode(',', $this->category ?? '');
    $batch = explode(',', $this->batch ?? '');

    $query->where('academic_year', $this->academic_year)
        ->where('course', $this->course)
        ->whereIn('campus', $branches)
        ->whereIn('coaching_type', $coachingTypes);

    if (in_array('OFFLINE', $coachingTypes)) {
        $query->whereIn('section', $sections);
        if (in_array($this->course, ['NEET', 'JEE'])) {
            if (array_intersect([1, 4, 5], $branches)) {
                $query->whereIn('hostel_dayscholar', $category);
            }
            $query->whereIn('batch', $batch);
        }
    }

    if ($this->gender !== 'All') {
        $query->where('gender', $this->gender);
    }

    return $query->whereNotNull('device_token')->get();
}

}
