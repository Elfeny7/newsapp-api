<?php

namespace App\Interfaces;

interface NewsServiceInterface
{
    public function index();
    public function createNews(array $payload);
    public function getbyId(string $id);
    public function updateNews(array $payload, string $id);
    public function deleteNews(string $id);
}
