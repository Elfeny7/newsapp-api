<?php

namespace App\Http\Controllers;

use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Interfaces\NewsServiceInterface;
use App\Http\Resources\NewsResource;
use App\Helper\ApiResponse;

class NewsController extends Controller
{
    private NewsServiceInterface $newsServiceInterface;

    public function __construct(NewsServiceInterface $newsServiceInterface)
    {
        $this->newsServiceInterface = $newsServiceInterface;
    }

    public function index()
    {
        $data = $this->newsServiceInterface->getAllNews();
        return ApiResponse::success(NewsResource::collection($data), 'News data retrieved', 200);
    }

    public function store(StoreNewsRequest $request)
    {
        $this->authorize('create', 'manage-news');
        $news = $this->newsServiceInterface->createNews($request->getStoreNewsPayload());
        return ApiResponse::success(new NewsResource($news), 'News Create successsful', 201);
    }

    public function show(int $id)
    {
        $news = $this->newsServiceInterface->getNewsById($id);
        return ApiResponse::success(new NewsResource($news), 'News retrieved', 200);
    }

    public function update(UpdateNewsRequest $request, int $id)
    {
        $this->authorize('update',  $this->newsServiceInterface->getNewsById($id));
        $this->newsServiceInterface->updateNews($request->getUpdateNewsPayload(), $id);
        return ApiResponse::success('', 'News Update successsful', 200);
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', 'manage-news');
        $this->newsServiceInterface->deleteNews($id);
        return ApiResponse::success('', 'News Delete successsful', 204);
    }
}
