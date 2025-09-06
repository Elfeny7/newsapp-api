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

    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    public function updateUser(array $data, $id)
    {
        return User::whereId($id)->update($data);
    }

    public function deleteUser($id)
    {
        User::destroy($id);
    }
}