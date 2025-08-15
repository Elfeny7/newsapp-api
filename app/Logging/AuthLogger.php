<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class AuthLogger
{
    public static function loginSuccess($user)
    {
        Log::channel('auth')->info('User login success', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function loginFailed($email)
    {
        Log::channel('auth')->warning('User login failed', [
            'email' => $email,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function logout($user)
    {
        Log::channel('auth')->info('User logged out', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }
}
