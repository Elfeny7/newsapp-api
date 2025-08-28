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

    public function index()
    {
        return $this->newsRepositoryInterface->index();
    }

    public function createNews(array $payload)
    {
        DB::beginTransaction();
        try {
            $image = $payload['image'];
            $imageName = $image->hashName();
            Storage::disk('public')->putFileAs('news', $image, $imageName);

            $details = [
                'title'   => $payload['title'],
                'category_id' => $payload['category_id'],
                'slug'   => $payload['slug'],
                'excerpt' => $payload['excerpt'],
                'content' => $payload['content'],
                'status'  => $payload['status'],
                'views'   => 0,
                'image'   => $imageName,
            ];
            $news = $this->newsRepositoryInterface->store($details);

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

    public function getById(string $id)
    {
        return $this->newsRepositoryInterface->getById($id);
    }

    public function updateNews(array $payload, string $id)
    {
        DB::beginTransaction();

        try {
            $existingNews = $this->newsRepositoryInterface->getById($id);

            if (!empty($payload['image']) && $payload['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageName = $payload['image']->hashName();
                Storage::disk('public')->putFileAs('news', $payload['image'], $imageName);

                
                $updateDetails = [
                    'title'   => $payload['title'] ?? $existingNews->title,
                    'image'   => $imageName,
                    'slug'    => $payload['slug'] ?? $existingNews->slug,
                    'excerpt' => $payload['excerpt'] ?? $existingNews->excerpt,
                    'content' => $payload['content'] ?? $existingNews->content,
                    'status'  => $payload['status'] ?? $existingNews->status,
                    'category_id' => $payload['category_id'] ?? $existingNews->category_id,
                ];
            } else {
                $updateDetails = [
                    'title'   => $payload['title'] ?? $existingNews->title,
                    'slug'    => $payload['slug'] ?? $existingNews->slug,
                    'excerpt' => $payload['excerpt'] ?? $existingNews->excerpt,
                    'content' => $payload['content'] ?? $existingNews->content,
                    'status'  => $payload['status'] ?? $existingNews->status,
                    'category_id' => $payload['category_id'] ?? $existingNews->category_id,
                ];
            }
            
            $this->newsRepositoryInterface->update($updateDetails, $id);
            DB::commit();

            if ($existingNews->image && Storage::disk('public')->exists('news/' . $existingNews->image)) {
                Storage::disk('public')->delete('news/' . $existingNews->image);
            }

            NewsLogger::updated($updateDetails, $existingNews, $this->authServiceInterface->getUser());
            
        } catch (\Exception $e) {

            if (!empty($imageName) && Storage::disk('public')->exists('news/' . $imageName)) {
                Storage::disk('public')->delete('news/' . $imageName);
            }

            DB::rollBack();
            NewsLogger::updateFailed($payload, $existingNews, $this->authServiceInterface->getUser(), $e);
            throw $e;
        }
    }

    public function deleteNews(string $id)
    {
        $existingNews = $this->getById($id);
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
