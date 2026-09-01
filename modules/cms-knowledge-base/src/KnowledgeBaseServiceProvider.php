<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBase;

use Illuminate\Support\ServiceProvider;

final class KnowledgeBaseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
