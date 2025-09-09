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
            'role' => $user->role,
            'ip' => request()->ip(),
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function registerSuccess($user)
    {
        Log::channel('auth')->info('User register success', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
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

    public static function updateSuccess($payload, $existingUser)
    {
        Log::channel('auth')->info('User updated', [
            'user_id' => $existingUser->id,
            'email' => $existingUser->email,
            'old_data' => $existingUser,
            'new_data' => $payload,
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function updateFailed($payload, $existingUser, $message)
    {
        Log::channel('auth')->warning('User update failed', [
            'user_id' => $existingUser->id,
            'email' => $existingUser->email,
            'payload' => $payload,
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function deleteSuccess($id)
    {
        Log::channel('auth')->info('User deleted', [
            'user_id' => $id,
            'time' => now()->toDateTimeString()
        ]);
        
    }

    public static function deleteFailed($id, $message)
    {
        Log::channel('auth')->warning('User delete failed', [
            'user_id' => $id,
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }
}
