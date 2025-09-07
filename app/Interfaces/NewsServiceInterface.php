<?php

namespace App\Interfaces;

interface NewsServiceInterface
{
    public function getAllNews();
    public function getNewsById(int $id);
    public function createNews(array $payload);
    public function updateNews(array $payload, int $id);
    public function deleteNews(int $id);
}
