<?php

namespace App\Http\Controllers;

use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Interfaces\NewsServiceInterface;
use App\Http\Resources\NewsResource;
use App\Support\ApiResponse;

class NewsController extends Controller
{

    private NewsServiceInterface $newsServiceInterface;

    public function __construct(NewsServiceInterface $newsServiceInterface)
    {
        $this->newsServiceInterface = $newsServiceInterface;
    }

    public function index()
    {
        try {
            $data = $this->newsServiceInterface->getAllNews();
            return ApiResponse::success(NewsResource::collection($data), 'News data retrieved', 200);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function store(StoreNewsRequest $request)
    {
        try {
            $news = $this->newsServiceInterface->createNews($request->getStoreNewsPayload());
            return ApiResponse::success(new NewsResource($news), 'News Create successsful', 201);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function show(int $id)
    {
        try {
            $news = $this->newsServiceInterface->getNewsById($id);
            return ApiResponse::success(new NewsResource($news), 'News retrieved', 200);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function update(UpdateNewsRequest $request, int $id)
    {
        try {
            $this->newsServiceInterface->updateNews($request->getUpdateNewsPayload(), $id);
            return ApiResponse::success('', 'News Update successsful', 201);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->newsServiceInterface->deleteNews($id);
            return ApiResponse::success('', 'News Delete successsful', 204);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }
}
