<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewsApiTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_news_index_returns_paginated_data()
    {
        $response = $this->getJson('/api/news');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data' => ['data', 'current_page', 'last_page', 'per_page', 'total']
                ]);
    }
}
