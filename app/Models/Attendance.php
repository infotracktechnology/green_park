<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * 
     *
     * @var string
     */
    protected $table = 'attendance';

    /**
     * 
     * 
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 
     * 
     *
     * @var array
     */
    protected $casts = [
        'attendance_date' => 'date', 
    ];

    public function student() {
        return $this->belongsTo('App\Models\Student');
    }
}