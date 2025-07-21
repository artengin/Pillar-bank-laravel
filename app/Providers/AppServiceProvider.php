<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;
use RonasIT\Support\EntityGeneratorServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'local') {
            App::register(EntityGeneratorServiceProvider::class);
        }
    }
}
