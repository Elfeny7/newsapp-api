<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class NewsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_list_of_news()
    {
        News::factory()->count(3)->create();
        $response = $this->getJson('/api/news');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['*' => ['id', 'image', 'title', 'content'],]
            ]);
    }

    public function test_it_returns_empty_list_when_no_news_exist()
    {
        $response = $this->getJson('/api/news');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => []
            ]);
    }

    public function test_it_can_create_a_news()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('news.jpg');
        $payload = [
            'title' => 'Judul Berita',
            'content' => 'Isi berita yang panjang...',
            'image' => $file,
        ];

        $response = $this->postJson('/api/news', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'image',
                    'content',
                ],
                'message'
            ]);

        $this->assertTrue(Storage::disk('public')->exists('news/' . $file->hashName()));

        $this->assertDatabaseHas('news', [
            'title' => 'Judul Berita',
            'content' => 'Isi berita yang panjang...',
            'image' => $file->hashName(),
        ]);
    }

    public function test_it_returns_validation_errors_when_image_is_missing()
    {
        $payload = [
            'title' => 'Test',
            'content' => 'Test content',
        ];

        $response = $this->postJson('/api/news', $payload);
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation errors',
            ])
            ->assertJsonPath('data.image.0', 'The image field is required.');
    }

    public function test_it_returns_validation_errors_when_title_is_missing_on_store()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('news.jpg');

        $payload = [
            'title' => '',
            'content' => 'Test content',
            'image' => $file
        ];

        $response = $this->postJson('/api/news', $payload);
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation errors',
            ])
            ->assertJsonPath('data.title.0', 'The title field is required.');
    }

    public function test_it_returns_validation_errors_when_content_is_missing_on_store()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('news.jpg');

        $payload = [
            'title' => 'Test',
            'content' => '',
            'image' => $file
        ];

        $response = $this->postJson('/api/news', $payload);
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation errors',
            ])
            ->assertJsonPath('data.content.0', 'The content field is required.');
    }

    public function test_it_returns_validation_errors_when_all_is_missing_on_store()
    {
        $payload = [];

        $response = $this->postJson('/api/news', $payload);
        $response->assertStatus(422)->assertJson([
            'success' => false,
            'message' => 'Validation errors',
        ])->assertJsonPath('data.image.0', 'The image field is required.')
            ->assertJsonPath('data.title.0', 'The title field is required.')
            ->assertJsonPath('data.content.0', 'The content field is required.');
    }

    public function test_it_returns_news_detail()
    {
        $news = News::factory()->create([
            'title' => 'Berita Penting',
            'content' => 'Isi konten penting',
            'image' => 'image.jpg',
        ]);

        $response = $this->getJson('/api/news/' . $news->id);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $news->id,
                    'title' => 'Berita Penting',
                    'content' => 'Isi konten penting',
                    'image' => 'image.jpg',
                ],
            ]);
    }

    public function test_it_can_update_news_with_new_image()
    {
        Storage::fake('public');

        $oldImage = UploadedFile::fake()->image('old.jpg')->storeAs('news', 'old.jpg', 'public');
        $news = News::factory()->create([
            'title' => 'Old Title',
            'content' => 'Old Content',
            'image' => 'old.jpg',
        ]);

        $newImage = UploadedFile::fake()->image('new.jpg');

        $payload = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'image' => $newImage,
        ];

        $response = $this->putJson('/api/news/' . $news->id, $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => 'News Update Successful',
            ]);

        $this->assertTrue(Storage::disk('public')->exists('news/' . $newImage->hashName()));
        $this->assertFalse(Storage::disk('public')->exists('news/old.jpg'));

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'image' => $newImage->hashName(),
        ]);
    }

    public function test_it_can_update_news_without_new_image()
    {
        $news = News::factory()->create([
            'title' => 'Old Title',
            'content' => 'Old Content',
            'image' => 'existing.jpg',
        ]);

        $payload = [
            'title' => 'Changed Title',
            'content' => 'Changed Content',
        ];

        $response = $this->putJson('/api/news/' . $news->id, $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => 'News Update Successful',
            ]);

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Changed Title',
            'content' => 'Changed Content',
            'image' => 'existing.jpg',
        ]);
    }

    public function test_it_returns_validation_errors_when_title_is_missing_on_update()
    {
        $news = News::factory()->create([
            'title' => 'Old Title',
            'content' => 'Old Content',
            'image' => 'existing.jpg',
        ]);

        $payload = [
            'title' => '',
            'content' => 'Changed Content',
            'image' => UploadedFile::fake()->image('news.jpg'),
        ];

        $response = $this->putJson('api/news/' . $news->id, $payload);
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation errors',

            ])->assertJsonPath('data.title.0', 'The title field is required.');
    }

    public function test_it_returns_validation_errors_when_content_is_missing_on_update()
    {
        $news = News::factory()->create([
            'title' => 'Old Title',
            'content' => 'Old content',
            'image' => 'existing.jpg'
        ]);

        $payload = [
            'title' => 'Changed Title',
            'content' => '',
            'image' => UploadedFile::fake()->image('news.jpg'),
        ];

        $response = $this->putJson('api/news/' . $news->id, $payload);
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation errors',
            ])->assertJsonPath('data.content.0', 'The content field is required.');
    }

    public function test_it_returns_validation_errors_when_all_is_missing_on_update()
    {
        $news = News::factory()->create([
            'title' => 'Old title',
            'content' => 'Old content',
            'image' => 'existing.jpg'
        ]);

        $payload = [];

        $response = $this->putJson('api/news/' . $news->id, $payload);
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation errors'
            ])->assertJsonPath('data.title.0', 'The title field is required.')
            ->assertJsonPath('data.content.0', 'The content field is required.');
    }

    public function test_it_can_delete_a_news()
    {
        Storage::fake('public');
        UploadedFile::fake()->image('to-be-deleted.jpg')->storeAs('news', 'to-be-deleted.jpg', 'public');
        $news = News::factory()->create([
            'title' => 'Title',
            'content' => 'Content Example',
            'image' => 'to-be-deleted.jpg'
        ]);
        $this->assertTrue(Storage::disk('public')->exists('news/' . 'to-be-deleted.jpg'));
        $response = $this->deleteJson('api/news/' . $news->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('news', [
            'id' => $news->id,
        ]);
        $this->assertFalse(Storage::disk('public')->exists('news/' . 'to-be-deleted.jpg'));
    }
}
