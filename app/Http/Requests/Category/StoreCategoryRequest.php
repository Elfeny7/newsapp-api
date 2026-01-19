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
            'slug'        => 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:categories,slug',
            'description' => 'required|string',
            'parent_id'   => 'nullable|integer|exists:categories,id',
            'status'      => 'required|string|in:active,inactive',
        ];
    }
}
