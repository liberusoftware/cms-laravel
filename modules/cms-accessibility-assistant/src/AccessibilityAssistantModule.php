<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistant;

use Liberu\Cms\Core\Module\AbstractModule;

final class AccessibilityAssistantModule extends AbstractModule
{
    public function key(): string
    {
        return 'accessibility-assistant';
    }

    public function name(): string
    {
        return 'Accessibility Assistant';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
