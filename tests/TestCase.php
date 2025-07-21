<?php

namespace App\Tests;

use RonasIT\Support\Testing\TestCase as BaseTestCase;
use RonasIT\AutoDoc\Traits\AutoDocTestCaseTrait;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Arr;

abstract class TestCase extends BaseTestCase
{
    use AutoDocTestCaseTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->loadEnvironmentFrom('.env.testing');
    }

    public function assertQueueEqualsFixture(string $fixture, bool $exportMode = false): void
    {
        if (!str_contains($fixture, '.')) {
            $fixture .= '.json';
        }

        $actualData = [];

        foreach (Queue::pushedJobs() as $namespace => $jobs) {
            $actualData[$namespace] = Arr::map($jobs, fn ($job) => $this->getObjectAttributes($job['job']));
        }

        $this->assertEqualsFixture("queue_states/{$fixture}", $actualData, $exportMode);
    }

    public function assertQueueEmpty(): void
    {
        $actualJobs = Queue::pushedJobs();

        $this->assertEquals([], $actualJobs, 'Failed assert that faked queue is empty.');
    }

    protected function getObjectAttributes(object $object): array
    {
        return method_exists($object, 'toArray')
            ? $object->toArray()
            : get_object_vars($object);
    }
}
