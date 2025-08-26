<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentConcern extends Model
{
    use HasFactory;

    protected $table = 'parent_concern';
    protected $guarded = [];
    public function student() {
        return $this->belongsTo(Student::class,'student_id','student_id');
    }
}  


