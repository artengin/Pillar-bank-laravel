<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollectionResource extends ResourceCollection
{
    public $collects = TransactionResource::class;
}
