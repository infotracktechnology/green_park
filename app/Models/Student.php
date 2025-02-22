<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Chairmanvideo;
use App\Models\Examportion;
use App\Models\ExamAnswer;


class Student extends Authenticatable
{
    public $table = 'student';

    protected $guarded = [];
    function branch()
    {
        return $this->belongsTo(Branch::class, 'campus', 'id');
    }
    function room()
    {
        return $this->belongsTo(HostelRoom::class, 'room_id', 'id');
    }
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->password_1 = Str::random(8);
            $model->password = bcrypt($model->password_1);
        });
        static::created(function ($model) {
            $model->user_name = 'L' . $model->id;
            $model->save();
        });
    }
   public function announcement()
   {
       return Announcement::where('branch', 'like', "%{$this->campus}%")
           ->where('coaching_type', 'like', "%{$this->coaching_type}%")
           ->where(function ($query) {
               $query->where('gender', $this->gender)
                     ->orWhere('gender', 'All');
           })
           ->latest()
           ->get();
   }

    function chairmanvideo()
    {
        return Chairmanvideo::where('branch_id', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('gender', 'like', "%$this->gender%")->first();
    }
    function examportion()
    {
        return Examportion::where('branch_id', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->first();
    }

   
   
}
