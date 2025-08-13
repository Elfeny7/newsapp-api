<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Classes\ApiResponseClass;
use App\Interfaces\AuthServiceInterface;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
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
            return ApiResponseClass::sendResponse($responseData, 'Register Successful', 201);
        } catch (JWTException $e) {
            return ApiResponseClass::throw($e, $e->getMessage() ?: 'Register Failed', 401);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
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
            return ApiResponseClass::sendResponse($responseData, 'Login Successful', 200);
        } catch (InvalidCredentialsException $e) {
            return ApiResponseClass::throw($e, 'Invalid Credentials', 401);
        } catch (JWTException $e) {
            return ApiResponseClass::throw($e, $e->getMessage() ?: 'Login Failed', 401);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }

    public function logout()
    {
        try {
            $this->authServiceInterface->logout();
            return ApiResponseClass::sendResponse('', 'Logout Successful', 200);
        } catch (JWTException $e) {
            return ApiResponseClass::throw($e, $e->getMessage() ?: 'Logout Failed', 401);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }

    public function getUser()
    {
        try {
            $user = new UserResource($this->authServiceInterface->getUser());
            return ApiResponseClass::sendResponse($user, 'User Retrieved', 200);
        } catch (UserNotFoundException $e) {
            return ApiResponseClass::throw($e, 'User Not Found', 404);
        } catch (JWTException $e) {
            return ApiResponseClass::throw($e, $e->getMessage() ?: 'Failed to Retrieve User', 401);
        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }
}
