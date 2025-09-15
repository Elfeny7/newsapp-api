<?php

namespace App\Policies;

use App\Models\User;

class CategoryPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'superadmin' || $user->role === 'journalist';
    }

    public function update(User $user): bool
    {
        return $user->role === 'superadmin' || $user->role === 'journalist';
    }

    public function delete(User $user): bool
    {
        return $user->role === 'superadmin';
    }
}
