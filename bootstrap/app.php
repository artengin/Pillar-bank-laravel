<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Schedule;
use RonasIT\AutoDoc\Http\Middleware\AutoDocMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/status',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->use([
            AutoDocMiddleware::class,
        ]);
        $middleware->throttleWithRedis();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function () {
        Schedule::command('telescope:prune --set-hours=cache:2,resolved_exception:24,log:168,schedule:25,unresolved_exception:168,completed_job:0.1 --hours=48')
            ->environments('development')
            ->everyFiveMinutes();
    })
    ->create();
