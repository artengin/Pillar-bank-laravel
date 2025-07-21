<?php

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Support\Testing\ModelTestState;
use App\Models\User;
use App\Models\Card;
use RonasIT\Support\Traits\MockTrait;

class CardTest extends TestCase
{
    use MockTrait;

    protected static ModelTestState $cardState;
    protected static User $user;

    public function setUp(): void
    {
        parent::setUp();

        self::$cardState ??= new ModelTestState(Card::class);

        self::$user ??= User::find(1);
    }

    public function testIssueNotAuthorizated()
    {
        $response = $this->postJson('/cards');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testIssueWithName()
    {
        $this->mockNativeFunction('App\Services', [
            $this->functionCall(
                name: 'mt_rand',
                arguments: [1000000000000000, 9999999999999999],
                result: 1500001000300200,
            )
        ]);

        $response = $this->actingAs(self::$user)->postJson('/cards', ['name' => 'Test']);

        $response->assertCreated();

        $this->assertEqualsFixture('issue_with_name_response', $response->json());

        self::$cardState->assertChangesEqualsFixture('approve_with_name');
    }

    public function testIssueWithoutName()
    {
        $this->mockNativeFunction('App\Services', [
            $this->functionCall(
                name: 'mt_rand',
                arguments: [1000000000000000, 9999999999999999],
                result: 1500001000300200,
            )
        ]);

        $response = $this->actingAs(self::$user)->postJson('/cards');

        $response->assertCreated();

        $this->assertEqualsFixture('issue_without_name_response', $response->json());

        self::$cardState->assertChangesEqualsFixture('approve_without_name');
    }

    public function testIssueValidationErrors()
    {
        $response = $this->actingAs(self::$user)->postJson('/cards', ['name' => '']);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['name']);
    }

    public function testGetNotAuthorizated()
    {
        $response = $this->getJson('/cards/1');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testGetCardNotFound()
    {
        $response = $this->actingAs(self::$user)->getJson('/cards/9');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Card does not exist']);
    }

    public function testGetForbidden()
    {
        $response = $this->actingAs(self::$user)->getJson('/cards/2');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }


    public function testGet()
    {
        $response = $this->actingAs(self::$user)->getJson('/cards/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_response', $response->json());
    }

    public function testGetValidationErrors()
    {
        $response = $this->actingAs(self::$user)->getJson('/cards/a');

        $response->assertNotFound();

        $response->assertJson(['message' => 'The route cards/a could not be found.']);
    }

    public static function getSearchFilters(): array
    {
        return [
            [
                'filter' => ['query' => 'BankCa'],
                'fixture' => 'search/search_cards_by_name',
            ],
            [
                'filter' => [
                    'order_by' => 'name',
                    'desc' => true,
                ],
                'fixture' => 'search/search_cards_order_by_name',
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
                    'all' => true,
                ],
                'fixture' => 'search/search_all',
            ],
            [
                'filter' => [
                    'per_page' => 1,
                    'query' => 'BankCard',
                ],
                'fixture' => 'search/search_complex',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    public function testSearch($filter, $fixture)
    {
        $response = $this->actingAs(self::$user)->json('get', '/cards', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchNoAuth()
    {
        $response = $this->postJson('/cards');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testFreezeCard()
    {
        $response = $this->actingAs(self::$user)->putJson('/cards/1/freeze');

        $response->assertOk();

        self::$cardState->assertChangesEqualsFixture('freeze_card');
    }

    public function testFreezeFrozenCard()
    {
        $response = $this->actingAs(self::$user->find(4))->putJson('/cards/4/freeze');

        $response->assertBadRequest();

        $response->assertJson(['message' => 'Card is not in Active status']);
    }

    public function testFreezeCardNotFound()
    {
        $response = $this->actingAs(self::$user)->putJson('/cards/0/freeze');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Card does not exist']);
    }

    public function testFreezeCardUnauthorized()
    {
        $response = $this->putJson('/cards/1/freeze');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testAnotherUserFreezeCard()
    {
        $response = $this->actingAs(self::$user)->putJson('/cards/2/freeze');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testUnfreezeCard()
    {
        $response = $this->actingAs(self::$user)->putJson('/cards/6/unfreeze');

        $response->assertOk();

        self::$cardState->assertChangesEqualsFixture('unfreeze_card');
    }

    public function testUnfreezeActiveCard()
    {
        $response = $this->actingAs(self::$user)->putJson('/cards/1/unfreeze');

        $response->assertBadRequest();

        $response->assertJson(['message' => 'Card is not in Freeze status']);
    }

    public function testUnfreezeCardUnauthorized()
    {
        $response = $this->putJson('/cards/1/unfreeze');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testAnotherUserUnfreezeCard()
    {
        $response = $this->actingAs(self::$user)->putJson('/cards/2/unfreeze');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testUnfreezeCardNotFound()
    {
        $response = $this->actingAs(self::$user)->putJson('/cards/0/freeze');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Card does not exist']);
    }

    public function testReissuedCard()
    {
        $this->mockNativeFunction('App\Services', [
            $this->functionCall(
                name: 'mt_rand',
                arguments: [1000000000000000, 9999999999999999],
                result: 1500001000300200,
            )
        ]);

        $response = $this->actingAs(self::$user)->postJson('/card/1/reissue');

        $response->assertOk();

        self::$cardState->assertChangesEqualsFixture('reissued_card');
    }

    public function testReissueOfReissuedCard()
    {
        $response = $this->actingAs(self::$user)->postJson('/card/8/reissue');

        $response->assertBadRequest();

        $response->assertJson(['message' => 'Card is not in Active status']);
    }

    public function testReissueNoAuth()
    {
        $response = $this->postJson('/card/1/reissue');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testReissueCardNotFound()
    {
        $response = $this->actingAs(self::$user)->postJson('/card/0/reissue');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Card does not exist']);
    }

    public function testAnotherUserReissuedCard()
    {
        $response = $this->actingAs(self::$user)->postJson('/card/2/reissue');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }
}
