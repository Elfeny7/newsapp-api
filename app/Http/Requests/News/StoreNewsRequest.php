<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'published_at' => $this->status === 'published' ? now() : null,
        ]);
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
            'published_at' => 'nullable|date',
        ];
    }
}
