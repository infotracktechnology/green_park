<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

class Achievement extends Model
{
    protected $table = 'achievement';

    protected $guarded = [];
    protected $casts = [
        'images' => 'json',
       
    ];

    function branch()
    {
        return $this->belongsTo(Branch::class, 'branch', 'id');
    }
    
}
