<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Classes\ApiResponseClass;

class UpdateNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'content' => 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ApiResponseClass::validationError($validator->errors(), 'Validation errors', 422);
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
