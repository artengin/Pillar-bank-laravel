<?php

namespace App\Http\Requests\Card;

use App\Http\Requests\Request;
use App\Models\Card;

class SearchRequest extends Request
{
    public function rules(): array
    {
        $orderFields = $this->getOrderableFields(Card::class);

        return [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1',
            'query' => 'string',
            'order_by' => "string|in:{$orderFields}",
            'desc' => 'boolean',
            'all' => 'boolean'
        ];
    }
}
