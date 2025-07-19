<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(){
        $news = News::latest()->paginate(5);
        return (new NewsResource(true, 'List of News', $news))
                ->response()
                ->setStatusCode(200);
    }
}
