<?php
namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
 
	use SoftDeletes, HasApiTokens;
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
		return $this->belongsTo(WorkShift::class, 'shiftid');
	}
	// public function AttendedShift($sno=1,$date = null){
	// 	$date = $date ? Carbon::parse($date) : Carbon::now();
	// 	$suffix = $date->format('n_Y');
	// 	$ontime = $sno == 1 ? $this->shift->session1_ontime : $this->shift->session2_ontime;
	
	// 	$attended = DB::connection('epushserver')->table("DeviceLogs_$suffix")->where('UserId', $this->biometric_no)->whereDate('LogDate', $date)->first();

	// 	if($attended){
	// 		$session = DB::connection('epushserver')->table("DeviceLogs_$suffix")->where('UserId', $this->biometric_no)->whereDate('LogDate', $date)->whereTime('LogDate', '<=', $ontime)->first();
	// 		return $session ? "P" : "L";
	// 	}

	// 	return "A";
	// }


	public static function boot()
	{
		parent::boot();
	}
}