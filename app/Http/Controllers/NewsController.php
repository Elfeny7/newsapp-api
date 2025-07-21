<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Interfaces\NewsRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{

    private NewsRepositoryInterface $newsRepositoryInterface;

    public function __construct(NewsRepositoryInterface $newsRepositoryInterface)
    {
        $this->newsRepositoryInterface = $newsRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->newsRepositoryInterface->index();
        return ApiResponseClass::sendResponse(NewsResource::collection($data),'',200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        $details = [
            'title' => $request->title,
            'image' => $request->image,
            'content' => $request->content
        ];
        DB::beginTransaction();
        try {
            $news = $this->newsRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new NewsResource($news), 'News Create Successful', 201);
        } catch(\Exception $exc) {
            return ApiResponseClass::rollback($exc);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
