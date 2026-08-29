<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Publishing\Models\PublicationRelease;
use Liberu\Cms\Publishing\Services\PublishingService;

uses(RefreshDatabase::class);

it('publishes a due release and records cache/index evidence', function (): void {
    $release = PublicationRelease::create(['key' => 'release-1', 'targets' => [['type' => 'page', 'id' => 1]], 'cache_tags' => ['page:1'], 'publish_at' => now()->subMinute()]);
    app(PublishingService::class)->processDue();

    expect($release->fresh()->state)->toBe('published');
    $this->assertDatabaseHas('cms_publication_release_events', ['release_id' => $release->id, 'event' => 'published']);
});

it('enforces embargo and expiry invariants', function (): void {
    $service = app(PublishingService::class);
    $release = PublicationRelease::create(['key' => 'invalid', 'publish_at' => now(), 'embargo_until' => now()->addHour()]);
    expect(fn () => $service->schedule($release))->toThrow(ValidationException::class);
    $release->update(['embargo_until' => null, 'publish_at' => now()->subHour(), 'expires_at' => now()->subMinute()]);
    $service->schedule($release);
    $service->processDue();
    expect($release->fresh()->state)->toBe('archived');
});

it('supports explicit unpublish and archive transitions', function (): void {
    $release = PublicationRelease::create(['key' => 'manual', 'state' => 'published']);
    $service = app(PublishingService::class);
    $service->unpublish($release);
    $service->archive($release->fresh());
    expect($release->fresh()->state)->toBe('archived');
});

it('creates releases through the domain boundary and rejects missing keys', function (): void {
    $service = app(PublishingService::class);
    expect($service->create(['key' => 'new-release'])->state)->toBe('scheduled')
        ->and(fn () => $service->create([]))->toThrow(ValidationException::class);
});

it('rejects invalid publication state transitions', function (): void {
    $service = app(PublishingService::class);
    $archived = PublicationRelease::create(['key' => 'archived', 'state' => 'archived']);
    $draft = PublicationRelease::create(['key' => 'draft', 'state' => 'draft']);

    expect(fn () => $service->publish($archived))->toThrow(ValidationException::class)
        ->and(fn () => $service->unpublish($draft))->toThrow(ValidationException::class);
});
