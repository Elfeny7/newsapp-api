<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Support\ApiResponse;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|unique:categories,slug',
            'description' => 'required|string',
            'parent_id'   => 'nullable|integer|exists:categories,id',
            'status'      => 'required|string|in:active,inactive',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ApiResponse::validationError($validator->errors(), 'Validation errors', 422);
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
