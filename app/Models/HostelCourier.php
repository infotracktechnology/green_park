<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelCourier extends Model
{
    use HasFactory;

    protected $table = 'hostel_courier';
    protected $guarded = [];
    protected $casts = [
        'datetime_arrival' => 'datetime',
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

