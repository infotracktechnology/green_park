<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;


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

    protected function photo():Attribute {
        return Attribute::make(
            get:fn()=> file_exists(base_path("assets/profilepic/{$this->student_id}.jpg")) ? asset("profilepic/{$this->student_id}.jpg") : asset('img/avather.png'),
        );
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->student_id = self::generateId($model->course);
            $model->password = self::generatePassword(7);
            //$model->password = bcrypt($model->password_1);
            $model->user_name = self::generateName($model->course, $model->student_id);
        });
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('student_name');
        });
    }



    public function feespaidhistory()
    {
        return $this->hasMany(FeeCollection::class, 'student_id', 'id')->with('item');
    }

    public function feespaid()
    {
        $amount = FeesPlanItem::where('coaching_type', $this->coaching_type)->sum('amount');
        $payed = FeeCollectionItem::where('studentid', $this->student_id)->sum('payamount');
        return $amount - $payed;
    }

    private static function generatePassword($length)
    {
        $characters = 'ACFHJKMPRXY23456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $password;
    }

    private static function generateId($course)
    {
        $lastId = self::where('course', $course)->max('student_id');
        $y = date('y')+1;
        if ($lastId) {
            return $lastId+1;
        } else {
            $setting = Setting::firstWhere('key', 'like', "%$course Admission No%");
            $lastId =  $setting->value ?? $y.'00001';
            return $lastId;
        }
    }

    private static function generateName($course, $student_id)
    {
        if ($course == "XI-OB" || $course == "XII-OB") {
            return 'S'.$student_id;
        } else {
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
    public static function StudentFilterQuery($branch, $course, $type = null, $category = null, $batch = null, $gender = null)
    {
        $query = self::query();
        if ($course) {
            $query->where('course', $course);
        }
        if ($branch) {
            $query->whereIn('campus', explode(',', $branch));
        }
        if ($type) {
            $query->whereIn('coaching_type', explode(',', $type));
        }

        if ($category) {
            $query->whereIn('hostel_dayscholar', explode(',', $category));
        }
        if ($batch) {
            $query->whereIn('batch', explode(',', $batch));
        }
        if ($gender && $gender != 'All') {
            $query->where('gender', $gender);
        }
        return $query;
    }

    public  function GetExam()
    {
        return Exam::query()
            ->where(function ($query) {
                $query->where('usertype', 'INDIVIDUAL')
                    ->where('students', $this->student_id)
                    ->whereRaw("date(start_at) = ?", [date('Y-m-d')]);
            })
            ->orWhere(function ($query) {
                $query->where('academic_year', $this->academic_year)
                    ->where('course', $this->course)
                    ->where('branch', 'like', "%{$this->campus}%")
                    ->where('coaching_type', 'like', "%{$this->coaching_type}%")
                    ->when($this->coaching_type === 'OFFLINE', function ($q) {
                        if (in_array($this->course, ['NEET', 'JEE'])) {
                            if (in_array($this->campus, [1, 4, 5])) {
                                $q->where('category', 'like', "%{$this->hostel_dayscholar}%");
                            }
                            $q->where('batch', 'like', "%{$this->batch}%");
                        }
                        $q->where('section', 'like', "%{$this->section}%");
                    })
                    ->whereIn('gender', [$this->gender, 'All'])
                    ->whereRaw("date(start_at) = ?", [date('Y-m-d')]);
            })->first();
    }
}
