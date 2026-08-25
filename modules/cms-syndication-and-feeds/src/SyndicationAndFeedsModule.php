<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeeds;

use Liberu\Cms\Core\Module\AbstractModule;

final class SyndicationAndFeedsModule extends AbstractModule
{
    public function key(): string
    {
        return 'syndication-and-feeds';
    }

    public function name(): string
    {
        return 'Syndication and Feeds';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
