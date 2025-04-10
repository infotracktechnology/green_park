<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Chairmanvideo;
use App\Models\Examportion;
use App\Models\ExamAnswer;
use App\Models\AnswerKey;
use App\Models\QuestionKey;
use Illuminate\Database\Eloquent\SoftDeletes;


class Student extends Authenticatable
{
    use SoftDeletes;
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
            $model->password_1 = self::generatePassword(6);
            $model->password = bcrypt($model->password_1);
        });
        static::created(function ($model) {
            $model->user_name = 'L' . $model->student_id;
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
        return Examportion::where('branch_id', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%");
    }

    public function answerkey()
    {
        return AnswerKey::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->latest()->take(5)->get();
    }

    public function questionkey()
    {
        return QuestionKey::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->latest()->take(5)->get();
    }

    public function downloads()
    {
        return Download::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->latest()->get();
    }

    public function worksheet()
    {
        return Worksheet::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->latest()->get();
    }



    public function classvideo($subject = ''){
        $datetime = date('Y-m-d H:i:s');
        return ClassVideo::where('start_at', '<=', $datetime)->where('end_at', '>=', $datetime)->where('subject', $subject)->get();
    }
    

    public function discussionvideos($subject = '')
    {
        $datetime = date('Y-m-d H:i:s');
        return DiscussionVideo::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('start_at', '<=', $datetime)->where('end_at', '>=', $datetime)->where('subject', $subject)->get();
    }

    private static function generatePassword($length = 6){
        $characters = 'ACFHJKMRXY23456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $password;
    }
   
   
}
