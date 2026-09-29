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
    try {
        $deviceInfo = $device ?? request()->userAgent() ?? 'Unknown Device';

        return DB::table('user_logs')->insert([
            'user_id'    => $userId,
            'role'       => $role,
            'action'     => $action,
            'device'     => $deviceInfo,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        } catch (\Throwable $e) {
             \Log::error('User log insert failed', [
            'user_id' => $userId,
            'role' => $role,
            'action' => $action,
            'error' => $e->getMessage(),
        ]);
           
            return false;
        }
    }
}