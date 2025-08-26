<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * 
     *
     * @var string
     */
    protected $table = 'attendance';

    /**
     * 
     * 
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 
     * 
     *
     * @var array
     */
    protected $casts = [
        'attendance_date' => 'date', 
    ];

    public function student() {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}