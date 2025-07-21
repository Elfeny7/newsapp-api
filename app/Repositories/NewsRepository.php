<?php

namespace App\Repositories;
use App\Models\News;
use App\Interfaces\NewsRepositoryInterface;


class NewsRepository implements NewsRepositoryInterface
{
    public function index()
    {
        return News::all();
    }

    public function getById($id)
    {
        return News::findOrFail($id);
    }

    public function store(array $data)
    {
        return News::create($data);
    }

    public function update(array $data, $id)
    {
        return News::whereId($id)->update($data);
    }

    public function delete($id)
    {
        News::destroy($id);
    }
}
