<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use App\Http\Requests\User\GetProfileRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function profile(GetProfileRequest $request, UserService $service): UserResource
    {
        $user = auth('api')->user();

        $result = $service
            ->with($request->input('with', []))
            ->find($user->id);

        return UserResource::make($result);
    }
}
