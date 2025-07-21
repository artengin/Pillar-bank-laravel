<?php

namespace App\Repositories;

use App\Models\Transaction;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Transaction $model
 */
class TransactionRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Transaction::class);
    }
}
