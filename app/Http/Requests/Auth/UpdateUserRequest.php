<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Hash;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email,' . $this->route('user'),
            'password'  => 'nullable|string|min:6|confirmed',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ApiResponse::validationError($validator->errors(), 'Validation errors', 422);
    }

    public function getUpdatePayload(): array
    {
        $payload = [
            'name'      => $this->input('name'),
            'email'     => $this->input('email'),
        ];

        if ($this->filled('password')) {
            $payload['password'] = Hash::make($this->password);
        }

        return $payload;
    }
}
