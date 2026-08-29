<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistant;

use Liberu\Cms\AccessibilityAssistant\Services\AccessibilityAssistantService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class AccessibilityAssistantServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new AccessibilityAssistantModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(AccessibilityAssistantService::class);
    }

    protected function bootModule(): void
    {
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('accessibility-assistant', 'Accessibility Assistant', AccessScope::Content, ['view', 'analyze', 'review']));
        }
    }
}
