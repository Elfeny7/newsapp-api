<?php

namespace App\Interfaces;

interface CategoryServiceInterface
{
    public function index();
    public function createCategory(array $payload);
    public function getbyId(string $id);
    public function updateCategory(array $payload, string $id);
    public function deleteCategory(string $id);
}