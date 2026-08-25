<?php

declare(strict_types=1);

namespace Liberu\Cms\Taxonomy;

use Liberu\Cms\Core\Module\AbstractModule;

final class TaxonomyModule extends AbstractModule
{
    public function key(): string
    {
        return 'taxonomy';
    }

    public function name(): string
    {
        return 'Taxonomy';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
