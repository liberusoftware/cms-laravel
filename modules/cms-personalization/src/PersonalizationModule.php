<?php

declare(strict_types=1);

namespace Liberu\Cms\Personalization;

use Liberu\Cms\Core\Module\AbstractModule;

final class PersonalizationModule extends AbstractModule
{
    public function key(): string
    {
        return 'personalization';
    }

    public function name(): string
    {
        return 'Personalization';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
