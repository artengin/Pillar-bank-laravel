<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 5;

    public function backoff(): array
    {
        return [
            30,
            Carbon::SECONDS_PER_MINUTE * 1,
            Carbon::SECONDS_PER_MINUTE * 3,
            Carbon::SECONDS_PER_MINUTE * 5,
        ];
    }
}
