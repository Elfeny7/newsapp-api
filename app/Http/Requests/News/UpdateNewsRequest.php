<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Support\ApiResponse;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ApiResponse::validationError($validator->errors(), 'Validation errors', 422);
    }

    public function getUpdateNewsPayload(): array
    {
        return [
            'title' => $this->input('title'),
            'content' => $this->input('content'),
            'image' => $this->file('image')
        ];
    }
}
