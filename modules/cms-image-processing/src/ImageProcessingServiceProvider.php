<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessing;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ImageProcessing\Services\ImageProcessingService;

final class ImageProcessingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImageProcessingService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
