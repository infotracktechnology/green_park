<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerKey extends Model
{
    use HasFactory;
    protected $table = 'answer_key';
    protected $fillable = ['title', 'file_path', 'branch', 'coaching_type'];

    // protected $casts = [
    //     'branch' => 'array',
    //     'coaching_type' => 'array',
    // ];
    // function branch()
    // {
    //     return $this->belongsTo(Branch::class,'branch_id','id');
    // }
}
