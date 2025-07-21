<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use RonasIT\Support\Http\BaseResource;

class SuccessLoginResource extends BaseResource
{
    public function __construct(
        protected string $token,
        protected User $user
    ) {
        parent::__construct([]);
    }

    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'ttl' => config('jwt.ttl'),
            'refresh_ttl' => config('jwt.refresh_ttl'),
            'user' => UserResource::make($this->user),
        ];
    }
}
