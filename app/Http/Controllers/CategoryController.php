<?php

namespace App\Http\Controllers;

use App\Interfaces\CategoryServiceInterface;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Helper\ApiResponse;
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
        $data = $this->categoryServiceInterface->getAllCategory();
        return ApiResponse::success(CategoryResource::collection($data), 'Category data retrieved', 200);
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', 'manage-category');
        $category = $this->categoryServiceInterface->createCategory($request->getStoreCategoryPayload());
        return ApiResponse::success(new CategoryResource($category), 'Category Create successsful', 201);
    }

    public function show(int $id)
    {
        $category = $this->categoryServiceInterface->getCategoryById($id);
        return ApiResponse::success(new CategoryResource($category), 'Category retrieved', 200);
    }

    public function update(UpdateCategoryRequest $request, int $id)
    {
        $this->authorize('update', 'manage-category');
        $this->categoryServiceInterface->updateCategory($request->getUpdateCategoryPayload(), $id);
        return ApiResponse::success('', 'Category Update successsful', 200);
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', 'manage-category');
        $this->categoryServiceInterface->deleteCategory($id);
        return ApiResponse::success('', 'Category Delete successsful', 204);
    }
}
