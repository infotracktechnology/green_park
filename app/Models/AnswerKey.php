<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerKey extends Model
{
    use HasFactory;
    protected $table = 'answer_key';
    protected $guarded = [];

    protected $casts = [
        'file_path' => 'json',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_schedule' => 'boolean',
    ];

    public function branchNames()
    {
        return Branch::whereIn('id', explode(',', $this->branch))->get()->implode('name', '/');
    }

    public static function ForStudent(Student $student)
    {
        return self::query()
            ->where(function($mainQuery) use ($student) {
                $mainQuery->where(function ($query) use ($student) {
                    $query->where('usertype', 'INDIVIDUAL')
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
                        })
                        ->whereIn('gender', [$student->gender, 'All']);
                });
            })
            ->where(function($q) {
                $q->where('is_schedule', false)
                  ->orWhere(function($q2) {
                      $q2->where('is_schedule', true)
                         ->where('start_at', '<=', date('Y-m-d H:i:s'));
                  });
            })
            ->latest()->get();
    }
}
