<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;
use RonasIT\Support\Testing\ModelTestState;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

class TransactionTest extends TestCase
{
    protected static User $user;

    protected static ModelTestState $transactionState;

    protected string $token;

    public function setUp(): void
    {
        parent::setUp();

        self::$user ??= User::find(1);

        self::$transactionState ??= new ModelTestState(Transaction::class);

        $this->token = 'bank-system-simulator';
    }

    public function testCreateNoAuth()
    {
        $data = $this->getJsonFixture('transaction_webhook_request');

        $response = $this->json('post', '/webhook/transactions', $data);

        $response->assertForbidden();
    }

    public function testCardNotFound()
    {
        $data = $this->getJsonFixture('card_not_found_request');

        $response = $this->actingAs(self::$user)
            ->withHeaders(['Authorization' => $this->token])
            ->json('post', '/webhook/transactions', $data);

        $response->assertNotFound();

        $response->assertJson(['message' => 'Card does not exist']);
    }

    public function testNotEnoughBalance()
    {
        $data = $this->getJsonFixture('card_not_enough_balance_request');

        $response = $this->actingAs(self::$user)
            ->withHeaders(['Authorization' => $this->token])
            ->json('post', '/webhook/transactions', $data);

        $response->assertBadRequest();

        $response->assertJson(['message' => 'There is insufficient balance on the card']);
    }

    public function testEmptyField()
    {
        $data = $this->getJsonFixture('empty_transaction_field_request');

        $response = $this->actingAs(self::$user)
            ->withHeaders(['Authorization' => $this->token])
            ->json('post', '/webhook/transactions', $data);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The amount field is required.']);
    }

    public function testInvalidTypeTransaction()
    {
        $data = $this->getJsonFixture('invalid_type_transaction_request');

        $response = $this->actingAs(self::$user)
            ->withHeaders(['Authorization' => $this->token])
            ->json('post', '/webhook/transactions', $data);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The selected type is invalid.']);
    }

    public function testHandleWebhook()
    {
        Queue::fake();

        $data = $this->getJsonFixture('transaction_webhook_request');

        $response = $this
            ->actingAs(self::$user)
            ->withHeaders(['Authorization' => $this->token])
            ->json('post', '/webhook/transactions', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertQueueEqualsFixture('handle_transaction_webhook');
    }

    public function testGetNotAuthorizated()
    {
        $response = $this->getJson('/transactions/1');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testGetTransactionNotFound()
    {
        $response = $this->actingAs(self::$user)->getJson('/transactions/9');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Transaction does not exist']);
    }

    public function testGetForbidden()
    {
        $response = $this->actingAs(self::$user)->getJson('/transactions/2');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testGetTransaction()
    {
        $response = $this->actingAs(self::$user)->getJson('/transactions/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_response', $response->json());
    }
}
