<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Services\NewsService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NewsApiTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_news_index_returns_paginated_data()
    {
        $response = $this->getJson('/api/news');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
}
