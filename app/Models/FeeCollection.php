<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeCollection extends Model
{

    public $table = 'fee_collection';

    protected $guarded = [];

    public function item()
    {
        return $this->hasMany(FeeCollectionItem::class,'fee_collection_id','id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class,'collected_branch','id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class,'student_id','id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function cancelRequest()
    {
        return $this->hasOne(ReceiptCancellation::class, 'receipt_id');
    }


    protected static function booted()
    {
        static::created(function ($feeCollection) {
            $feeCollection->update([
                'receipt_no' => 'SPECTRA' . str_replace('-', '', $feeCollection->financial_year) . $feeCollection->id
            ]);
        });
    }


}