<?php

namespace App\Repositories;
use App\Models\News;
use App\Interfaces\NewsRepositoryInterface;


class NewsRepository implements NewsRepositoryInterface
{
    public function getAll()
    {
        return News::all();
    }

    public function getById(int $id)
    {
        return News::findOrFail($id);
    }

    public function create(array $data)
    {
        return News::create($data);
    }

    public function update(array $data, int $id)
    {
        $news = News::findOrFail($id);
        $news->update($data);
        return $news;
    }

    public function delete(int $id)
    {
        News::findOrFail($id)->delete();
    }
}
