<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    public function update(User $user, News $news): bool
    {
        return $user->role === 'superadmin' || $user->id === $news->published_by;
    }

    public function delete(User $user): bool
    {
        return $user->role === 'superadmin';
    }
}
