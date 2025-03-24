<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $table = 'holiday';
    protected $guarded = [];
    // protected $casts = [
    //     'branch' => 'array',
    //     'coaching_type' => 'array',
    // ];
    // function branch()
    // {
    //     return $this->belongsTo(Branch::class,'branch_id','id');
    // }
}
