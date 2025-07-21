<?php

namespace App\Http\Requests\Card;

use App\Http\Requests\Request;
use App\Services\CardService;
use App\Models\Card;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetRequest extends Request
{
    protected ?Card $card;

    public function authorize(): bool
    {
        return $this->card->user_id === $this->user()->id;
    }

    public function validateResolved(): void
    {
        $this->card = app(CardService::class)->find($this->route('id'));

        if (empty($this->card)) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['Entity' => 'Card']));
        }

        parent::validateResolved();
    }
}
