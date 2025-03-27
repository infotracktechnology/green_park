<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $table = 'holiday';
    protected $guarded = [];

    function branch()
    {
        if(strpos($this->branch_id, ',')){
            return Branch::whereIn('id', explode(',', $this->branch_id))->get()->implode('name', ', ');
        }
        return Branch::find($this->branch_id)->name ?? '';
    }
    public static function isHoliday($date = '', $branch_id = null, $hostel = null, $gender = null, $section = null){ 
        $date = empty($date) ? date('Y-m-d') : $date;
        $week_of = self::where('attendance_date', $date)->where('branch_id', 'like', "%$branch_id%")->where('hostel', $hostel)->where('gender', $gender)->where('section','like' ,"%$section%")->first();
        if($week_of){ 
            return true; 
        }
        $datetime = $date.' '.date('H:i:s');
        $leave = self::where('start_date', '<=', $datetime)->where('end_date', '>=', $datetime)->first();
        if($leave){
            return true;
        }
        return false;
    }
}
