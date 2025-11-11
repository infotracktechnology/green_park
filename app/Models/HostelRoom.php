<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    use HasFactory;

    protected $table = 'hostel_rooms';
    protected $guarded = [];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

}

