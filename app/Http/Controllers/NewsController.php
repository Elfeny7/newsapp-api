<?php

namespace App\Http\Controllers;

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
        Storage::disk('public')->putFileAs('news', $image, $imageName);
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
        } catch (\Exception $e) {
            Storage::disk('public')->delete('news/' . $imageName);
            return ApiResponseClass::rollback($e);
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
        DB::beginTransaction();
        try {
            $existingNews = $this->newsRepositoryInterface->getById($id);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->hashName();
                Storage::disk('public')->putFileAs('news', $image, $imageName);

                if ($existingNews->image && Storage::disk('public')->exists('news/' . $existingNews->image)) {
                    Storage::disk('public')->delete('news/' . $existingNews->image);
                }

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

            $this->newsRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse('News Update Successful', '', 201);
        } catch (\Exception $e) {

            if (isset($imageName)) {
                Storage::disk('public')->delete('news/' . $imageName);
            }
            return ApiResponseClass::rollback($e);
        }
    }

    public function destroy(string $id)
    {
        $existingNews = $this->newsRepositoryInterface->getById($id);
        if ($existingNews->image && Storage::disk('public')->exists('news/' . $existingNews->image)) {
            Storage::disk('public')->delete('news/' . $existingNews->image);
        }
        $this->newsRepositoryInterface->delete($id);
        return ApiResponseClass::sendResponse('News Delete Successful', '', 204);
    }
}
