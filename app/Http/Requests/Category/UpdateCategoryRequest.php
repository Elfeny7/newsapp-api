<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|string|max:255',
            'slug'        => 'sometimes|string|max:255|regex:/^[a-z0-9-]+$/|unique:categories,slug,' . $this->route('category'),
            'description' => 'sometimes|string',
            'parent_id'   => 'sometimes|integer|exists:categories,id',
            'status'      => 'sometimes|string|in:active,inactive',
        ];
    }

    public function getUpdateCategoryPayload(): array
    {
        return [
            'name'        => $this->input('name'),
            'slug'        => $this->input('slug'),
            'description' => $this->input('description'),
            'parent_id'   => $this->input('parent_id'),
            'status'      => $this->input('status'),
        ];
    }
}
