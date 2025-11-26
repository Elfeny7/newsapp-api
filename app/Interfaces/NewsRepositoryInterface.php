<?php

namespace App\Interfaces;

interface NewsRepositoryInterface
{
    public function getAll();
    public function getById(int $id);
    public function create(array $newsDetails);
    public function update(array $newsDetails, int $id);
    public function delete(int $id);
}
