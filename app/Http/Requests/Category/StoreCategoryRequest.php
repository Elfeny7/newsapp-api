<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:category,slug',
            'description' => 'required|string',
            'parent_id'   => 'nullable|integer|exists:categories,id',
            'status'      => 'required|string|in:active,inactive',
        ];
    }

    public function getStoreCategoryPayload(): array
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
