<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\CardFactory;

class CardSeeder extends Seeder
{
    public function run()
    {
        CardFactory::new()->make([
            'user_id' => \Database\Factories\UserFactory::new()->create()->id,
        ]);
    }
}
