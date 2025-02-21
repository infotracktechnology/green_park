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
        if(strpos($this->branch_id, ',')){
            return Branch::whereIn('id', explode(',', $this->branch_id))->get()->implode('name', ', ');
        }
        return Branch::find($this->branch_id)->name;
    }


    public static function getOngoingExams($coachingType, $branchId)
    {
    return self::where('start_at', '<=', now())  
                ->where('end_at', '>=', now())    
                ->where('coaching_type', 'like', "%$coachingType%")
                ->where('branch_id', 'like', "%$branchId%")->first();

    }

    public static function getUpcomingExams($coachingType, $branchId){
        $datetime = date('Y-m-d H:i:s');
        return self::where('start_at', '>=', $datetime)
                ->where('coaching_type', 'like', "%$coachingType%")
                ->where('branch_id', 'like', "%$branchId%")->selectRaw("id,TIMESTAMPDIFF(MINUTE,start_at,$datetime)diff")->first();
    }
    
	public static function boot()
	{
		parent::boot();
    }
}