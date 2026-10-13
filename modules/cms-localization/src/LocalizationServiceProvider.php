<?php

declare(strict_types=1);

namespace Liberu\Cms\Localization;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Localization\Services\LocalizationService;

final class LocalizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LocalizationService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
