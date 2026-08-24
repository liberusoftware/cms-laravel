<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\NotificationsAndSubscriptions\Services\SubscriptionService;

uses(RefreshDatabase::class);

it('follows subjects, updates preferences, unsubscribes, and queues instant delivery', function (): void {
    $service = app(SubscriptionService::class);
    $subscription = $service->follow('user', 4, 'post', 9, channels: ['mail', 'web']);
    expect($service->queueEvent('post.published', 'post', 9, ['title' => 'Hello']))->toBe(2)
        ->and($service->pending())->toHaveCount(2);
    $service->markSent($subscription->deliveries()->firstOrFail());
    expect($subscription->deliveries()->where('status', 'sent')->count())->toBe(1);
    $service->updatePreferences($subscription, 'weekly', ['push'], 'en');
    $service->unsubscribe($subscription);
    expect($subscription->fresh()->active)->toBeFalse()->and($subscription->fresh()->unsubscribed_at)->not->toBeNull();
});

it('rejects unsupported frequency and channels', function (): void {
    $service = app(SubscriptionService::class);
    expect(fn () => $service->follow('user', 1, 'author', 2, 'hourly'))->toThrow(ValidationException::class);
    expect(fn () => $service->follow('user', 1, 'author', 2, channels: ['carrier-pigeon']))->toThrow(ValidationException::class);
});
