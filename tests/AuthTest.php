<?php

namespace App\Tests;

use App\Models\User;
use App\Tests\Support\BankSimulatorMockTrait;
use Illuminate\Support\Facades\Hash;
use RonasIT\Support\Traits\FixturesTrait;
use RonasIT\Support\Traits\MockTrait;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class AuthTest extends TestCase
{
    use MockTrait;
    use BankSimulatorMockTrait;
    use FixturesTrait;

    protected static User $user;

    public function setUp(): void
    {
        parent::setUp();

        self::$user ??= User::find(1);
    }

    public function testRegister()
    {
        $this->mockNativeFunction('App\Services', [
            $this->functionCall(
                name: 'mt_rand',
                arguments: [1000000000000000, 9999999999999999],
                result: 1500001000300201,
            )
        ]);

        JWTAuth::shouldReceive('fromUser')
            ->andReturn('some-token');

        Hash::shouldReceive('make')
            ->with('pillar-bank')
            ->andReturn('token');

        Hash::shouldReceive('make')
            ->with('Password')
            ->andReturn('hashed_password');

        $this->mockHttpRequestService([$this->kycCall(
            requestData: [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '1236743',
                'email' => 'zhora_2@mail.ru',
                'ssn' => '111-11-2222',
            ],
            responseData: [
                'status' => 'approve',
            ]
        )]);

        $response = $this->postJson('/registration', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1236743',
            'email' => 'zhora_2@mail.ru',
            'ssn' => '111-11-2222',
            'password' => 'Password',
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1236743',
            'email' => 'zhora_2@mail.ru',
            'ssn' => '111-11-2222',
            'password' => 'hashed_password',
        ]);

        $this->assertDatabaseHas('cards', [
            'user_id' => 3,
            'name' => 'BankCard',
            'number' => 1500001000300201,
            'balance' => 0,
        ]);

        $response->assertJson([
            'token' => 'some-token',
            'ttl' => 60,
            'refresh_ttl' => 20160,
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '1236743',
                'email' => 'zhora_2@mail.ru',
                'ssn' => '111-11-2222',
            ]
        ]);
    }

    public function testRegisterUserExists()
    {
        User::factory()->create();

        $response = $this->postJson('/registration', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1236743',
            'email' => 'zhora_2@mail.ru',
            'ssn' => '111-11-2222',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors([
            'phone' => 'The phone has already been taken.',
        ]);
    }

    public function testRegisterEmptyRequiredFields()
    {
        $response = $this->postJson('/registration', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => '',
            'phone' => '',
            'ssn' => '',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors([
            'email' => 'The email field is required',
            'phone' => 'The phone field is required',
            'ssn' => 'The ssn field is required',
        ]);
    }

    public function testRegisterInvalidSsn()
    {
        $response = $this->postJson('/registration', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '0123456789',
            'email' => 'zhora_2@mail.ru',
            'ssn' => '111-11321111',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors([
            'ssn' => 'The ssn field format is invalid.',
        ]);
    }
    public function testRegisterReject()
    {
        Hash::shouldReceive('make')
            ->with('pillar-bank')
            ->andReturn('token');

        Hash::shouldReceive('make')
            ->with('Password')
            ->andReturn('hashed_password');

        $this->mockHttpRequestService([$this->kycCall(
            requestData: [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '1236743',
                'email' => 'zhora_2@mail.ru',
                'ssn' => '111-11-1111',
            ],
            responseData: [
                'status' => 'reject',
            ],
            statusCode: Response::HTTP_CREATED,
        )]);

        $response = $this->postJson('/registration', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1236743',
            'email' => 'zhora_2@mail.ru',
            'ssn' => '111-11-1111',
            'password' => 'Password',
        ]);

        $response->assertStatus(Response::HTTP_LOCKED);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1236743',
            'email' => 'zhora_2@mail.ru',
            'ssn' => '111-11-1111',
            'password' => 'hashed_password',
            'status' => 'reject'
        ]);

        $this->assertDatabaseEmpty('cards');

        $content = json_decode($response->getContent(), true);

        $this->assertEqualsFixture('register_with_kyc_reject', $content['message']);
    }

    public function testRegisterWithExistSsn()
    {
        Hash::shouldReceive('make')
            ->with('pillar-bank')
            ->andReturn('token');

        Hash::shouldReceive('make')
            ->with('Password')
            ->andReturn('hashed_password');

        $this->mockHttpRequestService([$this->kycCall(
            requestData: [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '1236743',
                'email' => 'zhora_2@mail.ru',
                'ssn' => '111-11-1112',
            ],
            responseData: [
                'message' => 'SSN exists with a different phone number',
            ],
            statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
        )]);

        $response = $this->postJson('/registration', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1236743',
            'email' => 'zhora_2@mail.ru',
            'ssn' => '111-11-1112',
            'password' => 'Password',
        ]);

        $response->assertStatus(Response::HTTP_FAILED_DEPENDENCY);

        $content = json_decode($response->getContent(), true);

        $this->assertEqualsFixture('register_with_kyc_exception', $content['message']);
    }

    public function testRegisterWithKycUnauthorized()
    {
        Hash::shouldReceive('make')
            ->with('pillar-bank')
            ->andReturn('token');

        Hash::shouldReceive('make')
            ->with('Password')
            ->andReturn('hashed_password');

        $this->mockHttpRequestService([$this->kycCall(
            requestData: [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '1236743',
                'email' => 'zhora_2@mail.ru',
                'ssn' => '111-11-1112',
            ],
            responseData: [
                'message' => 'This action is unauthorized.',
            ],
            statusCode: Response::HTTP_FORBIDDEN
        )]);

        $response = $this->postJson('/registration', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1236743',
            'email' => 'zhora_2@mail.ru',
            'ssn' => '111-11-1112',
            'password' => 'Password',
        ]);

        $response->withHeaders([
            'authorization' => 'wrong_token',
            'content-type' => 'application/json',
            'accept' => 'application/json',
        ]);

        $response->assertStatus(Response::HTTP_FAILED_DEPENDENCY);

        $content = json_decode($response->getContent(), true);

        $this->assertEqualsFixture('register_with_kyc_unauthorized', $content['message']);
    }

    public function testLogin()
    {
        JWTAuth::shouldReceive('attempt')
            ->andReturn('some-token');

        $this->actingAs(self::$user);

        $response = $this->postJson('/login', [
            'phone' => '11111111',
            'password' => 'password',
        ]);

        $response->assertOk();

        $this->assertEqualsFixture('login', $response->json());
    }

    public function testLoginWrongCredentials()
    {
        $response = $this->postJson('/login', [
            'phone' => '2222',
            'password' => 'pass',
        ]);

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'These credentials do not match our records.']);
    }

    public function testLoginAccountBlocked()
    {
        $response = $this->postJson('/login', [
            'phone' => '22222222',
            'password' => 'password',
        ]);

        $response->assertStatus(423);

        $response->assertJson(['message' => 'Your account is blocked.']);
    }

    public function testLoginManyAttempts()
    {
        Redis::flushall();

        Collection::times(10, function () {
            $this->postJson('/login', [
                'phone' => '2222',
                'password' => 'pass',
            ])->assertUnauthorized();
        });

        $response = $this->postJson('/login', [
            'phone' => '2222',
            'password' => 'pass',
        ]);

        $response->assertTooManyRequests();

        $response->assertJson(['message' => 'Too Many Attempts.']);

        Redis::flushall();
    }

    public function testLoginValidationErrors()
    {
        $response = $this->postJson('/login', [
            'phone' => '',
            'password' => '',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['phone', 'password']);
    }
}
