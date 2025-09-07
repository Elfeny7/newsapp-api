<?php

namespace App\Interfaces;

interface NewsRepositoryInterface
{
    public function getAllNews();
    public function getNewsById(int $id);
    public function createNews(array $newsDetails);
    public function updateNews(array $newsDetails, int $id);
    public function deleteNews(int $id);
}
