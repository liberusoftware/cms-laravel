<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent;

use Liberu\Cms\Core\Module\AbstractModule;

final class MembershipContentModule extends AbstractModule
{
    public function key(): string
    {
        return 'membership-content';
    }

    public function name(): string
    {
        return 'Membership Content';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
