<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Exam extends Model
{
 

    public $table = 'exam';

    protected $guarded = [];
    protected $casts = [
        'questions' => 'json',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id','id');
    }


    public static function getOngoingExams($coachingType, $branchId)
{
    return self::where('start_at', '<=', now())  
                ->where('end_at', '>=', now())    
                ->where('coaching_type', 'like', "%$coachingType%")
                ->where('branch_id', 'like', "%$branchId%")->first();

}
    
	public static function boot()
	{
		parent::boot();
    }
}