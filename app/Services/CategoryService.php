<?php

namespace App\Services;

use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Logging\CategoryLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $categoryRepositoryInterface;
    private AuthServiceInterface $authServiceInterface;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, AuthServiceInterface $authServiceInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->authServiceInterface = $authServiceInterface;
    }

    public function index()
    {
        return $this->categoryRepositoryInterface->index();
    }

    public function createCategory(array $payload)
    {
        DB::beginTransaction();
        try {
            $details = [
                'name'   => $payload['name'],
                'slug'   => $payload['slug'],
                'description'   => $payload['description'],
                'parent_id' => $payload['parent_id'],
                'status' => $payload['status'],
            ];
            $category = $this->categoryRepositoryInterface->store($details);

            DB::commit();
            CategoryLogger::created($category, $this->authServiceInterface->getUser());

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            CategoryLogger::createFailed($payload, $this->authServiceInterface->getUser(), $e);

            throw $e;
        }
    }

    public function getById(string $id)
    {
        return $this->categoryRepositoryInterface->getById($id);
    }

    public function updateCategory(array $payload, string $id)
    {
        DB::beginTransaction();

        try {
            $existingCategory = $this->categoryRepositoryInterface->getById($id);
            $updateDetails = [
                'name'   => $payload['name'] ?? $existingCategory->name,
                'slug' => $payload['slug'] ?? $existingCategory->slug,
                'description' => $payload['description'] ?? $existingCategory->description,
                'parent_id' => $payload['parent_id'] ?? $existingCategory->parent_id,
                'status' => $payload['status'] ?? $existingCategory->status,
            ];

            $this->categoryRepositoryInterface->update($updateDetails, $id);
            DB::commit();

            CategoryLogger::updated($updateDetails, $existingCategory, $this->authServiceInterface->getUser());
        } catch (\Exception $e) {
            DB::rollBack();
            CategoryLogger::updateFailed($payload, $existingCategory, $this->authServiceInterface->getUser(), $e);

            throw $e;
        }
    }

    public function deleteCategory(string $id)
    {
        $existingCategory = $this->getById($id);
        try {
            DB::transaction(function () use ($id) {
                $this->categoryRepositoryInterface->delete($id);
            });

            CategoryLogger::deleted($existingCategory, $this->authServiceInterface->getUser());
        } catch (\Exception $e) {
            CategoryLogger::deleteFailed($existingCategory, $this->authServiceInterface->getUser(), $e);
            
            throw $e;
        }
    }
}
