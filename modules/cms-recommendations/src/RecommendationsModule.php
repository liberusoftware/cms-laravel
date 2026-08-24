<?php

declare(strict_types=1);

namespace Liberu\Cms\Recommendations;

use Liberu\Cms\Core\Module\AbstractModule;

final class RecommendationsModule extends AbstractModule
{
    public function key(): string
    {
        return 'recommendations';
    }

    public function name(): string
    {
        return 'Recommendations';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
