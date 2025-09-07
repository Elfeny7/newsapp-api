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

    public function register(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            try {
                $user = $this->userRepositoryInterface->createUser($payload);
                $token = $this->tokenServiceInterface->generate($user);
                AuthLogger::registerSuccess($user);

                return compact('user', 'token');
            } catch (\Exception $e) {
                AuthLogger::registerFailed($payload['email'], $e->getMessage());
                throw $e;
            }
        });
    }

    public function login(array $credentials)
    {
        $token = $this->tokenServiceInterface->attempt($credentials);
        if (!$token) {
            AuthLogger::loginFailed($credentials['email']);
            throw new InvalidCredentialsException();
        }

        $user = $this->getAuthenticatedUser();
        AuthLogger::loginSuccess($user);

        return [
            'user' => $user,
            'token' => $token,
            'expires_in' => $this->tokenServiceInterface->getTTL(),
        ];
    }

    public function logout()
    {
        try {
            $this->tokenServiceInterface->invalidate();
            AuthLogger::logoutSuccess($this->getAuthenticatedUser());
        } catch (\Exception $e) {
            AuthLogger::logoutFailed($this->getAuthenticatedUser(), $e->getMessage());
            throw $e;
        }
    }

    public function getAllUsers()
    {
        return $this->userRepositoryInterface->getAllUsers();
    }

    public function getUserById(int $id)
    {
        return $this->userRepositoryInterface->getUserById($id);
    }
    
    public function getAuthenticatedUser()
    {
        $user = $this->tokenServiceInterface->getUser();
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function updateUser(array $payload, int $id)
    {
        DB::beginTransaction();
        try {
            $existingUSer = $this->userRepositoryInterface->getUserById($id);
            $updateDetails = [
                'name'   => $payload['name'] ?? $existingUSer->name,
                'email'  => $payload['email'] ?? $existingUSer->email,
                'password'  => $payload['password'] ?? $existingUSer->password,
            ];
            $this->userRepositoryInterface->updateUser($updateDetails, $id);

            DB::commit();
            AuthLogger::updateSuccess($updateDetails, $existingUSer);
        } catch (\Exception $e){

            DB::rollBack();
            AuthLogger::updateFailed($updateDetails, $existingUSer, $e);
            throw $e;
        }
    }

    public function deleteUser(int $id)
    {
        try {
            $this->userRepositoryInterface->deleteUser($id);
            AuthLogger::deleteSuccess($id);
        } catch (\Exception $e) {
            AuthLogger::deleteFailed($id, $e);
            throw $e;
        }
    }
}
