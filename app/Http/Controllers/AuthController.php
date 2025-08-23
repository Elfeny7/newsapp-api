<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Exceptions\JWTException;
use App\Support\ApiResponse;
use App\Interfaces\AuthServiceInterface;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserNotFoundException;

class AuthController extends Controller
{
    private AuthServiceInterface $authServiceInterface;

    public function __construct(AuthServiceInterface $authServiceInterface)
    {
        $this->authServiceInterface = $authServiceInterface;
    }

    public function register(RegisterUserRequest $request)
    {
        try {
            $data = $this->authServiceInterface->register($request->getRegisterPayload());
            $responseData = [
                'user' => new UserResource($data['user']),
                'token' => $data['token']
            ];
            return ApiResponse::success($responseData, 'Register Successful', 201);
        } catch (JWTException $e) {
            return ApiResponse::throw($e, $e->getMessage() ?: 'Register Failed', 401);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {
            $data = $this->authServiceInterface->login($request->getCredentials());
            $responseData = [
                'user' => new UserResource($data['user']),
                'token' => $data['token'],
                'expires_in' => $data['expires_in']
            ];
            return ApiResponse::success($responseData, 'Login Successful', 200);
        } catch (InvalidCredentialsException $e) {
            return ApiResponse::throw($e, 'Invalid Credentials', 401);
        } catch (JWTException $e) {
            return ApiResponse::throw($e, $e->getMessage() ?: 'Login Failed', 401);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function logout()
    {
        try {
            $this->authServiceInterface->logout();
            return ApiResponse::success('', 'Logout Successful', 200);
        } catch (JWTException $e) {
            return ApiResponse::throw($e, $e->getMessage() ?: 'Logout Failed', 401);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function getUser()
    {
        try {
            $user = new UserResource($this->authServiceInterface->getUser());
            return ApiResponse::success($user, 'User Retrieved', 200);
        } catch (UserNotFoundException $e) {
            return ApiResponse::throw($e, 'User Not Found', 404);
        } catch (JWTException $e) {
            return ApiResponse::throw($e, $e->getMessage() ?: 'Failed to Retrieve User', 401);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }
}
