<?php

namespace App\Interfaces;

interface AuthServiceInterface
{
    public function register(array $payload);
    public function login(array $credentials);
    public function logout();
    public function getUser();
    public function refresh();
}
