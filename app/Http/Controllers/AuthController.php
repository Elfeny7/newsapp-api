<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Interfaces\AuthServiceInterface;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    private AuthServiceInterface $service;

    public function __construct(AuthServiceInterface $service)
    {
        $this->service = $service;
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $this->service->register($request->validated());
        $responseData = [
            'user' => new UserResource($data['user']),
            'token' => $data['token']
        ];
        return ApiResponse::success($responseData, 'Register Successful', 201);
    }

    public function login(LoginUserRequest $request)
    {
        $data = $this->service->login($request->validated());
        $responseData = [
            'user' => new UserResource($data['user']),
            'token' => $data['token'],
            'expires_in' => $data['expires_in']
        ];
        return ApiResponse::success($responseData, 'Login Successful', 200);
    }

    public function logout()
    {
        $this->service->logout();
        return ApiResponse::success('', 'Logout Successful', 200);
    }

    public function getUser()
    {
        $user = new UserResource($this->service->getUser());
        return ApiResponse::success($user, 'User Retrieved', 200);
    }

    public function refresh()
    {
        $data = $this->service->refresh();
        return ApiResponse::success($data, 'Token Refreshed', 200);
    }
}
