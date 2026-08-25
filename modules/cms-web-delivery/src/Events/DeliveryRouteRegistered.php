<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Events;

use Liberu\Cms\WebDelivery\Models\DeliveryRoute;

final readonly class DeliveryRouteRegistered
{
    public function __construct(public DeliveryRoute $route) {}
}
