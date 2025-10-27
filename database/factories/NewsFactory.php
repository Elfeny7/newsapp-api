<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        if (!Storage::disk('public')->exists('news')) {
            Storage::disk('public')->makeDirectory('news');
        }

        $defaultImage = 'dummy/default.jpg';
        $newFileName = 'news_' . uniqid() . '.jpg';

        if (Storage::disk('public')->exists($defaultImage)) {
            Storage::disk('public')->copy($defaultImage, 'news/' . $newFileName);
        } else {
            $newFileName = 'no-image.jpg';
        }

        $title = fake()->words(2, true);
        return [
            'image' => $newFileName,
            'title' => ucfirst($title),
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(),
            'content' => fake()->sentence(),
            'category_id' => $this->faker->randomElement([1, 2, 3]),
            'status' => $this->faker->randomElement(['draft', 'published']),

        ];
    }
}
