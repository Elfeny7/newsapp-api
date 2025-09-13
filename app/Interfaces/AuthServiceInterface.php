<?php

namespace App\Interfaces;

interface AuthServiceInterface
{
    public function getAllUsers();
    public function getUserById(int $id);
    public function createUser(array $payload);
    public function updateUser(array $payload, int $id);
    public function deleteUser(int $id);
    public function register(array $payload);
    public function login(array $credentials);
    public function logout();
    public function getAuthenticatedUser();
}
