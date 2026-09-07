<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperations;

use Illuminate\Support\ServiceProvider;

final class FormOperationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
