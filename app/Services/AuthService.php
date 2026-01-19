<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Interfaces\TokenServiceInterface;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserNotFoundException;
use App\Logging\AuthLogger;

class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $repo;
    private TokenServiceInterface $tokenService;

    public function __construct(UserRepositoryInterface $repo, TokenServiceInterface $tokenService)
    {
        $this->repo = $repo;
        $this->tokenService = $tokenService;
    }

    public function register(array $payload)
    {
        try {
            $payload['role'] = 'viewer';
            $user = $this->repo->create($payload);
            $token = $this->tokenService->generate($user);
            AuthLogger::registerSuccess($user);
            return compact('user', 'token');
        } catch (\Exception $e) {
            AuthLogger::registerFailed($payload['email'], $e->getMessage());
            throw $e;
        }
    }

    public function login(array $credentials)
    {
        $token = $this->tokenService->attempt($credentials);
        if (!$token) {
            AuthLogger::loginFailed($credentials['email']);
            throw new InvalidCredentialsException();
        }

        $user = $this->getUser();
        AuthLogger::loginSuccess($user);

        return [
            'user' => $user,
            'token' => $token,
            'expires_in' => $this->tokenService->getTTL(),
        ];
    }

    public function logout()
    {
        try {
            $this->tokenService->invalidate();
            AuthLogger::logoutSuccess($this->getUser());
        } catch (\Exception $e) {
            AuthLogger::logoutFailed($this->getUser(), $e->getMessage());
            throw $e;
        }
    }

    public function getUser()
    {
        $user = $this->tokenService->getUser();
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function refresh()
    {
        $token = $this->tokenService->refresh();
        return [
            'token' => $token,
            'expires_in' => $this->tokenService->getRefreshTTL(),
        ];
    }
}
