<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Hostel extends Model
{
    use HasFactory;

    protected $table = 'hostel';
    protected $guarded = [];

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class);
    }
}
