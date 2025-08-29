<?php

namespace App\Http\Controllers;

use App\Interfaces\CategoryServiceInterface;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Support\ApiResponse;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    private CategoryServiceInterface $categoryServiceInterface;

    public function __construct(CategoryServiceInterface $categoryServiceInterface)
    {
        $this->categoryServiceInterface = $categoryServiceInterface;
    }

    public function index()
    {
        try {
            $data = $this->categoryServiceInterface->index();
            return ApiResponse::success(CategoryResource::collection($data), 'Category data retrieved', 200);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->categoryServiceInterface->createCategory($request->getStoreCategoryPayload());
            return ApiResponse::success(new CategoryResource($category), 'Category Create successsful', 201);
        } catch (\Exception $e) {
        }
    }

    public function show(string $id)
    {
        try {
            $category = $this->categoryServiceInterface->getById($id);
            return ApiResponse::success(new CategoryResource($category), 'Category retrieved', 200);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function update(UpdateCategoryRequest $request, string $id)
    {
        try {
            $category = $this->categoryServiceInterface->updateCategory($request->getUpdateCategoryPayload(), $id);
            return ApiResponse::success(new CategoryResource($category), 'Category Update successsful', 201);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->categoryServiceInterface->deleteCategory($id);
            return ApiResponse::success('', 'Category Delete successsful', 204);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }
}
