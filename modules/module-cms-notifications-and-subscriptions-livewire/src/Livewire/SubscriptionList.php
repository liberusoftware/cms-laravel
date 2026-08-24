<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptionsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\NotificationsAndSubscriptions\Models\Subscription;
use Livewire\Component;

final class SubscriptionList extends Component
{
    public string $subscriberType = '';

    public string $subscriberId = '';

    public function render(): View
    {
        $subscriptions = Subscription::query()->when($this->subscriberType !== '', fn ($q) => $q->where('subscriber_type', $this->subscriberType))->when($this->subscriberId !== '', fn ($q) => $q->where('subscriber_id', $this->subscriberId))->latest()->get();

        return view('cms-notifications-and-subscriptions-livewire::subscription-list', ['subscriptions' => $subscriptions]);
    }
}
