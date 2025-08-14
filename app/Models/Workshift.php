<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshift extends Model
{
    use HasFactory;

    protected $table = 'workshift';
    protected $guarded = [];
    public function staff() {
        return $this->hasMany(Staff::class,'shiftid');
    }

    public function branch() {
        return $this->belongsTo(Branch::class,'branchid');
    }
}  


