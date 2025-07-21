<?php

namespace App\Tests;

use RonasIT\Support\Testing\ModelTestState;
use RonasIT\Support\Testing\TestCase;
use App\Jobs\HandleTransactionWebhookJob;
use App\Models\Transaction;

class UnitTest extends TestCase
{
    protected static ModelTestState $transactionState;

    public function setUp(): void
    {
        parent::setUp();

        self::$transactionState ??= new ModelTestState(Transaction::class);
    }

    public function testWebhookTransactionsJob()
    {
        $data = $this->getJsonFixture('transaction_webhook_data');

        HandleTransactionWebhookJob::dispatchSync($data);

        self::$transactionState->assertChangesEqualsFixture('transaction_webhook');
    }
}
