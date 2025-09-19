<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
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
   

    function attendance()
    {
        return $this->belongsTo(Attendance::class, 'student_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->password_1 = self::generatePassword(6);
            $model->password = bcrypt($model->password_1);
            $model->user_name = self::generateName($model->coaching_type,$model->student_id);
        });
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
    public static function StudentFilterQuery($branch,$course,$type=null,$category=null,$batch=null,$gender=null){
        $query = self::query();
        if($course){
            $query->where('course', $course);
        }
        if($branch){
            $query->whereIn('campus', explode(',', $branch));
        }
        if($type){
            $query->whereIn('coaching_type', explode(',',$type));
        }
        
        if($category){
            $query->whereIn('hostel_dayscholar', explode(',', $category));
        }
        if($batch){
            $query->whereIn('batch', explode(',', $batch));
        }
        if($gender && $gender != 'All'){
            $query->where('gender', $gender);
        }
        return $query;
    }
   
}
