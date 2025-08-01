<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterUserRequest $request)
    {
        $credentials = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];

        DB::beginTransaction();
        try {
            $data = $this->authService->register($credentials);
            $responseData = [
                'user' => new UserResource($data['user']),
                'token' => $data['token']
            ];
            DB::commit();
            return ApiResponseClass::sendResponse($responseData, 'Register Successful', 201);
        }catch(JWTException $e){
            return ApiResponseClass::rollback($e);
        }
    }

    public function login()
    {

    }

    public function logout()
    {

    }

    public function getUser()
    {

    }

    public function updateUser()
    {
        
    }
}
