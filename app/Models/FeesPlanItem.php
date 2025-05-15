<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeesPlanItem extends Model
{
 

    public $table = 'feeplan_item';

    protected $guarded = [];
}