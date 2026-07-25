<?php

declare(strict_types=1);

namespace Liberu\Cms\Search;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Search. Adds a full-text search endpoint to the Delivery API, aggregating
 * results from every content module that registers a searchable source. It
 * consumes only contracts and the core module system.
 */
final class CmsSearchModule extends AbstractModule
{
    public function key(): string
    {
        return 'search';
    }

    public function name(): string
    {
        return 'Search';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
