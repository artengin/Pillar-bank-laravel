<?php

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
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
        dd($this->token, config('defaults.auth'));
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
        $response = $this->actingAs(self::$user::find(2))->getJson('/transactions/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testGetTransaction()
    {
        $response = $this->actingAs(self::$user)->getJson('/transactions/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_response', $response->json());
    }

    public static function getSearchFilters(): array
    {
        return [
            [
                'filter' => [
                    'date_from' => '2010-10-20',
                    'date_to' => '2013-10-20',
                ],
                'fixture' => 'search/search_transactions_by_date',
            ],
            [
                'filter' => [
                    'all' => true,
                ]
                ,
                'fixture' => 'search/search_transactions',
            ],
            [
                'filter' => [
                    'type' => 'incoming'
                ],
                'fixture' => 'search/search_transactions_by_type',
            ],
            [
                'filter' => [
                    'card_id' => 2
                ],
                'fixture' => 'search/search_transactions_by_card_id',
            ],
            [
                'filter' => [
                    'per_page' => 2,
                    'page' => 2,
                ],
                'fixture' => 'search/search_per_page',
            ],
            [
                'filter' => [
                    'order_by' => 'name',
                    'desc' => true,
                ],
                'fixture' => 'search/search_transactions_order_by_name',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    public function testSearch($filter, $fixture)
    {
        $response = $this->actingAs(self::$user)->json('get', '/transactions', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchNoAuth()
    {
        $response = $this->getJson('/transactions');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
