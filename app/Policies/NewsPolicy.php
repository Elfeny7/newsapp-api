<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function update(User $user, News $news): bool
    {
        return $user->role === 'superadmin' || $user->id === $news->published_by;
    }
}
