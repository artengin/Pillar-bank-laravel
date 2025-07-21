<?php

namespace App\Http\Resources\Card;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CardCollectionResource extends ResourceCollection
{
    public $collects = CardResource::class;
}
