<?php

namespace App\Tests;

class StatusTest extends TestCase
{
    public function testStatus()
    {
        $this
            ->get('/status')
            ->assertStatus(200);
    }
}
