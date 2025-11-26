<?php

namespace App\Services;

use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Logging\CategoryLogger;
use Illuminate\Support\Facades\DB;


class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $categoryRepositoryInterface;
    private AuthServiceInterface $authServiceInterface;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, AuthServiceInterface $authServiceInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->authServiceInterface = $authServiceInterface;
    }

    public function getAllCategory()
    {
        return $this->categoryRepositoryInterface->getAll();
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
            $category = $this->categoryRepositoryInterface->create($data);

            DB::commit();
            CategoryLogger::created($category, $this->authServiceInterface->getUser());

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            CategoryLogger::createFailed($payload, $this->authServiceInterface->getUser(), $e);

            throw $e;
        }
    }

    public function getCategoryById(int $id)
    {
        return $this->categoryRepositoryInterface->getById($id);
    }

    public function updateCategory(array $payload, int $id)
    {
        DB::beginTransaction();

        try {
            $prev = $this->categoryRepositoryInterface->getById($id);
            $data = [
                'name'   => $payload['name'] ?? $prev->name,
                'slug' => $payload['slug'] ?? $prev->slug,
                'description' => $payload['description'] ?? $prev->description,
                'parent_id' => $payload['parent_id'] ?? $prev->parent_id,
                'status' => $payload['status'] ?? $prev->status,
            ];

            $this->categoryRepositoryInterface->update($data, $id);
            DB::commit();

            CategoryLogger::updated($data, $prev, $this->authServiceInterface->getUser());
        } catch (\Exception $e) {
            DB::rollBack();
            CategoryLogger::updateFailed($payload, $prev, $this->authServiceInterface->getUser(), $e);

            throw $e;
        }
    }

    public function deleteCategory(int $id)
    {
        $prev = $this->getCategoryById($id);
        try {
            DB::transaction(function () use ($id) {
                $this->categoryRepositoryInterface->delete($id);
            });

            CategoryLogger::deleted($prev, $this->authServiceInterface->getUser());
        } catch (\Exception $e) {
            CategoryLogger::deleteFailed($prev, $this->authServiceInterface->getUser(), $e);
            
            throw $e;
        }
    }
}
