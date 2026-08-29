<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AccessibilityAssistantFilament\Resources\AccessibilityResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class AccessibilityAssistantFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('accessibility-assistant', AccessibilityResource::class);
        }
    }
}
