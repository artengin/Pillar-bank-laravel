<?php

namespace App\Jobs;

use App\Services\TransactionService;

class HandleTransactionWebhookJob extends BaseJob
{
    public function __construct(
        public array $data,
    ) {
        $this->onQueue('webhook-transactions');
    }

    public function handle(): void
    {
        app(TransactionService::class)->handleWebhook($this->data);
    }
}
