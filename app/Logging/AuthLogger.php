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

    public static function createSuccess($payload, $user)
    {
        Log::channel('auth')->info('User created', [
            'created_user' => $payload,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function createFailed($payload, $message, $user)
    {
        Log::channel('auth')->warning('User create failed', [
            'created_user' => $payload ?? null,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function updateSuccess($payload, $existingUser, $user)
    {
        Log::channel('auth')->info('User updated', [
            'old_data' => $existingUser,
            'new_data' => $payload,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function updateFailed($payload, $existingUser, $message, $user)
    {
        Log::channel('auth')->warning('User update failed', [
            'old_data' => $existingUser,
            'new_data' => $payload ?? null,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function deleteSuccess($id, $user)
    {
        Log::channel('auth')->info('User deleted', [
            'deleted_user_id' => $id,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'time' => now()->toDateTimeString()
        ]);
        
    }

    public static function deleteFailed($id, $message, $user)
    {
        Log::channel('auth')->warning('User delete failed', [
            'undeleted_user_id' => $id,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }
}
