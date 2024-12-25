<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public $table = 'announcement';

    protected $guarded = [];
    function branch()
    {
        return $this->belongsTo(Branch::class,'campus','id');
    }
}
