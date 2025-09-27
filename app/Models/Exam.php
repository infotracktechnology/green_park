<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Exam extends Model
{


    public $table = 'exam';

    protected $guarded = [];
    protected $casts = [
        'questions' => 'json',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function branchNames()
    {
        return Branch::whereIn('id', explode(',', $this->branch))->get()->implode('name', '/');
    }

}
