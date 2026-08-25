<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederation;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentFederationModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-federation';
    }

    public function name(): string
    {
        return 'Content Federation';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
