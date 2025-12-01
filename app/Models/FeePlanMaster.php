<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePlanMaster extends Model
{
    use HasFactory;
    protected $table = 'feeplan_master';
    protected $guarded = [];

    public function feePlanItems()
    {
        return $this->hasMany(FeesPlanItem::class, 'feeplan_master_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }
}
