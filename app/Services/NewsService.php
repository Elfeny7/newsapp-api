<?php

namespace App\Services;

use App\Interfaces\NewsServiceInterface;
use App\Interfaces\NewsRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use App\Logging\NewsLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class NewsService implements NewsServiceInterface
{
    private NewsRepositoryInterface $repo;
    private AuthServiceInterface $auth;

    public function __construct(NewsRepositoryInterface $repo, AuthServiceInterface $auth)
    {
        $this->repo = $repo;
        $this->auth = $auth;
    }

    public function getAllNews()
    {
        return $this->repo->getAll();
    }

    public function createNews(array $payload)
    {
        DB::beginTransaction();
        try {
            $image = $payload['image'];
            $imageName = $image->hashName();
            Storage::disk('public')->putFileAs('news', $image, $imageName);

            $newsDetails = [
                'title'   => $payload['title'],
                'category_id' => $payload['category_id'],
                'slug'   => $payload['slug'],
                'excerpt' => $payload['excerpt'],
                'content' => $payload['content'],
                'status'  => $payload['status'],
                'published_at' => $payload['published_at'],
                'published_by' => $this->auth->getUser()->id,
                'views'   => 0,
                'image'   => $imageName,
            ];
            $news = $this->repo->create($newsDetails);

            DB::commit();
            NewsLogger::created($news, $this->auth->getUser());

            return $news;
        } catch (\Exception $e) {
            if (!empty($imageName) && Storage::disk('public')->exists('news/' . $imageName)) {
                Storage::disk('public')->delete('news/' . $imageName);
            }

            DB::rollBack();
            NewsLogger::createFailed($payload, $this->auth->getUser(), $e);
            throw $e;
        }
    }

    public function getNewsById(int $id)
    {
        return $this->repo->getById($id);
    }

    public function updateNews(array $payload, int $id)
    {
        $imageName = null;
        $hasNewImage = false;
        $oldImage = null;

        DB::beginTransaction();

        try {
            $existingNews = $this->repo->getById($id);
            $oldImage = $existingNews->image;
            $hasNewImage = !empty($payload['image']) && $payload['image'] instanceof \Illuminate\Http\UploadedFile;

            if ($hasNewImage) {
                $imageName = $payload['image']->hashName();
                Storage::disk('public')->putFileAs('news', $payload['image'], $imageName);
            }

            $updateDetails = [
                'title'   => $payload['title'] ?? $existingNews->title,
                'slug'    => $payload['slug'] ?? $existingNews->slug,
                'excerpt' => $payload['excerpt'] ?? $existingNews->excerpt,
                'content' => $payload['content'] ?? $existingNews->content,
                'status'  => $payload['status'] ?? $existingNews->status,
                'published_at' => array_key_exists('published_at', $payload)
                    ? $payload['published_at']
                    : $existingNews->published_at,
                'published_by' => $this->auth->getUser()->id,
                'category_id' => $payload['category_id'] ?? $existingNews->category_id,
            ];

            if ($hasNewImage) {
                $updateDetails['image'] = $imageName;
            }

            $this->repo->update($updateDetails, $id);
            DB::commit();

            if ($hasNewImage && $oldImage && Storage::disk('public')->exists('news/' . $oldImage)) {
                Storage::disk('public')->delete('news/' . $oldImage);
            }

            NewsLogger::updated($updateDetails, $existingNews, $this->auth->getUser());
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($imageName) && Storage::disk('public')->exists('news/' . $imageName)) {
                Storage::disk('public')->delete('news/' . $imageName);
            }

            NewsLogger::updateFailed($payload, $existingNews, $this->auth->getUser(), $e);
            throw $e;
        }
    }

    public function deleteNews(int $id)
    {
        $existingNews = $this->getNewsById($id);
        try {
            DB::transaction(function () use ($id) {
                $this->repo->delete($id);
            });

            if ($existingNews->image && Storage::disk('public')->exists('news/' . $existingNews->image)) {
                Storage::disk('public')->delete('news/' . $existingNews->image);
            }

            NewsLogger::deleted($existingNews, $this->auth->getUser());
        } catch (\Exception $e) {
            NewsLogger::deleteFailed($existingNews, $this->auth->getUser(), $e);
            throw $e;
        }
    }
}
