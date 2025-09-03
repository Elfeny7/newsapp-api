<?php

namespace App\Repositories;
use App\Models\User;
use App\Interfaces\UserRepositoryInterface;


class UserRepository implements UserRepositoryInterface
{
    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function getAllUsers()
    {
        return User::all();
    }
}