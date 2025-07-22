<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use RonasIT\Support\Http\BaseResource;

class TransactionResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'card_id' => $this->card_id,
            'card' => $this->card_number,
            'name' => $this->name,
            'amount' => $this->amount,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}
