<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptions\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\NotificationsAndSubscriptions\Models\Delivery;
use Liberu\Cms\NotificationsAndSubscriptions\Models\Subscription;

final class SubscriptionService
{
    private const array FREQUENCIES = ['instant', 'daily', 'weekly'];

    private const array CHANNELS = ['mail', 'web', 'push', 'log'];

    public function follow(string $subscriberType, int|string $subscriberId, string $subjectType, int|string $subjectId, string $frequency = 'instant', array $channels = ['mail'], ?string $locale = null, ?int $teamId = null): Subscription
    {
        $this->validateOptions($frequency, $channels);

        return Subscription::query()->updateOrCreate(['subscriber_type' => $subscriberType, 'subscriber_id' => (string) $subscriberId, 'subject_type' => $subjectType, 'subject_id' => (string) $subjectId, 'team_id' => $teamId], ['frequency' => $frequency, 'channels' => array_values(array_unique($channels)), 'locale' => $locale, 'active' => true, 'unsubscribed_at' => null]);
    }

    public function updatePreferences(Subscription $subscription, string $frequency, array $channels, ?string $locale = null): Subscription
    {
        $this->validateOptions($frequency, $channels);
        $subscription->update(['frequency' => $frequency, 'channels' => array_values(array_unique($channels)), 'locale' => $locale, 'active' => true, 'unsubscribed_at' => null]);

        return $subscription->refresh();
    }

    public function unsubscribe(Subscription $subscription): Subscription
    {
        $subscription->update(['active' => false, 'unsubscribed_at' => now()]);

        return $subscription->refresh();
    }

    public function queueEvent(string $event, string $subjectType, int|string $subjectId, array $payload = [], ?int $teamId = null): int
    {
        $subscriptions = Subscription::query()->where('subject_type', $subjectType)->where('subject_id', (string) $subjectId)->where('active', true)->where('team_id', $teamId)->get();
        $created = 0;
        foreach ($subscriptions as $subscription) {
            if ($subscription->frequency !== 'instant') {
                continue;
            }
            foreach ($subscription->channels ?? [] as $channel) {
                Delivery::query()->firstOrCreate(['subscription_id' => $subscription->id, 'event' => $event, 'channel' => $channel], ['payload' => $payload, 'status' => 'pending', 'attempts' => 0, 'team_id' => $teamId]);
                $created++;
            }
        }

        return $created;
    }

    /** @return array<int, Delivery> */
    public function pending(?string $channel = null): array
    {
        return Delivery::query()->where('status', 'pending')->when($channel, fn ($q) => $q->where('channel', $channel))->with('subscription')->oldest()->get()->all();
    }

    public function markSent(Delivery $delivery): Delivery
    {
        $delivery->update(['status' => 'sent', 'sent_at' => now(), 'attempts' => $delivery->attempts + 1]);

        return $delivery->refresh();
    }

    public function markFailed(Delivery $delivery): Delivery
    {
        $delivery->update(['status' => 'failed', 'failed_at' => now(), 'attempts' => $delivery->attempts + 1]);

        return $delivery->refresh();
    }

    private function validateOptions(string $frequency, array $channels): void
    {
        if (! in_array($frequency, self::FREQUENCIES, true)) {
            throw ValidationException::withMessages(['frequency' => 'Unsupported subscription frequency.']);
        }
        if ($channels === [] || array_diff($channels, self::CHANNELS) !== []) {
            throw ValidationException::withMessages(['channels' => 'At least one supported delivery channel is required.']);
        }
    }
}
