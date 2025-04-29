<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeesPlan extends Model
{
 

    public $table = 'fees_plan';

    protected $guarded = [];


    function items()
    {
        return $this->hasMany(FeesPlanItem::class, 'plan_id', 'id');
    }
}