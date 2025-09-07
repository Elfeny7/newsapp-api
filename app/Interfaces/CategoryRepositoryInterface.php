<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface
{
    public function getAllCategory();
    public function getCategoryById(int $id);
    public function createCategory(array $categoryDetails);
    public function updateCategory(array $categoryDetails, int $id);
    public function deleteCategory(int $id);
}
