<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'image', 'excerpt', 'content', 'views', 'status', 'published_at', 'category_id'];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
