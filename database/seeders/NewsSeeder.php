<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        News::create([
            'title' => 'Exciting Soccer Match Ends in a Draw',
            'slug' => 'exciting-soccer-match-ends-in-a-draw',
            'image' => 'news1.jpg',
            'excerpt' => 'An exhilarating soccer match between top teams ended in a thrilling draw.',
            'content' => 'In an exciting game that kept fans on the edge of their seats, the two leading soccer teams battled to a 2-2 draw. Key players showcased their skills, with memorable goals and defensive plays. The match highlighted the competitive spirit of the league and left fans eagerly anticipating the next encounter.',
            'views' => 0,
            'status' => 'published',
            'published_at' => now(),
            'category_id' => 2,
            'published_by' => 1,
        ]);
    }
}
