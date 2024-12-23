<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelAllocation extends Model
{
    use HasFactory;

    protected $table = 'hostel_allocations';
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
}

