<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptionsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\NotificationsAndSubscriptionsFilament\Resources\SubscriptionResource;

final class ListSubscriptions extends ListRecords
{
    #[\Override]
    protected static string $resource = SubscriptionResource::class;
}
