<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Classes\ApiResponseClass;

class StoreNewsRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required',
            'content' => 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ApiResponseClass::validationError($validator->errors(), 'Validation errors', 422);
    }

    public function getStoreNewsPayload(): array
    {
        return [
            'image' => $this->file('image'),
            'title' => $this->input('title'),
            'content' => $this->input('content')
        ];
    }
}
