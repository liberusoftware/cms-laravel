<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistant;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\MediaAssistant\Services\MediaAssistantService;

final class MediaAssistantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MediaAssistantService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
