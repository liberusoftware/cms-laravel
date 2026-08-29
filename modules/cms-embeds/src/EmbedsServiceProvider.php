<?php

namespace Liberu\Cms\Embeds;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Embeds\Queries\EmbedsQuery;
use Liberu\Cms\Embeds\Services\EmbedsService;

class EmbedsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmbedsService::class);
        $this->app->singleton(EmbedsQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
