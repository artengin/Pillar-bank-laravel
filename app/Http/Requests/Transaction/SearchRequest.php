<?php

namespace App\Http\Requests\Transaction;

use App\Enums\Transaction\TypeEnum;
use App\Http\Requests\Request;
use App\Models\Transaction;

class SearchRequest extends Request
{
    public function rules(): array
    {
        $orderFields = $this->getOrderableFields(Transaction::class);

        return [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1',
            'order_by' => "string|in:{$orderFields}",
            'desc' => 'boolean',
            'all' => 'boolean',
            'card_id' => 'integer',
            'type' => 'string|in:' . TypeEnum::toString(),
            'date_from' => 'date_format:Y-m-d',
            'date_to' => 'date_format:Y-m-d|after_or_equal:date_from',
        ];
    }
}
