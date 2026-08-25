<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccessFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentAccessFilament\Resources\AccessRuleResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentAccessFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-access-rules', AccessRuleResource::class);
        }
    }
}
