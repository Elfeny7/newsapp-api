<?php

namespace App\Services;

use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Logging\CategoryLogger;
use Illuminate\Support\Facades\DB;


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
        DB::beginTransaction();
        try {
            $data = [
                'name'   => $payload['name'],
                'slug'   => $payload['slug'],
                'description'   => $payload['description'],
                'parent_id' => $payload['parent_id'],
                'status' => $payload['status'],
            ];
            $category = $this->repo->create($data);

            DB::commit();
            CategoryLogger::created($category, $this->auth->getUser());

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
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
        DB::beginTransaction();

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
            DB::commit();

            CategoryLogger::updated($data, $prev, $this->auth->getUser());
        } catch (\Exception $e) {
            DB::rollBack();
            CategoryLogger::updateFailed($payload, $prev, $this->auth->getUser(), $e);

            throw $e;
        }
    }

    public function deleteCategory(int $id)
    {
        $prev = $this->getCategoryById($id);
        try {
            DB::transaction(function () use ($id) {
                $this->repo->delete($id);
            });

            CategoryLogger::deleted($prev, $this->auth->getUser());
        } catch (\Exception $e) {
            CategoryLogger::deleteFailed($prev, $this->auth->getUser(), $e);
            
            throw $e;
        }
    }
}
