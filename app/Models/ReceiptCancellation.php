<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptCancellation extends Model
{
    use HasFactory;

    protected $table = 'receipt_cancellations';

    protected $guarded = [];

    public function receipt()
    {
        return $this->belongsTo(FeeCollection::class, 'receipt_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

}
