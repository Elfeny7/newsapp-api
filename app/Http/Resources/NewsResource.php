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
            'name'         => $this->name,
            'slug'         => $this->slug,
            'content'      => $this->content,
            'excerpt'      => $this->excerpt,
            'status'       => $this->status,
            'published_at' => $this->published_at,
            'category_id'  => $this->category_id,
            'views'        => $this->views,
            'image'        => $this->image,
        ];
    }
}