<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
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
            'image'        => 'sometimes|mage|mimes:jpeg,png,jpg,gif|max:2048',
            'title'        => 'sometimes|string|max:255',
            'slug'         => 'sometimes|string|max:255|regex:/^[a-z0-9-]+$/|unique:news,slug,' . $this->route('news'),
            'excerpt'      => 'sometimes|string|max:500',
            'content'      => 'sometimes|string',
            'category_id'  => 'sometimes|integer|exists:categories,id',
            'status'       => 'sometimes|string|in:draft,published',
            'published_at' => 'nullable|date',
        ];
    }
}
