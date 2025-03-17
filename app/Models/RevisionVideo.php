<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisionVideo extends Model
{
    public $table = 'revision_videos';
    protected $guarded = [];

    public static function boot()
	{
		parent::boot();
    }

}
