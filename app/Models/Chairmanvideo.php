<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chairmanvideo extends Model
{
    public $table = 'chairmanvideos';
    protected $guarded = [];

    function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id','id');
    }
    public static function boot()
	{
		parent::boot();
    }

}
