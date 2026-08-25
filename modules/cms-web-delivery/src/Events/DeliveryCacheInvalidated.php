<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Events;

use Liberu\Cms\WebDelivery\Models\DeliveryInvalidation;

final readonly class DeliveryCacheInvalidated
{
    public function __construct(public DeliveryInvalidation $invalidation) {}
}
