<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilder;

use Liberu\Cms\Core\Module\AbstractModule;

final class ViewsAndQueryBuilderModule extends AbstractModule
{
    public function key(): string
    {
        return 'views-and-query-builder';
    }

    public function name(): string
    {
        return 'Views and Query Builder';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
