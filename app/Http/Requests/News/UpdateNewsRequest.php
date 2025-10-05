<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image'        => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|unique:news,slug,' . $this->route('news'),
            'excerpt'      => 'required|string|max:500',
            'content'      => 'required|string',
            'category_id'  => 'required|integer|exists:categories,id',
            'status'       => 'required|string|in:draft,published',
        ];
    }

    public function getUpdateNewsPayload(): array
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
