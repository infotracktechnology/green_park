<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class TimetableAssign extends Model
{
 

    public $table = 'timetable_assign';

    protected $guarded = [];

    protected $casts = [
        'periods' => 'json',
    ];

}