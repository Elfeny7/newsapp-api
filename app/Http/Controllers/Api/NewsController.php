<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Services\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }
    
    public function index()
    {
        $news = $this->newsService->getPaginatedNews();
        return (new NewsResource(true, 'List of News', $news))->response()->setStatusCode(200);
    }
}