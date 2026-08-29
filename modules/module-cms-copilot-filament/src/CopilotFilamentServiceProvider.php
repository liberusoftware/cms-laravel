<?php

declare(strict_types=1);

namespace Liberu\Cms\CopilotFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\CopilotFilament\Resources\CopilotRequestResource;

final class CopilotFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('cms-copilot', CopilotRequestResource::class);
        }
    }
}
