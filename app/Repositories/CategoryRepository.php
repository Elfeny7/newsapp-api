<?php

namespace App\Repositories;
use App\Models\Category;
use App\Interfaces\CategoryRepositoryInterface;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function index()
    {
        return Category::all();
    }

    public function getById($id)
    {
        return Category::findOrFail($id);
    }

    public function store(array $data)
    {
        return Category::create($data);
    }

    public function update(array $data, $id)
    {
        return Category::whereId($id)->update($data);
    }

    public function delete($id)
    {
        Category::destroy($id);
    }
}
