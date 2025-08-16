<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class NewsLogger
{
    public static function created($news, $user)
    {
        Log::channel('news')->info('News created', [
            'news_id' => $news->id,
            'title'   => $news->title,
            'user_id' => $user->id,
            'email'   => $user->email ?? null,
            'time'    => now()->toDateTimeString()
        ]);
    }

    public static function updated($news, $oldNews, $user)
    {
        Log::channel('news')->info('News updated', [
            'news_id'  => $news->id,
            'title'    => $news->title,
            'user_id'  => $user->id,
            'email'    => $user->email ?? null,
            'old_data' => $oldNews,
            'new_data' => $news,
            'time'     => now()->toDateTimeString(),
        ]);
    }

    public static function deleted($news, $user)
    {
        Log::channel('news')->warning('News deleted', [
            'news_id' => $news->id,
            'title'   => $news->title,
            'user_id' => $user->id,
            'email'   => $user->email ?? null,
            'time'    => now()->toDateTimeString(),
        ]);
    }

    public static function createFailed($payload, $user, \Exception $e)
    {
        Log::channel('news')->error('News creation failed', [
            'title'   => $payload['title'] ?? null,
            'user_id' => $user->id ?? null,
            'email'   => $user->email ?? null,
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'time'    => now()->toDateTimeString(),
        ]);
    }

    public static function updateFailed($oldNews, $payload, $user, \Exception $e)
    {
        Log::channel('news')->error('News update failed', [
            'news_id' => $oldNews->id ?? null,
            'title'   => $oldNews->title ?? $payload['title'] ?? null,
            'user_id' => $user->id ?? null,
            'email'   => $user->email ?? null,
            'payload' => $payload,
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'time'    => now()->toDateTimeString(),
        ]);
    }

    public static function deleteFailed($news, $user, \Exception $e)
    {
        Log::channel('news')->error('News deletion failed', [
            'news_id' => $news->id ?? null,
            'title'   => $news->title ?? null,
            'user_id' => $user->id ?? null,
            'email'   => $user->email ?? null,
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'time'    => now()->toDateTimeString(),
        ]);
    }
}
