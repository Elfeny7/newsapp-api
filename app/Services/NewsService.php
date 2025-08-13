<?php

namespace App\Services;

use App\Interfaces\NewsServiceInterface;
use App\Interfaces\NewsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class NewsService implements NewsServiceInterface
{
    private NewsRepositoryInterface $newsRepositoryInterface;

    public function __construct(NewsRepositoryInterface $newsRepositoryInterface)
    {
        $this->newsRepositoryInterface = $newsRepositoryInterface;
    }

    public function index()
    {
        $data = $this->newsRepositoryInterface->index();
        return $data;
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
                'content' => $payload['content'],
                'image'   => $imageName
            ];
            $news = $this->newsRepositoryInterface->store($details);

            DB::commit();
            return $news;
        } catch (\Exception $e) {
            if (!empty($imageName) && Storage::disk('public')->exists('news/' . $imageName)) {
                Storage::disk('public')->delete('news/' . $imageName);
            }
            throw $e;
        }
    }

    public function getById(string $id)
    {
        $news = $this->newsRepositoryInterface->getById($id);
        return $news;
    }

    public function updateNews(array $payload, string $id)
    {
        DB::beginTransaction();
        try {
            $existingNews = $this->newsRepositoryInterface->getById($id);

            if (!empty($payload['image']) && $payload['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageName = $payload['image']->hashName();
                Storage::disk('public')->putFileAs('news', $payload['image'], $imageName);

                if ($existingNews->image && Storage::disk('public')->exists('news/' . $existingNews->image)) {
                    Storage::disk('public')->delete('news/' . $existingNews->image);
                }

                $updateDetails = [
                    'title'   => $payload['title'] ?? $existingNews->title,
                    'image'   => $imageName,
                    'content' => $payload['content'] ?? $existingNews->content
                ];
            } else {
                $updateDetails = [
                    'title'   => $payload['title'] ?? $existingNews->title,
                    'content' => $payload['content'] ?? $existingNews->content
                ];
            }

            $this->newsRepositoryInterface->update($updateDetails, $id);
            DB::commit();
        } catch (\Exception $e) {
            if (!empty($imageName) && Storage::disk('public')->exists('news/' . $imageName)) {
                Storage::disk('public')->delete('news/' . $imageName);
            }
            throw $e;
        }
    }

    public function deleteNews(string $id)
    {
        $existingNews = $this->getById($id);
        if ($existingNews->image && Storage::disk('public')->exists('news/' . $existingNews->image)) {
            Storage::disk('public')->delete('news/' . $existingNews->image);
        }
        $this->newsRepositoryInterface->delete($id);
    }
}
