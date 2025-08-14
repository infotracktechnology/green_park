<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


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

	public static function boot()
	{
		parent::boot();
	}
}