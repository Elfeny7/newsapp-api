<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface
{
    public function getAll();
    public function getById(int $id);
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
}
