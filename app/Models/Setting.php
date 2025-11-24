<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Setting extends Model
{
    public $table = 'settings';
    protected $guarded = []; 
}