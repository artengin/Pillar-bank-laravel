<?php

namespace App\Http\Requests\Transaction;

use App\Http\Requests\Request;
use App\Services\TransactionService;
use App\Models\Transaction;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetRequest extends Request
{
    protected ?Transaction $transaction;

    public function authorize(): bool
    {
        return $this->transaction->card->user_id === $this->user()->id;
    }

    public function validateResolved(): void
    {
        $this->transaction = app(TransactionService::class)
            ->with('card')
            ->find($this->route('id'));

        if (empty($this->transaction)) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['Entity' => 'Transaction']));
        }

        parent::validateResolved();
    }
}
