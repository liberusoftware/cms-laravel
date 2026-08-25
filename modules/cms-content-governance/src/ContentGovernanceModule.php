<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernance;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentGovernanceModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-governance';
    }

    public function name(): string
    {
        return 'Content Governance';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
