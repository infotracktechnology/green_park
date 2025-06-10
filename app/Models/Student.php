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
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;


class Student extends Authenticatable
{
    use SoftDeletes;
    public $table = 'student';

    protected $guarded = [];

    protected $casts = [
        'menu' => 'json',
    ];

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
            $maxId = self::max('student_id') ?: 0;
            $model->password_1 = self::generatePassword(6);
            $model->password = bcrypt($model->password_1);
            $model->student_id = ($maxId + 1);
            $model->user_name = 'L'.$model->student_id;
        });
        // static::created(function ($model) {
          
        //     $model->save();
        // });
    }
   public function announcement()
   {
       return Announcement::where('branch', 'like', "%{$this->campus}%")
           ->where('coaching_type', 'like', "%{$this->coaching_type}%")
           ->where('academic_year', $this->academic_year)
           ->where(function ($query) {
               $query->where('gender', $this->gender)
                     ->orWhere('gender', 'All');
           })
           ->latest()
           ->get();
   }

   public function announcement_count(){

       return Announcement::where('branch', 'like', "%{$this->campus}%")
           ->where('coaching_type', 'like', "%{$this->coaching_type}%")
           ->where('academic_year', $this->academic_year)
           ->whereJsonDoesntContain('student_ids', $this->student_id)
           ->where(function ($query) { 
            $query->where('gender', $this->gender)->orWhere('gender', 'All');
            })->count();
   }

    function chairmanvideo()
    {
        return Chairmanvideo::where('branch_id', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('gender', 'like', "%$this->gender%")->where('academic_year', $this->academic_year)->latest()->first();
    }
    function examportion()
    {
        return Examportion::where('branch_id', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('academic_year', $this->academic_year);
    }

    public function answerkey()
    {
        return AnswerKey::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('academic_year', $this->academic_year)->latest()->take(5)->get();
    }

    public function questionkey()
    {
        return QuestionKey::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('academic_year', $this->academic_year)->latest()->take(5)->get();
    }

    public function downloads()
    {
        return Download::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('academic_year', $this->academic_year)->latest()->get();
    }

    public function worksheet()
    {
        return Worksheet::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('academic_year', $this->academic_year)->latest()->get();
    }

    public function achievements()
    {
        return Achievement::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('academic_year', $this->academic_year)->latest()->get();
    }



    public function classvideo($subject = ''){
        $datetime = date('Y-m-d H:i:s');
        return ClassVideo::where('start_at', '<=', $datetime)->where('end_at', '>=', $datetime)->where('subject', $subject)->where('academic_year', $this->academic_year)->get();
    }
    

    public function discussionvideos($subject = '')
    {
        $datetime = date('Y-m-d H:i:s');
        return DiscussionVideo::where('branch', 'like', "%$this->campus%")->where('coaching_type', 'like', "%$this->coaching_type%")->where('start_at', '<=', $datetime)->where('end_at', '>=', $datetime)->where('subject', $subject)->where('academic_year', $this->academic_year)->get();
    }

    public function fees()
    {
        return FeesPlanItem::where('coaching_type', $this->coaching_type)->get()->map(function ($item) {
            $payed = FeeCollectionItem::where('studentid', $this->student_id)->where('feeid', $item->id)->sum('payamount');
            return [
                'id' => $item->id,
                'check' => false,
                'amount' => $item->amount - $payed,
                'instalment' => $item->instalment,
            ];
        });
    }

    public function feespaid()
    {
       $amount = FeesPlanItem::where('coaching_type', $this->coaching_type)->sum('amount');
       $payed = FeeCollectionItem::where('studentid', $this->student_id)->sum('payamount');
       return $amount - $payed;
    }

    private static function generatePassword($length = 6){
        $characters = 'ACFHJKMRXY23456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $password;
    }

    private static function generateID(){
            return $this->max('student_id') + 1;
    }

    private static function generateName($coaching_type,$student_id){
        if($coaching_type == "XI - OB" || $coaching_type == "XII - OB"){
            return 'S'.$student_id;
        }else{
            return 'L'.$student_id;
        }
    }
    public function calculateCurrentMonthStats(string $studentId): object
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $distinctAttendanceSubQuery = Attendance::select('attendance_date', 'timing', 'status')
            ->where('student_id', $studentId)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth]) 
            ->distinct();
        $stats = DB::query()
            ->fromSub($distinctAttendanceSubQuery, 'distinct_attendance')
            ->selectRaw("
                COUNT(DISTINCT attendance_date) as total_days,
                SUM(CASE WHEN status = 'P' THEN 0.5 ELSE 0 END) as present_days
            ")
            ->first();
        $totalDays = $stats->total_days ?? 0;
        $presentDays = $stats->present_days ?? 0;
        $percentage = ($totalDays > 0) 
                      ? round(($presentDays / $totalDays) * 100, 2) 
                      : 0;
        return (object) [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'percentage' => $percentage,
        ];
    }
   
}
