<?php

namespace App\Services;

use App\Enums\Card\StatusEnum;
use App\Models\Card;
use App\Repositories\CardRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use RonasIT\Support\Services\EntityService;

/**
 * @property CardRepository $repository
 *
 * @mixin CardRepository
 */
class CardService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(CardRepository::class);
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        return $this
            ->searchQuery($filters)
            ->filterByQuery(['name'])
            ->getSearchResults();
    }

    public function issueCard(int $userId, string $cardName): Card
    {
        return $this->create([
            'name' => $cardName,
            'user_id' => $userId,
            'number' => $this->generateCardNumber(),
            'status' => StatusEnum::Active->value,
            'balance' => 0,
            'finished_at' => now()->addYears(config('defaults.card_life_time')),
        ]);
    }

    public function generateCardNumber(): int
    {
        do {
            $cardNumber = mt_rand(1000000000000000, 9999999999999999);
        } while ($this->exists(['number' => $cardNumber]));

        return $cardNumber;
    }

    public function reissue(int $id): void
    {
        $card = $this->find($id);

        $this->update($id, ['status' => StatusEnum::Reissued]);

        $this->create([
            'name' => $card->name,
            'user_id' => $card->user_id,
            'number' => $this->generateCardNumber(),
            'balance' => $card->balance,
            'finished_at' => now()->addYears(config('defaults.card_life_time')),
            'reissued_id' => $card->id,
        ]);
    }
}
