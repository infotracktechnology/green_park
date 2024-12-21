<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends Authenticatable
{
    public $table = 'student';

    protected $guarded = [];
    public static function boot()
	{
		parent::boot();
		static::creating(function($model)
		{
            $model->password_1 = Str::random(8);
            $model->password = bcrypt($model->password_1);
		});
        static::created(function($model) {
            $model->user_name = 'L' . $model->id;
            $model->save();
        });
    }

    
}
