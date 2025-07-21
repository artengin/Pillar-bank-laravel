<?php

namespace App\Services;

use App\Enums\Transaction\TypeEnum;
use App\Repositories\TransactionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RonasIT\Support\Services\EntityService;

/**
 * @property TransactionRepository $repository
 *
 * @mixin TransactionRepository
 */
class TransactionService extends EntityService
{
    public function __construct(
        protected CardService $cardService,
    ) {
        $this->setRepository(TransactionRepository::class);
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        return $this
            ->searchQuery($filters)
            ->filterBy('card_id')
            ->filterByQuery(['name'])
            ->getSearchResults();
    }

    public function handleWebhook(array $data)
    {
        $card = $this->cardService->first(['number' => $data['card_number']]);

        DB::transaction(function () use ($card, $data) {
            match (TypeEnum::from($data['type'])) {
                TypeEnum::Incoming => $card->increment('balance', $data['amount']),
                TypeEnum::Outgoing => $card->decrement('balance', $data['amount']),
            };

            $this->create([
                'card_id' => $card->id,
                'card_number' => $data['card_number'],
                'name' => $data['name'],
                'amount' => $data['amount'],
                'type' => $data['type'],
            ]);
        });
    }
}
