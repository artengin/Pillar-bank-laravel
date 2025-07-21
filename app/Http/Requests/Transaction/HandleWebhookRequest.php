<?php

namespace App\Http\Requests\Transaction;

use App\Enums\Transaction\TypeEnum;
use App\Http\Requests\Request;
use App\Models\Card;
use App\Services\CardService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HandleWebhookRequest extends Request
{
    protected ?Card $card;

    public function authorize(): bool
    {
        return config('defaults.auth') === $this->header('Authorization');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'card_number' => 'required|integer|digits:16',
            'amount' => 'required|integer|min:0',
            'type' => 'required|in:incoming,outgoing',
        ];
    }

    public function validateResolved(): void
    {
        parent::validateResolved();

        $this->card = app(CardService::class)->first(['number' => $this->input('card_number')]);

        $this->checkCardExist();

        $this->checkBalance();
    }

    public function checkCardExist(): void
    {
        if (empty($this->card)) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['Entity' => 'Card']));
        }
    }

    protected function checkBalance(): void
    {
        if (
            $this->type === TypeEnum::Outgoing->value
            && $this->card->balance < $this->input('amount')
        ) {
            throw new BadRequestHttpException(__('validation.exceptions.not_enough_balance'));
        }
    }
}
