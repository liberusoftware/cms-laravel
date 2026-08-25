<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Support;

use Liberu\Cms\WebDelivery\Models\DeliveryInvalidation;

interface EdgeInvalidationRegistry
{
    public function invalidate(DeliveryInvalidation $invalidation): void;
}
