<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Exam extends Model
{
 

    public $table = 'exam';

    protected $guarded = [];
    protected $casts = [
        'questions' => 'json',
    ];

    function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id','id');
    }

	public static function boot()
	{
		parent::boot();
    }
}