<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'status'];

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
