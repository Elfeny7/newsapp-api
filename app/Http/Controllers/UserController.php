<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Interfaces\UserServiceInterface;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    private UserServiceInterface $userServiceInterface;

    public function __construct(UserServiceInterface $userServiceInterface)
    {
        $this->userServiceInterface = $userServiceInterface;
    }

    public function index()
    {
        $this->authorize('manage', 'manage-user');
        $users = $this->userServiceInterface->getAllUsers();
        return ApiResponse::success(UserResource::collection($users), 'Users Retrieved', 200);
    }

    public function show(int $id)
    {
        $this->authorize('manage', 'manage-user');
        $user = $this->userServiceInterface->getUserById($id);
        return ApiResponse::success(new UserResource($user), 'User Retrieved', 200);
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('manage', 'manage-user');
        $user = $this->userServiceInterface->createUser($request->getStorePayload());
        return ApiResponse::success(new UserResource($user), 'User Create successsful', 201);
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $this->authorize('manage', 'manage-user');
        $this->userServiceInterface->updateUser($request->getUpdatePayload(), $id);
        return ApiResponse::success('', 'User Update successsful', 200);
    }

    public function destroy(int $id)
    {
        $this->authorize('manage', 'manage-user');
        $this->userServiceInterface->deleteUser($id);
        return ApiResponse::success('', 'User Delete successsful', 204);
    }
}
