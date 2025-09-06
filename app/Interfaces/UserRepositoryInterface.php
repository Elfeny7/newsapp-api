<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function createUser(array $data);
    public function getAllUsers();
    public function getUserById($id);
    public function updateUser(array $data, $id);
    public function deleteUser($id);
}
