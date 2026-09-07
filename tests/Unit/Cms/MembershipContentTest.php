<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\MembershipContent\Models\MembershipContent;
use Liberu\Cms\MembershipContent\Services\MembershipContentService;

uses(RefreshDatabase::class);

it('gates published content with entitlements and drip schedules', function (): void {
    $service = app(MembershipContentService::class);
    $content = $service->create([
        'title' => 'Members guide',
        'subject_type' => 'course',
        'subject_key' => 'guide',
        'status' => 'published',
    ], 7);

    $service->rule($content, 'pro');
    expect($service->canAccess($content, 'user', '42', 7))->toBeFalse();

    $service->grantEntitlement('user', '42', 'pro', 7, now()->subDay()->toDateTimeString());
    $service->drip($content, 'pro', 3);
    expect($service->canAccess($content, 'user', '42', 7))->toBeFalse();

    $service->grantEntitlement('user', '42', 'pro', 7, now()->subDays(4)->toDateTimeString());
    expect($service->canAccess($content->refresh(), 'user', '42', 7))->toBeTrue()
        ->and($service->canAccess($content, 'user', '42', 8))->toBeFalse();
});

it('supports content lifecycle data and rejects unsafe values', function (): void {
    $service = app(MembershipContentService::class);
    $content = $service->create([
        'title' => 'Downloadable guide',
        'subject_type' => 'course',
        'subject_key' => 'downloads',
    ]);

    $download = $service->download($content, [
        'path' => 'private/guides/guide.pdf',
        'filename' => 'guide.pdf',
        'mime_type' => 'application/pdf',
    ]);
    $portal = $service->portal('stripe', 'price_123', 7, ['plan' => 'pro']);

    expect($download->public_id)->not->toBeNull()
        ->and($portal->metadata)->toBe(['plan' => 'pro'])
        ->and(MembershipContent::query()->whereKey($content->id)->exists())->toBeTrue();

    expect(fn () => $service->download($content, ['path' => '../secret', 'filename' => 'x']))
        ->toThrow(ValidationException::class);
    expect(fn () => $service->grantEntitlement('user', '42', 'pro', 7, '2026-09-02', '2026-09-01'))
        ->toThrow(ValidationException::class);
});

it('revokes entitlements within the tenant boundary', function (): void {
    $service = app(MembershipContentService::class);
    $service->grantEntitlement('user', '42', 'pro', 7);
    $service->grantEntitlement('user', '42', 'pro', 8);

    expect($service->revokeEntitlement('user', '42', 'pro', 7))->toBeTrue()
        ->and($service->revokeEntitlement('user', '42', 'pro', 7))->toBeFalse();
});
