<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SickRoomEntry extends Model
{
    use HasFactory;
    protected $table = 'sick_room_entry';
    protected $guarded = [];
    protected $casts = [
        'in_time' => 'datetime',
        'out_time' => 'datetime',
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
