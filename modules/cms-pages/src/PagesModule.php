<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Pages. Declares a dependency on the Media module so it cannot be enabled
 * without media, and media cannot be disabled while Pages is enabled.
 */
final class PagesModule extends AbstractModule
{
    public function key(): string
    {
        return 'pages';
    }

    public function name(): string
    {
        return 'Pages';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    #[\Override]
    public function dependencies(): array
    {
        return ['media'];
    }
}
