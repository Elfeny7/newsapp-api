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

    public static function registerSuccess($user)
    {
        Log::channel('auth')->info('User register success', [
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

    public static function registerFailed($email, $message)
    {
        Log::channel('auth')->warning('User register failed', [
            'email' => $email,
            'ip' => request()->ip(),
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }


    public static function logoutSuccess($user)
    {
        Log::channel('auth')->info('User logged out', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function logoutFailed($user, $message)
    {
        Log::channel('auth')->warning('User logout failed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'reason' => $message,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }
}
