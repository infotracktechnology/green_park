<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Options extends Model
{
 

    public $table = 'options';

    protected $guarded = [];
    protected $casts = [
        'value' => 'array',
    ];
}