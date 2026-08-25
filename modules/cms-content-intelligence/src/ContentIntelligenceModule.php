<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligence;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentIntelligenceModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-intelligence';
    }

    public function name(): string
    {
        return 'Content Intelligence';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
