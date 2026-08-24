<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptionsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\NotificationsAndSubscriptionsLivewire\Livewire\SubscriptionList;
use Livewire\Livewire;

final class NotificationsAndSubscriptionsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-notifications-and-subscriptions.subscription-list', SubscriptionList::class);
    }
}
