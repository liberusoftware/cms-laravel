<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentSearchFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentSearchFilament\Resources\SearchAnalyticResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentSearchFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-search-analytics', SearchAnalyticResource::class);
        }
    }
}
