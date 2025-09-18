<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use App\Interfaces\AuthServiceInterface;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    private AuthServiceInterface $authServiceInterface;

    public function __construct(AuthServiceInterface $authServiceInterface)
    {
        $this->authServiceInterface = $authServiceInterface;
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $this->authServiceInterface->register($request->getRegisterPayload());
        $responseData = [
            'user' => new UserResource($data['user']),
            'token' => $data['token']
        ];
        return ApiResponse::success($responseData, 'Register Successful', 201);
    }

    public function login(LoginUserRequest $request)
    {
        $data = $this->authServiceInterface->login($request->getCredentials());
        $responseData = [
            'user' => new UserResource($data['user']),
            'token' => $data['token'],
            'expires_in' => $data['expires_in']
        ];
        return ApiResponse::success($responseData, 'Login Successful', 200);
    }

    public function logout()
    {
        $this->authServiceInterface->logout();
        return ApiResponse::success('', 'Logout Successful', 200);
    }

    public function getUser()
    {
        $user = new UserResource($this->authServiceInterface->getAuthenticatedUser());
        return ApiResponse::success($user, 'User Retrieved', 200);
    }

    public function refresh()
    {
        $data = $this->authServiceInterface->refresh();
        return ApiResponse::success($data, 'Token Refreshed', 200);
    }
}
