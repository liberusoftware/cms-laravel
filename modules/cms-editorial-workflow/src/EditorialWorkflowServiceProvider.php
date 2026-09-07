<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\EditorialWorkflow\Services\EditorialWorkflowService;

final class EditorialWorkflowServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EditorialWorkflowService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
