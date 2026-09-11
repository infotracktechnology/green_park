<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

class StaffAnnouncement extends Model
{
    public $table = 'staff_announcement';

    protected $guarded = [];

     protected $casts = [
        'staff_ids' => 'json',
        'attachment' => 'json',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
    function branch()
    {
        return $this->belongsTo(Branch::class, 'branch', 'id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function branchNames()
    {
        return Branch::whereIn('id', explode(',', $this->branch))->get()->implode('name', '/');
    }
    
    public static function ForStaff(Staff $staff)
    {
        return self::query()
            ->where(function ($mainQuery) use ($staff) {
                $mainQuery->where(function ($q) use ($staff) {
                    $q->where('usertype', 'INDIVIDUAL')
                    ->whereJsonContains('staff_ids', $staff->username);
                })

                ->orWhere(function ($q) use ($staff) {
                    $q->where('usertype', 'GROUP')
                    ->where(function ($branchQuery) use ($staff) {
                        $branchQuery->where('branch', 'like', "%{$staff->branch}%")
                                    ->orWhere('branch', 'like', '%All%');
                    })
                    ->where(function ($departmentQuery) use ($staff) {
                        $departmentQuery->where('department', 'like', "%{$staff->department}%")
                                            ->orWhere('department', 'like', '%All%');
                    });
                });
            })
            ->where(function ($q) {
                $q->where('is_schedule', 0)
                ->orWhere(function ($q2) {
                    $q2->where('is_schedule', 1)
                        ->where('start_at', '<=', now());
                });
            });
    }
    public function StaffList()
    {
        $query = Staff::query();

        if ($this->usertype === 'INDIVIDUAL') {
            return $query
                ->whereIn('username', $this->staff_ids ?? [])
                ->get();
        }

        $departments = explode(',', $this->department ?? '');
        $branches = explode(',', $this->branch ?? '');

        $query->whereIn('department', $departments)
            ->whereIn('branch', $branches);

        return $query->get();
    }
}
