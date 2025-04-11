<?php
 namespace App\Models;
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Model;
 class HostelAttendance extends Model
 {
     use HasFactory;
     protected $table = 'hostel_attendance'; 
     protected $guarded = []; // Allow all attributes for mass assignment
 }