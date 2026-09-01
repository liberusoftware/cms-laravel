<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContent;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\EditorialContent\Services\EditorialContentService;

final class EditorialContentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EditorialContentService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
