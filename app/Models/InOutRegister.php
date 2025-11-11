<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InOutRegister extends Model
{
    use HasFactory;

    protected $table = 'in_out_register';
    protected $guarded = [];
    protected $casts = [
        'datetime_in' => 'datetime',
        'datetime_out' => 'datetime',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

}

