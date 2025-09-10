<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{
    // public function viewAny(User $user): bool
    // {
    //     return false;
    // }

    // public function view(User $user, News $news): bool
    // {
    //     return false;
    // }

    // public function create(User $user): bool
    // {
    //     return false;
    // }

    public function update(User $user, News $news): bool
    {
        return $user->role === 'superadmin' || $user->id === $news->published_by;
    }

    public function delete(User $user, News $news): bool
    {
        return $user->role === 'superadmin' || $user->id === $news->publisher_id;
    }

    // public function restore(User $user, News $news): bool
    // {
    //     return false;
    // }

    // public function forceDelete(User $user, News $news): bool
    // {
    //     return false;
    // }
}
