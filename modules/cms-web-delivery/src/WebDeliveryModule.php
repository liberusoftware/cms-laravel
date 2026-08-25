<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery;

use Liberu\Cms\Core\Module\AbstractModule;

final class WebDeliveryModule extends AbstractModule
{
    public function key(): string { return 'web-delivery'; }
    public function name(): string { return 'Web Delivery'; }
    public function version(): string { return '0.1.0'; }
}
