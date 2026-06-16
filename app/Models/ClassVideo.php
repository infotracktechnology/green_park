<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassVideo extends Model
{


    protected $table = 'class_video';

    protected $guarded = [];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

   
    public static function boot()
	{
		parent::boot();
    }

    public function branchNames()
    {
        return Branch::whereIn('id', explode(',', $this->branch))->get()->implode('name', '/');
    }

public static function ForStudent(Student $student)
{
        return self::query()
        ->where(function ($query) use ($student) {
            $query->where('usertype', 'INDIVIDUAL')
                  ->where('students', $student->student_id)
                  ->where('start_at', '<=', date('Y-m-d H:i:s'))
                  ->where('end_at', '>=', date('Y-m-d H:i:s'));
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
                  })
                  ->whereIn('gender', [$student->gender, 'All'])
                  ->where('start_at', '<=', date('Y-m-d H:i:s'))
                  ->where('end_at', '>=', date('Y-m-d H:i:s'));
        })->orderByDesc('date')
        ->get();
}
}

