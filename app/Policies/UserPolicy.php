<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function manage(User $user)
    {
        return $user->role === 'superadmin';
    }
}
