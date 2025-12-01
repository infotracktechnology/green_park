<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccounts extends Model
{
    use HasFactory;
    protected $table = 'bank_accounts';
    protected $guarded = [];

    public function bill_types()
    {
        return $this->hasMany(BillType::class);
    }
}
