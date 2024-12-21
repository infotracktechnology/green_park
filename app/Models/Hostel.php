<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    // Define the table explicitly (optional if it follows Laravel's naming convention)
    protected $table = 'hostel'; // Ensure this matches your database table name

    // Use guarded or fillable, but not both. Since `guarded` is empty, you allow all attributes for mass assignment.
    protected $guarded = [];

    /**
     * Define the relationship with HostelRoom.
     * 
     * A hostel can have many rooms.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooms()
    {
        return $this->hasMany(HostelRoom::class, 'hostel_id'); // Ensure 'hostel_id' matches the foreign key in the hostel_rooms table
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function staff()
{
    return $this->belongsTo(Staff::class);
}
}
