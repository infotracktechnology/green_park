<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class Staff extends Model
{
 

    public $table = 'staff';

    protected $guarded = [];

	  // Automatically cast children_details to and from JSON
	  protected $casts = [
         'children_details' => 'json',
		 'class_assign' => 'array',
		 'sub_assign' => 'array',
    ];
	

	public function branch()
	{
		return $this->belongsTo(Branch::class, 'branch_id'); // Ensure 'branch_id' is the correct foreign key
	}

	public function shift()
	{
		return $this->belongsTo(Workshift::class, 'shiftid');
	}
	public function AttendedShift($date = null,$sno=1){
		$date = $date ? Carbon::parse($date) : Carbon::today();
		$suffix = $date->format('n_Y');
		$ontime = $sno == 1 ? $this->shift->session1_ontime : $this->shift->session2_ontime;
		$ontime = Carbon::parse($date->format('Y-m-d') . ' ' . $ontime);
		$attended = DB::connection('epushserver')->table("DeviceLogs_$suffix")->where('UserId', $this->biometric_no)->whereDate('LogDate', $date)->first();
		if($attended){
			$logtime = Carbon::parse($attended->LogDate);
			if($ontime->lte($logtime)){
				return "P";
			}
			return "L";
		}
		return "A";
	}

	public static function boot()
	{
		parent::boot();
	}
}