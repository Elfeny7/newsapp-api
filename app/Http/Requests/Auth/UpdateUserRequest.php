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
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|string|email|max:255|unique:users,email,' . $this->route('user'),
            'password' => 'sometimes|string|min:6|confirmed',
            'role'     => 'sometimes|string'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ApiResponse::validationError($validator->errors(), 'Validation errors', 422);
    }

    public function getUpdatePayload(): array
    {
        $payload = $this->only(['name', 'email', 'role']);

        if ($this->filled('password')) {
            $payload['password'] = Hash::make($this->password);
        }

        return $payload;
    }
}
