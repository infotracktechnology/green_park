<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PhoneCard extends Model
{
      use HasFactory;

      protected $table = 'phone_card';
      protected $guarded = [];
      protected $casts = [
            'phone_date' => 'date',
      ];


      public function student() {
            return $this->belongsTo(Student::class,'student_id','student_id');
      }
      public function hostel(){
            return $this->belongsTo(Hostel::class, 'hostel_id', 'id');
      }

}
