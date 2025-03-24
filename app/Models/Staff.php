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
    ];
	
	public static function boot()
	{
		parent::boot();
	}
}