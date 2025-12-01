<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillType extends Model
{
    use HasFactory;
    protected $table = 'bill_type';
    protected $guarded = [];

    public function feesPlanItems()
    {
        return $this->hasMany(FeesPlanItem::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccounts::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
