<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassVideo extends Model
{


    protected $table = 'class_video';

    protected $fillable = [
        'academic_year',
        'subject',
        'chapter',
        'period',
        'video_id',
        'video_url',
        'start_at',
        'end_at',
    ];
}

