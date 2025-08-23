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
    ];
    function branch()
    {
        return $this->belongsTo(Branch::class, 'branch', 'id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
