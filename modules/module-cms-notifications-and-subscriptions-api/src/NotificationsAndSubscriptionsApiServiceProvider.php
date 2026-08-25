<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptionsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\NotificationsAndSubscriptionsApi\Http\SubscriptionController;

final class NotificationsAndSubscriptionsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('notifications-and-subscriptions-api', new ApiEndpoint('cms/notifications-and-subscriptions', SubscriptionController::class, 'index', 'cms.notifications-and-subscriptions.index'));
        }
    }
}
