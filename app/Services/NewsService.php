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
    private NewsRepositoryInterface $newsRepositoryInterface;
    private AuthServiceInterface $authServiceInterface;

    public function __construct(NewsRepositoryInterface $newsRepositoryInterface, AuthServiceInterface $authServiceInterface)
    {
        $this->newsRepositoryInterface = $newsRepositoryInterface;
        $this->authServiceInterface = $authServiceInterface;
    }

    public function getAllNews()
    {
        return $this->newsRepositoryInterface->getAll();
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
                'published_by' => $this->authServiceInterface->getUser()->id,
                'views'   => 0,
                'image'   => $imageName,
            ];
            $news = $this->newsRepositoryInterface->create($newsDetails);

            DB::commit();
            NewsLogger::created($news, $this->authServiceInterface->getUser());

            return $news;
        } catch (\Exception $e) {
            if (!empty($imageName) && Storage::disk('public')->exists('news/' . $imageName)) {
                Storage::disk('public')->delete('news/' . $imageName);
            }

            DB::rollBack();
            NewsLogger::createFailed($payload, $this->authServiceInterface->getUser(), $e);
            throw $e;
        }
    }

    public function getNewsById(int $id)
    {
        return $this->newsRepositoryInterface->getById($id);
    }

    public function updateNews(array $payload, int $id)
    {
        $imageName = null;
        $hasNewImage = false;
        $oldImage = null;

        DB::beginTransaction();

        try {
            $existingNews = $this->newsRepositoryInterface->getById($id);
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
                'published_by' => $this->authServiceInterface->getUser()->id,
                'category_id' => $payload['category_id'] ?? $existingNews->category_id,
            ];

            if ($hasNewImage) {
                $updateDetails['image'] = $imageName;
            }

            $this->newsRepositoryInterface->update($updateDetails, $id);
            DB::commit();

            if ($hasNewImage && $oldImage && Storage::disk('public')->exists('news/' . $oldImage)) {
                Storage::disk('public')->delete('news/' . $oldImage);
            }

            NewsLogger::updated($updateDetails, $existingNews, $this->authServiceInterface->getUser());
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($imageName) && Storage::disk('public')->exists('news/' . $imageName)) {
                Storage::disk('public')->delete('news/' . $imageName);
            }

            NewsLogger::updateFailed($payload, $existingNews, $this->authServiceInterface->getUser(), $e);
            throw $e;
        }
    }

    public function deleteNews(int $id)
    {
        $existingNews = $this->getNewsById($id);
        try {
            DB::transaction(function () use ($id) {
                $this->newsRepositoryInterface->delete($id);
            });

            if ($existingNews->image && Storage::disk('public')->exists('news/' . $existingNews->image)) {
                Storage::disk('public')->delete('news/' . $existingNews->image);
            }

            NewsLogger::deleted($existingNews, $this->authServiceInterface->getUser());
        } catch (\Exception $e) {
            NewsLogger::deleteFailed($existingNews, $this->authServiceInterface->getUser(), $e);
            throw $e;
        }
    }
}
