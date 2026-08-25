<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptionsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\NotificationsAndSubscriptions\Models\Subscription;

final class SubscriptionController
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['subscriber_type' => ['sometimes', 'string'], 'subscriber_id' => ['sometimes', 'string'], 'active' => ['sometimes', 'boolean']]);
        $subscriptions = Subscription::query()->when($data['subscriber_type'] ?? null, fn ($q, string $value) => $q->where('subscriber_type', $value))->when($data['subscriber_id'] ?? null, fn ($q, string $value) => $q->where('subscriber_id', $value))->when(array_key_exists('active', $data), fn ($q) => $q->where('active', $data['active']))->latest()->get();

        return response()->json(['data' => $subscriptions->map(fn (Subscription $subscription): array => ['id' => $subscription->id, 'subject_type' => $subscription->subject_type, 'subject_id' => $subscription->subject_id, 'frequency' => $subscription->frequency, 'channels' => $subscription->channels, 'active' => $subscription->active, 'unsubscribed_at' => $subscription->unsubscribed_at?->toISOString()])]);
    }
}
