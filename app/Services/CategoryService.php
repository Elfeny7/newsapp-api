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
        return $this->categoryRepositoryInterface->getAllCategory();
    }

    public function createCategory(array $payload)
    {
        DB::beginTransaction();
        try {
            $categoryDetails = [
                'name'   => $payload['name'],
                'slug'   => $payload['slug'],
                'description'   => $payload['description'],
                'parent_id' => $payload['parent_id'],
                'status' => $payload['status'],
            ];
            $category = $this->categoryRepositoryInterface->createCategory($categoryDetails);

            DB::commit();
            CategoryLogger::created($category, $this->authServiceInterface->getAuthenticatedUser());

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            CategoryLogger::createFailed($payload, $this->authServiceInterface->getAuthenticatedUser(), $e);

            throw $e;
        }
    }

    public function getCategoryById(int $id)
    {
        return $this->categoryRepositoryInterface->getCategoryById($id);
    }

    public function updateCategory(array $payload, int $id)
    {
        DB::beginTransaction();

        try {
            $existingCategory = $this->categoryRepositoryInterface->getCategoryById($id);
            $updateDetails = [
                'name'   => $payload['name'] ?? $existingCategory->name,
                'slug' => $payload['slug'] ?? $existingCategory->slug,
                'description' => $payload['description'] ?? $existingCategory->description,
                'parent_id' => $payload['parent_id'] ?? $existingCategory->parent_id,
                'status' => $payload['status'] ?? $existingCategory->status,
            ];

            $this->categoryRepositoryInterface->updateCategory($updateDetails, $id);
            DB::commit();

            CategoryLogger::updated($updateDetails, $existingCategory, $this->authServiceInterface->getAuthenticatedUser());
        } catch (\Exception $e) {
            DB::rollBack();
            CategoryLogger::updateFailed($payload, $existingCategory, $this->authServiceInterface->getAuthenticatedUser(), $e);

            throw $e;
        }
    }

    public function deleteCategory(int $id)
    {
        $existingCategory = $this->getCategoryById($id);
        try {
            DB::transaction(function () use ($id) {
                $this->categoryRepositoryInterface->deleteCategory($id);
            });

            CategoryLogger::deleted($existingCategory, $this->authServiceInterface->getAuthenticatedUser());
        } catch (\Exception $e) {
            CategoryLogger::deleteFailed($existingCategory, $this->authServiceInterface->getAuthenticatedUser(), $e);
            
            throw $e;
        }
    }
}
