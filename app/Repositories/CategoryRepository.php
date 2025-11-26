<?php

namespace App\Repositories;
use App\Models\Category;
use App\Interfaces\CategoryRepositoryInterface;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll()
    {
        return Category::all();
    }

    public function getById(int $id)
    {
        return Category::findOrFail($id);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update(array $data, int $id)
    {
        return Category::whereId($id)->update($data);
    }

    public function delete(int $id)
    {
        Category::destroy($id);
    }
}
