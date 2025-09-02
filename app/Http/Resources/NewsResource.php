<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'content'      => $this->content,
            'excerpt'      => $this->excerpt,
            'status'       => $this->status,
            'published_at' => $this->published_at,
            'published_by' => $this->published_by,
            'category_id'  => $this->category_id,
            'views'        => $this->views,
            'image'        => $this->image,
        ];
    }
}