<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use App\Models\Announcement;
class Student extends Authenticatable
{
    public $table = 'student';

    protected $guarded = [];
    function branch()
    {
        return $this->belongsTo(Branch::class,'campus','id');
    }
    function room()
    {
        return $this->belongsTo(HostelRoom::class,'room_id','id');
    }
    public static function boot()
	{
		parent::boot();
		static::creating(function($model)
		{
            $model->password_1 = Str::random(8);
            $model->password = bcrypt($model->password_1);
		});
        static::created(function($model) {
            $model->user_name = 'L' . $model->id;
            $model->save();
        });
    }
    public function announcement(){
        return Announcement::where('branch', $this->campus)->where('gender', $this->gender)->where('coaching_type', $this->coaching_type);
    }
    
    
}
