<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1236743',
            'email' => 'zhora@mail.ru',
            'ssn' => '111-11-2222',
            'password' => 'password',
            'status' => 'approve',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
