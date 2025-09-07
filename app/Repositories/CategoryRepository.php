<?php

namespace App\Repositories;
use App\Models\Category;
use App\Interfaces\CategoryRepositoryInterface;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategory()
    {
        return Category::all();
    }

    public function getCategoryById(int $id)
    {
        return Category::findOrFail($id);
    }

    public function createCategory(array $categoryDetails)
    {
        return Category::create($categoryDetails);
    }

    public function updateCategory(array $categoryDetails, int $id)
    {
        return Category::whereId($id)->update($categoryDetails);
    }

    public function deleteCategory(int $id)
    {
        Category::destroy($id);
    }
}
