<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;

class UserLogServiceProvider
{
    /**
     * Store user login/logout action.
     */
    public static function storelog(int $userId, string $role, string $action, ?string $device = null): bool
    {
        $deviceInfo = $device ?? request()->userAgent() ?? 'Unknown Device';

        return DB::table('user_logs')->insert([
            'user_id'    => $userId,
            'role'       => $role,
            'action'     => $action,
            'device'     => $deviceInfo,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}