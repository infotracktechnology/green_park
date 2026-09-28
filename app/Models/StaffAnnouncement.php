<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Providers\FcmServiceProvider;
use Illuminate\Support\Facades\Log;

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
        $username = $staff->username ?? '';
        $branchId = $staff->branch_id ?? '';
        $department = $staff->department ?? '';

        return self::query()
            ->where(function ($mainQuery) use ($username, $branchId, $department) {
                // Announcements targeted directly at this staff member
                $mainQuery->where(function ($q) use ($username) {
                    $q->where('usertype', 'INDIVIDUAL')
                        ->where(function ($staffQuery) use ($username) {
                            // staff_ids is stored as a JSON array of usernames
                            $staffQuery->where('staff_ids', 'like', '%"' . $username . '"%')
                                ->orWhere('staff_ids', 'like', '%' . $username . '%');
                        });
                })

                // Group announcements broadcast to the staff's branch & department
                ->orWhere(function ($q) use ($branchId, $department) {
                    $q->where('usertype', 'GROUP')
                    ->where(function ($branchQuery) use ($branchId) {
                        $branchQuery->where('branch', 'like', "%{$branchId}%")
                                    ->orWhere('branch', 'like', '%All%');
                    })
                    ->where(function ($departmentQuery) use ($department) {
                        $departmentQuery->where('department', 'like', "%{$department}%")
                                            ->orWhere('department', 'like', '%All%');
                    });
                });
            })
            ->where(function ($q) {
                $q->where(function ($immediate) {
                    // Announcements without scheduling are always visible
                    $immediate->whereNull('is_schedule')->orWhere('is_schedule', 0);
                })
                ->orWhere(function ($q2) {
                    // Scheduled announcements become visible once start_at is reached
                    $q2->where('is_schedule', 1)
                        ->whereNotNull('start_at')
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

    /**
     * Staff this announcement is targeted at (same filtering as ForStaff()).
     *
     * Used to build the FCM recipient list on store.
     */
    public function RecipientStaffs()
    {
        $branches = array_filter(explode(',', (string) $this->branch), fn ($b) => trim($b) !== '');
        $departments = array_filter(explode(',', (string) $this->department), fn ($d) => trim($d) !== '');
        $usernames = $this->staff_ids ?? [];

        return Staff::query()
            ->when($this->usertype === 'INDIVIDUAL', function ($query) use ($usernames) {
                $query->whereIn('username', $usernames);
            }, function ($query) use ($branches, $departments) {
                $query->where(function ($q) use ($branches) {
                    if (!empty($branches)) {
                        $q->whereIn('branch_id', $branches);
                    } else {
                        $q->whereNull('branch_id')->orWhere('branch_id', '');
                    }
                })->where(function ($q) use ($departments) {
                    if (!empty($departments)) {
                        $q->whereIn('department', $departments);
                    } else {
                        $q->whereNull('department')->orWhere('department', '');
                    }
                });
            })
            ->get();
    }

    /**
     * Send this announcement to the filtered staff FCM device tokens.
     * Returns the number of tokens sent to.
     */
    public function NotifyStaffs(FcmServiceProvider $fcm): int
    {
        $tokens = $this->RecipientStaffs()
            ->map(fn ($staff) => $staff->device_token)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($tokens)) {
            return 0;
        }

        $title = 'There is a staff announcement from GPCC';
        $body = $this->title;
        $image = env('APP_LOGO');
        $data = [
            'module' => 'Staff Announcement',
            'staffannouncement_id' => (string) $this->id,
        ];

        // FCM limits a single multicast request to 500 tokens
        foreach (array_chunk($tokens, 500) as $chunk) {
            try {
                $fcm->sendMulticast($chunk, $title, $body, $image, $data, 'Staff Announcements');
            } catch (\Throwable $e) {
                Log::error('Staff announcement FCM error: ' . $e->getMessage(), [
                    'staffannouncement_id' => $this->id,
                ]);
            }
        }

        return count($tokens);
    }
}

