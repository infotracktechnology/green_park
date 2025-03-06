<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionKey extends Model
{
    use HasFactory;
    
    protected $table = 'question_key';

    protected $fillable = ['title', 'file_path', 'branch', 'coaching_type'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // function branch()
    // {
    //     if(strpos($this->branch_id, ',')){
    //         return Branch::whereIn('id', explode(',', $this->branch_id))->get()->implode('name', ', ');
    //     }
    //     return Branch::find($this->branch_id)->name;
    // }

    
}

