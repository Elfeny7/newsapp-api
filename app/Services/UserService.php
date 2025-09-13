<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Logging\AuthLogger;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepositoryInterface;
    private AuthServiceInterface $authServiceInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, AuthServiceInterface $authServiceInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->authServiceInterface = $authServiceInterface;
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
            AuthLogger::createSuccess($payload, $this->authServiceInterface->getAuthenticatedUser());
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            AuthLogger::createFailed($payload, $e->getMessage(), $this->authServiceInterface->getAuthenticatedUser());
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
            AuthLogger::updateSuccess($updateDetails, $existingUser, $this->authServiceInterface->getAuthenticatedUser());
        } catch (\Exception $e) {

            DB::rollBack();
            AuthLogger::updateFailed($updateDetails, $existingUser, $e, $this->authServiceInterface->getAuthenticatedUser());
            throw $e;
        }
    }

    public function deleteUser(int $id)
    {
        try {
            $this->userRepositoryInterface->deleteUser($id);
            AuthLogger::deleteSuccess($id, $this->authServiceInterface->getAuthenticatedUser());
        } catch (\Exception $e) {
            AuthLogger::deleteFailed($id, $e->getMessage(), $this->authServiceInterface->getAuthenticatedUser());
            throw $e;
        }
    }
}
