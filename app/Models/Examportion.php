<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examportion extends Model
{
    public $table = 'examportion';
    protected $guarded = [];

    function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id','id');
    }
   
}
