<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamAnswer extends Model
{


    public $table = 'exam_answer';

    protected $guarded = [];

    public function Exam()
    {
        return Exam::where('testid', $this->test_id)->where('academic_year',$this->academic_year)->first();
    }

    public function Student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

}


