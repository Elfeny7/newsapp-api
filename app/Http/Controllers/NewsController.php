<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Interfaces\NewsServiceInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\NewsResource;

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
            $data = $this->newsServiceInterface->index();
            return ApiResponseClass::sendResponse(NewsResource::collection($data), 'News data retrieved', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }

    // public function create() {}

    public function store(StoreNewsRequest $request)
    {
        try {
            $news = $this->newsServiceInterface->createNews($request->getStoreNewsPayload());
            return ApiResponseClass::sendResponse(new NewsResource($news), 'News Create Successful', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }

    public function show(string $id)
    {
        try {
            $news = $this->newsServiceInterface->getById($id);
            return ApiResponseClass::sendResponse(new NewsResource($news), 'News retrieved', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }

    // public function edit(string $id) {}

    public function update(UpdateNewsRequest $request, string $id)
    {
        try {
            $this->newsServiceInterface->updateNews($request->getUpdateNewsPayload(), $id);
            return ApiResponseClass::sendResponse('', 'News Update Successful', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->newsServiceInterface->deleteNews($id);
            return ApiResponseClass::sendResponse('', 'News Delete Successful', 204);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }
}
