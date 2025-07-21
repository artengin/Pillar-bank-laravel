<?php

namespace Database\Factories;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Card;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        $faker = app(Faker::class);

        return [
            'number' => $faker->randomNumber(),
            'balance' => $faker->randomNumber(),
            'user_id' => 1,
            'name' => $faker->name,
            'finished_at' => $faker->dateTime,
        ];
    }
}
