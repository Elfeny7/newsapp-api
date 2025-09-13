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
    
    public function getAllUsers()
    {
        return $this->userRepositoryInterface->getAllUsers();
    }

    public function getUserById(int $id)
    {
        return $this->userRepositoryInterface->getUserById($id);
    }

    public function createUser(array $payload)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->createUser($payload);
            DB::commit();
            AuthLogger::createSuccess($payload, $this->getAuthenticatedUser());
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            AuthLogger::createFailed($payload, $e->getMessage(), $this->getAuthenticatedUser());
            throw $e;
        }
    }

    public function updateUser(array $payload, int $id)
    {
        DB::beginTransaction();
        try {
            $existingUser = $this->userRepositoryInterface->getUserById($id);
            $updateDetails = [
                'name'   => $payload['name'] ?? $existingUser->name,
                'email'  => $payload['email'] ?? $existingUser->email,
                'password'  => $payload['password'] ?? $existingUser->password,
                'role'  => $payload['role'] ?? $existingUser->role,
            ];
            $this->userRepositoryInterface->updateUser($updateDetails, $id);

            DB::commit();
            AuthLogger::updateSuccess($updateDetails, $existingUser, $this->getAuthenticatedUser());
        } catch (\Exception $e) {

            DB::rollBack();
            AuthLogger::updateFailed($updateDetails, $existingUser, $e, $this->getAuthenticatedUser());
            throw $e;
        }
    }

    public function deleteUser(int $id)
    {
        try {
            $this->userRepositoryInterface->deleteUser($id);
            AuthLogger::deleteSuccess($id, $this->getAuthenticatedUser());
        } catch (\Exception $e) {
            AuthLogger::deleteFailed($id, $e->getMessage(), $this->getAuthenticatedUser());
            throw $e;
        }
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

    public function getAuthenticatedUser()
    {
        $user = $this->tokenServiceInterface->getUser();
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}
