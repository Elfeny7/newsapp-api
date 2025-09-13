<?php

namespace App\Logging;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AuthLogger
{
    public static function createSuccess($payload, $user)
    {
        Log::channel('user')->info('User created', [
            'created_user' => Arr::except($payload, ['password']),
            'action_by' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function createFailed($payload, $message, $user)
    {
        Log::channel('user')->warning('Failed to create user', [
            'uncreated_user' => Arr::except($payload, ['password']) ?? null,
            'action_by' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function updateSuccess($payload, $existingUser, $user)
    {
        Log::channel('user')->info('User updated', [
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
        Log::channel('user')->warning('User update failed', [
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
        Log::channel('user')->info('User deleted', [
            'deleted_user_id' => $id,
            'action_by' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'time' => now()->toDateTimeString()
        ]);
    }

    public static function deleteFailed($id, $message, $user)
    {
        Log::channel('user')->warning('Failed to delete user', [
            'undeleted_user_id' => $id,
            'action_by' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'reason' => $message,
            'time' => now()->toDateTimeString()
        ]);
    }
}
