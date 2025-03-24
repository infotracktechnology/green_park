<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;
    protected $table = 'download';
    protected $fillable = ['title', 'file_path', 'branch', 'coaching_type', 'academic_year'];

    // protected $casts = [
    //     'branch' => 'array',
    //     'coaching_type' => 'array',
    // ];
    // function branch()
    // {
    //     return $this->belongsTo(Branch::class,'branch_id','id');
    // }
}
