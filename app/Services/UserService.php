<?php

namespace App\Services;

use App\Enums\User\StatusEnum;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Support\BankSimulatorApiService;
use Illuminate\Support\Facades\Hash;
use RonasIT\Support\Services\EntityService;

/**
 * @property UserRepository $repository
 *
 * @mixin UserRepository
 */
class UserService extends EntityService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected CardService $cardService,
        protected BankSimulatorApiService $bankSimulatorApiService,
    ) {
        $this->setRepository(UserRepository::class);
    }

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        $data['status'] = $this->bankSimulatorApiService->verifyUser($data);

        $user = $this->create($data);

        if ($user->status === StatusEnum::Approve) {
            $this->cardService->issueCard($user->id, config('defaults.card_name'));
        }

        return $user;
    }
}
