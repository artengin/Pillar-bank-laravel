<?php

namespace App\Http\Resources\Card;

use Illuminate\Http\Request;
use RonasIT\Support\Http\BaseResource;

class CardResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'balance' => $this->balance,
            'finished_at' => $this->finished_at
        ];
    }
}
