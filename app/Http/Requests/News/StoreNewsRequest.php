<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image'        => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title'        => 'required|string|max:255',
            'slug'         => 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:news,slug',
            'excerpt'      => 'required|string|max:500',
            'content'      => 'required|string',
            'category_id'  => 'required|integer|exists:categories,id',
            'status'       => 'required|string|in:draft,published',
        ];
    }

    public function getStoreNewsPayload(): array
    {
        return [
            'image'        => $this->file('image'),
            'title'        => $this->input('title'),
            'slug'         => $this->input('slug'),
            'excerpt'      => $this->input('excerpt'),
            'content'      => $this->input('content'),
            'category_id'  => $this->input('category_id'),
            'status'       => $this->input('status'),
            'published_at' => $this->input('status') === 'published' ? now() : null,
        ];
    }
}
