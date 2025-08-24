<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class CategoryLogger
{
    public static function created($category, $user)
    {
        Log::channel('category')->info('Category created', [
            'catetgory_id' => $category->id,
            'name'   => $category->name,
            'slug'   => $category->slug,
            'parent_id' => $category->parent_id ?? null,
            'status' => $category->status,
            'user_id'  => $user->id,
            'email'    => $user->email ?? null,
            'time'    => now()->toDateTimeString()
        ]);
    }

    public static function updated($payload, $existingCategory, $user)
    {
        Log::channel('category')->info('Category updated', [
            'name'   => $payload['name'] ?? null,
            'user_id'  => $user->id,
            'email'    => $user->email ?? null,
            'old_data' => $existingCategory,
            'new_data' => $payload,
            'time'     => now()->toDateTimeString(),
        ]);
    }

    public static function deleted($category, $user)
    {
        Log::channel('category')->warning('Category deleted', [
            'category_id' => $category->id,
            'name'   => $category->name,
            'user_id' => $user->id,
            'email'   => $user->email ?? null,
            'time'    => now()->toDateTimeString(),
        ]);
    }

    public static function createFailed($payload, $user, \Exception $e)
    {
        Log::channel('category')->error('Category creation failed', [
            'name'   => $payload['name'] ?? null,
            'user_id' => $user->id ?? null,
            'email'   => $user->email ?? null,
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'time'    => now()->toDateTimeString(),
        ]);
    }

    public static function updateFailed($payload, $existingCategory, $user, \Exception $e)
    {
        Log::channel('category')->error('Category update failed', [
            'category_id' => $existingCategory->id ?? null,
            'name'   => $existingNews->title ?? $payload['name'] ?? null,
            'user_id' => $user->id ?? null,
            'email'   => $user->email ?? null,
            'payload' => $payload,
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'time'    => now()->toDateTimeString(),
        ]);
    }

    public static function deleteFailed($category, $user, \Exception $e)
    {
        Log::channel('category')->error('Category deletion failed', [
            'category_id' => $category->id ?? null,
            'name'   => $category->name ?? null,
            'user_id' => $user->id ?? null,
            'email'   => $user->email ?? null,
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'time'    => now()->toDateTimeString(),
        ]);
    }
}
