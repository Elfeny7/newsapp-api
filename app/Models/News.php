<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title', 'slug', 'image', 'excerpt', 'content', 'views', 'status', 'published_at', 'category_id', 'published_by'];
    protected $casts = ['category_id' => 'integer', 'published_by' => 'integer'];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
