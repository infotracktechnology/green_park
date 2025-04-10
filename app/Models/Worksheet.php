<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worksheet extends Model
{
    use HasFactory;

    protected $table = 'worksheet';
    protected $fillable = ['title', 'file_path', 'branch', 'coaching_type', 'academic_year'];

}  


