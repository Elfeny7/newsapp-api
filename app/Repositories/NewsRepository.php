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

    public function create(array $newsDetails)
    {
        return News::create($newsDetails);
    }

    public function update(array $newsDetails, int $id)
    {
        return News::whereId($id)->update($newsDetails);
    }

    public function delete(int $id)
    {
        News::destroy($id);
    }
}
