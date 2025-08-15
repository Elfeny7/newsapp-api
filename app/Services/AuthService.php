<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Interfaces\TokenServiceInterface;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserNotFoundException;
use App\Logging\AuthLogger;

class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $userRepositoryInterface;
    private TokenServiceInterface $tokenServiceInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, TokenServiceInterface $tokenServiceInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->tokenServiceInterface = $tokenServiceInterface;
    }

    public function register($payload)
    {
        return DB::transaction(function () use ($payload) {
            $user = $this->userRepositoryInterface->createUser($payload);
            $token = $this->tokenServiceInterface->generate($user);
            return compact('user', 'token');
        });
    }

    public function login($credentials)
    {
        $token = $this->tokenServiceInterface->attempt($credentials);
        if (!$token) {
            AuthLogger::loginFailed($credentials['email']);
            throw new InvalidCredentialsException();
        }

        $user = $this->getUser();
        AuthLogger::loginSuccess($user);

        return [
            'user' => $user,
            'token' => $token,
            'expires_in' => $this->tokenServiceInterface->getTTL(),
        ];
    }

    public function logout()
    {
        $this->tokenServiceInterface->invalidate();
    }

    public function getUser()
    {
        $user = $this->tokenServiceInterface->getUser();
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}
