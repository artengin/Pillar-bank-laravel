<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use RonasIT\Support\Http\BaseResource;
use App\Http\Resources\Card\CardCollectionResource;

class UserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'ssn' => $this->ssn,
            'cards' => CardCollectionResource::make($this->whenLoaded('cards')),
        ];
    }
}
