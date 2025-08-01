<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Interfaces\UserRepositoryInterface;

class AuthService
{
    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function register($credentials)
    {
        $user = $this->userRepositoryInterface->createUser($credentials);
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
