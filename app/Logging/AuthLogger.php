<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class AuthLogger
{
    public static function registerSuccess($user)
    {
        Log::channel('auth')->info('User register success', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function registerFailed($email, $message)
    {
        Log::channel('auth')->warning('Failed to register user', [
            'email' => $email,
            'ip' => request()->ip(),
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function loginSuccess($user)
    {
        Log::channel('auth')->info('User login success', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function loginFailed($email)
    {
        Log::channel('auth')->warning('Failed to login user', [
            'email' => $email,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function logoutSuccess($user)
    {
        Log::channel('auth')->info('User logged out', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function logoutFailed($user, $message)
    {
        Log::channel('auth')->warning('Failed to logout user', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'reason' => $message,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }
}
