<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Branch extends Model
{
 

    public $table = 'branch';

    protected $guarded = [];

	public static function boot()
	{
		parent::boot();
		static::creating(function($model)
		{
			$user = Auth::user();
			$model->created_by = $user->id;
			$model->updated_by = $user->id;
		});
		static::updating(function($model)
		{
			$user = Auth::user();
			$model->updated_by = $user->id;
		});
	}
}