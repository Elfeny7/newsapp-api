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
    private UserRepositoryInterface $repo;
    private TokenServiceInterface $service;

    public function __construct(UserRepositoryInterface $repo, TokenServiceInterface $service)
    {
        $this->repo = $repo;
        $this->service = $service;
    }

    public function register(array $payload)
    {
        try {
            $payload['role'] = 'viewer';
            $user = DB::transaction(function () use ($payload) {
                return $this->repo->create($payload);
            });
            $token = $this->service->generate($user);
            AuthLogger::registerSuccess($user);

            return compact('user', 'token');
        } catch (\Exception $e) {
            AuthLogger::registerFailed($payload['email'], $e->getMessage());
            throw $e;
        }
    }

    public function login(array $credentials)
    {
        $token = $this->service->attempt($credentials);
        if (!$token) {
            AuthLogger::loginFailed($credentials['email']);
            throw new InvalidCredentialsException();
        }

        $user = $this->getUser();
        AuthLogger::loginSuccess($user);

        return [
            'user' => $user,
            'token' => $token,
            'expires_in' => $this->service->getTTL(),
        ];
    }

    public function logout()
    {
        try {
            $this->service->invalidate();
            AuthLogger::logoutSuccess($this->getUser());
        } catch (\Exception $e) {
            AuthLogger::logoutFailed($this->getUser(), $e->getMessage());
            throw $e;
        }
    }

    public function getUser()
    {
        $user = $this->service->getUser();
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function refresh()
    {
        $token = $this->service->refresh();
        return [
            'token' => $token,
            'expires_in' => $this->service->getRefreshTTL(),
        ];
    }
}
