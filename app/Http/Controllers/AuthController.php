<?php

namespace App\Http\Controllers;

use App\Enums\User\StatusEnum;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\SuccessLoginResource;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\LockedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, UserService $service): SuccessLoginResource
    {
        $user = $service->register($request->validated());

        if ($user->status === StatusEnum::Reject) {
            throw new LockedHttpException(__('validation.exceptions.user_account_is_blocked'));
        }

        $token = JWTAuth::fromUser($user);

        Auth::login($user);

        return SuccessLoginResource::make($token, $user);
    }

    public function login(LoginRequest $request): SuccessLoginResource
    {
        $credentials = $request->only('phone', 'password');

        $token = JWTAuth::attempt($credentials);

        if ($token === false) {
            throw new UnauthorizedHttpException('jwt-auth', __('auth.failed'));
        }

        $user = Auth::user();

        if ($user->status === StatusEnum::Reject) {
            throw new LockedHttpException(__('validation.exceptions.user_account_is_blocked'));
        }

        return SuccessLoginResource::make($token, $user);
    }
}
