<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligenceFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentIntelligenceFilament\Resources\ContentInsightResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentIntelligenceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-intelligence', ContentInsightResource::class);
        }
    }
}
