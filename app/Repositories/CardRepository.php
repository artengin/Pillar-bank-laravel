<?php

namespace App\Repositories;

use App\Models\Card;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Card $model
 */
class CardRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Card::class);
    }
}
