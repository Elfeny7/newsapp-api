<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Logging\UserLogger;
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
        return $this->userRepositoryInterface->getAll();
    }

    public function getUserById(int $id)
    {
        return $this->userRepositoryInterface->getById($id);
    }

    public function createUser(array $payload)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->create($payload);
            DB::commit();
            UserLogger::createSuccess($payload, $this->authServiceInterface->getUser());
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            UserLogger::createFailed($payload, $e->getMessage(), $this->authServiceInterface->getUser());
            throw $e;
        }
    }

    public function updateUser(array $payload, int $id)
    {
        DB::beginTransaction();
        try {
            $existingUser = $this->userRepositoryInterface->getById($id);
            $this->userRepositoryInterface->update($payload, $id);
            DB::commit();
            UserLogger::updateSuccess($payload, $existingUser, $this->authServiceInterface->getUser());
        } catch (\Exception $e) {
            DB::rollBack();
            UserLogger::updateFailed($payload, $existingUser, $e->getMessage(), $this->authServiceInterface->getUser());
            throw $e;
        }
    }

    public function deleteUser(int $id)
    {
        try {
            $this->userRepositoryInterface->delete($id);
            UserLogger::deleteSuccess($id, $this->authServiceInterface->getUser());
        } catch (\Exception $e) {
            UserLogger::deleteFailed($id, $e->getMessage(), $this->authServiceInterface->getUser());
            throw $e;
        }
    }
}
