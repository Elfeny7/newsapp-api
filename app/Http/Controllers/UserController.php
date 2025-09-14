<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use App\Interfaces\UserServiceInterface;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends Controller
{
    private UserServiceInterface $userServiceInterface;

    public function __construct(UserServiceInterface $userServiceInterface)
    {
        $this->userServiceInterface = $userServiceInterface;
    }

    public function index()
    {
        try {
            $this->authorize('manage', 'manage-user');
            $users = $this->userServiceInterface->getAllUsers();
            return ApiResponse::success(UserResource::collection($users), 'Users Retrieved', 200);
        } catch (AuthorizationException $e) {
            return ApiResponse::throw($e, 'Unauthorized', 403);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function show(int $id)
    {
        try {
            $this->authorize('manage', 'manage-user');
            $user = $this->userServiceInterface->getUserById($id);
            return ApiResponse::success(new UserResource($user), 'User Retrieved', 200);
        } catch (AuthorizationException $e) {
            return ApiResponse::throw($e, 'Unauthorized', 403);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $this->authorize('manage', 'manage-user');
            $user = $this->userServiceInterface->createUser($request->getStorePayload());
            return ApiResponse::success(new UserResource($user), 'User Create successsful', 201);
        } catch (AuthorizationException $e) {
            return ApiResponse::throw($e, 'Unauthorized', 403);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        try {
            $this->authorize('manage', 'manage-user');
            $this->userServiceInterface->updateUser($request->getUpdatePayload(), $id);
            return ApiResponse::success('', 'User Update successsful', 200);
        } catch (AuthorizationException $e) {
            return ApiResponse::throw($e, 'Unauthorized', 403);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->authorize('manage', 'manage-user');
            $this->userServiceInterface->deleteUser($id);
            return ApiResponse::success('', 'User Delete successsful', 204);
        } catch (AuthorizationException $e) {
            return ApiResponse::throw($e, 'Unauthorized', 403);
        } catch (\Exception $e) {
            return ApiResponse::throw($e);
        }
    }
}
