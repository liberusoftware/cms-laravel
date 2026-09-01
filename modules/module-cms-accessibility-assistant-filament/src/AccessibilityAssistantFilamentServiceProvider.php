<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AccessibilityAssistantFilament\Pages\AccessibilityAnalyzerPage;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class AccessibilityAssistantFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->afterResolving(AdminResourceRegistryInterface::class, static function (AdminResourceRegistryInterface $registry): void {
            $registry->registerPage('accessibility-assistant', AccessibilityAnalyzerPage::class);
        });
    }
}
