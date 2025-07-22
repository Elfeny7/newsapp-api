<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Interfaces\NewsRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{

    private NewsRepositoryInterface $newsRepositoryInterface;

    public function __construct(NewsRepositoryInterface $newsRepositoryInterface)
    {
        $this->newsRepositoryInterface = $newsRepositoryInterface;
    }

    public function index()
    {
        $data = $this->newsRepositoryInterface->index();
        return ApiResponseClass::sendResponse(NewsResource::collection($data), '', 200);
    }

    public function create() {}

    public function store(StoreNewsRequest $request)
    {
        $image = $request->file('image');
        $imageName = $image->hashName();
        $image->storeAs('public/news', $imageName);
        $details = [
            'title' => $request->title,
            'image' => $imageName,
            'content' => $request->content
        ];

        DB::beginTransaction();
        try {
            $news = $this->newsRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new NewsResource($news), 'News Create Successful', 201);
        } catch (\Exception $exc) {
            Storage::delete('public/news' . $imageName);
            return ApiResponseClass::rollback($exc);
        }
    }

    public function show(string $id)
    {
        $news = $this->newsRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new NewsResource($news), '', 200);
    }

    public function edit(string $id) {}

    public function update(UpdateNewsRequest $request, string $id)
    {
        $image = $request->file('image');
        $imageName = $image->hashName();
        $image->storeAs('public/news', $imageName);
        
        if ($request->hasFile('image')) {
            $updateDetails = [
                'title' => $request->title,
                'image' => $imageName,
                'content' => $request->content
            ];
        } else {
            $updateDetails = [
                'title' => $request->title,
                'content' => $request->content
            ];
        }
        
        DB::beginTransaction();
        try {
            $existingNews = $this->newsRepositoryInterface->getById($id);
            Storage::delete('public/news' . $existingNews->image);
            $news = $this->newsRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse(new NewsResource($news), 'News Update Successful', 201);
        } catch (\Exception $exc) {
            Storage::delete('public/news' . $imageName);
            return ApiResponseClass::rollback($exc);
        }
    }

    public function destroy(string $id)
    {
        $existingNews = $this->newsRepositoryInterface->getById($id);
        Storage::delete('public/news' . $existingNews->image);
        $news = $this->newsRepositoryInterface->delete($id);
        return ApiResponseClass::sendResponse(new NewsResource($news), 'News Delete Successful', 204);
    }
}
