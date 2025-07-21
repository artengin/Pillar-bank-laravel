<?php

namespace App\Tests;

use RonasIT\Support\Testing\ModelTestState;
use App\Models\User;

class UserTest extends TestCase
{
    protected static ModelTestState $userState;
    protected static User $user;

    public function setUp(): void
    {
        parent::setUp();

        self::$userState ??= new ModelTestState(User::class);

        self::$user ??= User::find(1);
    }

    public function testGetProfileNotAuthorizated()
    {
        $response = $this->getJson('/profile');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testGetProfile()
    {
        $response = $this->actingAs(self::$user)->json('get', '/profile');

        $response->assertOk();

        $this->assertEqualsFixture('profile_response', $response->json());
    }

    public function testGetProfileWithCards()
    {
        $response = $this->actingAs(self::$user)->json('get', '/profile', [
            'with' => ['cards'],
        ]);

        $response->assertOk();

        $this->assertEqualsFixture('profile_with_cards_response', $response->json());
    }

    public function testGetProfileWithoutCards()
    {
        $user = User::find(2);

        $response = $this->actingAs($user)->json('get', '/profile', [
            'with' => ['cards'],
        ]);

        $response->assertOk();

        $this->assertEqualsFixture('profile_without_cards_response', $response->json());
    }

    public function testGetProfileWrongParameters()
    {
        $response = $this->actingAs(self::$user)->json('get', '/profile', [
            'with' => ['posts'],
        ]);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'Invalid relation requested posts.']);
    }
}
