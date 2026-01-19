<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Logging\UserLogger;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $repo;
    private AuthServiceInterface $auth;

    public function __construct(UserRepositoryInterface $repo, AuthServiceInterface $auth)
    {
        $this->repo = $repo;
        $this->auth = $auth;
    }

    public function getAllUsers()
    {
        return $this->repo->getAll();
    }

    public function getUserById(int $id)
    {
        return $this->repo->getById($id);
    }

    public function createUser(array $payload)
    {
        try {
            $user = $this->repo->create($payload);
            UserLogger::createSuccess($payload, $this->auth->getUser());

            return $user;
        } catch (\Exception $e) {
            UserLogger::createFailed($payload, $e->getMessage(), $this->auth->getUser());

            throw $e;
        }
    }

    public function updateUser(array $payload, int $id)
    {
        try {
            $existingUser = $this->repo->getById($id);
            $this->repo->update($payload, $id);
            UserLogger::updateSuccess($payload, $existingUser, $this->auth->getUser());
        } catch (\Exception $e) {
            UserLogger::updateFailed($payload, $existingUser, $e->getMessage(), $this->auth->getUser());

            throw $e;
        }
    }

    public function deleteUser(int $id)
    {
        try {
            $this->repo->delete($id);
            UserLogger::deleteSuccess($id, $this->auth->getUser());
        } catch (\Exception $e) {
            UserLogger::deleteFailed($id, $e->getMessage(), $this->auth->getUser());
            
            throw $e;
        }
    }
}
