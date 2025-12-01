<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeesPlanItem extends Model
{
 

    public $table = 'feeplan_item';

    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id', 'id');
    }
    public function feeplanmaster()
    {
        return $this->belongsTo(FeePlanMaster::class, 'feeplan_master_id', 'id');
    }
}