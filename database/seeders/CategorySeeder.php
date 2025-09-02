<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Sports', 'slug' => 'sports', 'description' => 'All about sports news', 'status' => 'active', 'parent_id' => null]);
        Category::create(['name' => 'Soccer', 'slug' => 'soccer', 'description' => 'All about soccer news', 'status' => 'active', 'parent_id' => 1]);
        Category::create(['name' => 'Basketball', 'slug' => 'basketball', 'description' => 'All about basketball news', 'status' => 'active', 'parent_id' => 1]);
    }
}
