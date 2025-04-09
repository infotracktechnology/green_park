<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SickRoomEntry extends Model
{
    use HasFactory;
    protected $table = 'sick_room_entry';
    protected $guarded = [];
}
