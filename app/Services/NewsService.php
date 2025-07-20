<?php

namespace App\Services;
use App\Models\News;

class NewsService
{
    public function getPaginatedNews($perPage = 5)
    {
        return News::latest()->paginate($perPage);
    }
}
