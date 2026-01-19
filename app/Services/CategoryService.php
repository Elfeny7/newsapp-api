<?php

namespace App\Services;

use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Logging\CategoryLogger;

class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $repo;
    private AuthServiceInterface $auth;

    public function __construct(CategoryRepositoryInterface $repo, AuthServiceInterface $auth)
    {
        $this->repo = $repo;
        $this->auth = $auth;
    }

    public function getAllCategory()
    {
        return $this->repo->getAll();
    }

    public function createCategory(array $payload)
    {
        try {
            $data = [
                'name'   => $payload['name'],
                'slug'   => $payload['slug'],
                'description'   => $payload['description'],
                'parent_id' => $payload['parent_id'],
                'status' => $payload['status'],
            ];
            $category = $this->repo->create($data);
            CategoryLogger::created($category, $this->auth->getUser());

            return $category;
        } catch (\Exception $e) {
            CategoryLogger::createFailed($payload, $this->auth->getUser(), $e);

            throw $e;
        }
    }

    public function getCategoryById(int $id)
    {
        return $this->repo->getById($id);
    }

    public function updateCategory(array $payload, int $id)
    {
        try {
            $prev = $this->repo->getById($id);
            $data = [
                'name'   => $payload['name'] ?? $prev->name,
                'slug' => $payload['slug'] ?? $prev->slug,
                'description' => $payload['description'] ?? $prev->description,
                'parent_id' => $payload['parent_id'] ?? $prev->parent_id,
                'status' => $payload['status'] ?? $prev->status,
            ];
            $this->repo->update($data, $id);
            CategoryLogger::updated($data, $prev, $this->auth->getUser());
        } catch (\Exception $e) {
            CategoryLogger::updateFailed($payload, $prev, $this->auth->getUser(), $e);
            
            throw $e;
        }
    }

    public function deleteCategory(int $id)
    {
        $prev = $this->getCategoryById($id);
        try {
            $this->repo->delete($id);
            CategoryLogger::deleted($prev, $this->auth->getUser());
        } catch (\Exception $e) {
            CategoryLogger::deleteFailed($prev, $this->auth->getUser(), $e);
            
            throw $e;
        }
    }
}
