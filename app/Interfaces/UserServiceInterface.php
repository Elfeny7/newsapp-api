<?php

namespace App\Interfaces;

interface UserServiceInterface
{
    public function getAllUsers();
    public function getUserById(int $id);
    public function createUser(array $payload);
    public function updateUser(array $payload, int $id);
    public function deleteUser(int $id);
}