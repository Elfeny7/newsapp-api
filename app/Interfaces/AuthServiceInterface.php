<?php

namespace App\Interfaces;

interface AuthServiceInterface
{
    public function register(array $payload);
    public function login(array $credentials);
    public function logout();
    public function getAuthenticatedUser();
    public function getAllUsers();
    public function getUserById(int $id);
    public function updateUser(array $payload, int $id);
    public function deleteUser(int $id);
}
