<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Hash;


class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|string'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ApiResponse::validationError($validator->errors(), 'Validation errors', 422);
    }

    public function getStorePayload(): array
    {
        return [
            'name'      => $this->input('name'),
            'email'     => $this->input('email'),
            'password'  => Hash::make($this->input('password')),
            'role'      => $this->input('role')
        ];
    }
}
