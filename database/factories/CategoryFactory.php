<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;
    
    public function definition(): array
    {
        $name = fake()->words(2, true);
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'parent_id' => fake()->randomElement([1, 2]),
        ];
    }
}
