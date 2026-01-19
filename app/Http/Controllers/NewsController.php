<?php

namespace App\Http\Controllers;

use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Interfaces\NewsServiceInterface;
use App\Http\Resources\NewsResource;
use App\Http\Responses\ApiResponse;

class NewsController extends Controller
{
    private NewsServiceInterface $service;

    public function __construct(NewsServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getAllNews();
        return ApiResponse::success(NewsResource::collection($data), 'News data retrieved', 200);
    }

    public function store(StoreNewsRequest $request)
    {
        $this->authorize('create', 'manage-news');
        $data = $request->validated();
        $data['image'] = $request->file('image');
        $news = $this->service->createNews($data);
        return ApiResponse::success(new NewsResource($news), 'News Create successsful', 201);
    }

    public function show(int $id)
    {
        $news = $this->service->getNewsById($id);
        return ApiResponse::success(new NewsResource($news), 'News retrieved', 200);
    }

    public function update(UpdateNewsRequest $request, int $id)
    {
        $this->authorize('update',  $this->service->getNewsById($id));
        $data = $request->validated();
        $data['image'] = $request->file('image');
        $this->service->updateNews($request->validated(), $id);
        return ApiResponse::success('', 'News Update successsful', 200);
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', 'manage-news');
        $this->service->deleteNews($id);
        return ApiResponse::success('', 'News Delete successsful', 204);
    }
}
