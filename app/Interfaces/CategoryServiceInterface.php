<?php

namespace App\Interfaces;

interface CategoryServiceInterface
{
    public function getAllCategory();
    public function getCategoryById(int $id);
    public function createCategory(array $payload);
    public function updateCategory(array $payload, int $id);
    public function deleteCategory(int $id);
}