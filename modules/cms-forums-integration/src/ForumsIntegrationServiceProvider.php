<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegration;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ForumsIntegration\Services\ForumProviderRegistry;

final class ForumsIntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ForumProviderRegistry::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
