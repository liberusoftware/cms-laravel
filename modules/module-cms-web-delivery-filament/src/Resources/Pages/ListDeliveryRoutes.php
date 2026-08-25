<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\WebDeliveryFilament\Resources\DeliveryRouteResource;

final class ListDeliveryRoutes extends ListRecords
{
    protected static string $resource = DeliveryRouteResource::class;
}
