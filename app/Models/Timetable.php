<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Timetable extends Model
{
 

    public $table = 'timetable';

    protected $guarded = [];

    protected $casts = [
        'structure' => 'json',
    ];

}