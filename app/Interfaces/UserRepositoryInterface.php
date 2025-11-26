<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function create(array $data);
    public function getAll();
    public function getById($id);
    public function update(array $data, $id);
    public function delete($id);
}
