<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionPublisher;
use Liberu\Cms\ExtensionMarketplace\Services\ExtensionMarketplaceService;

uses(RefreshDatabase::class);

it('manages a signed listing through licensing, trial, support, and distribution', function (): void {
    $service = app(ExtensionMarketplaceService::class);
    $publisher = $service->publisher(['key' => 'liberu', 'name' => 'Liberu', 'status' => 'approved']);
    $category = $service->category(['key' => 'content', 'name' => 'Content']);
    $listing = $service->listing(['key' => 'seo', 'name' => 'SEO', 'publisher_id' => $publisher->id, 'category_id' => $category->id]);
    $version = $service->version($listing, ['version' => '1.0.0', 'download_url' => 'https://example.test/seo.zip', 'checksum' => str_repeat('a', 64), 'status' => 'released']);
    $service->sign($version, 'signed-payload', 'key-1');
    $service->security($listing, 'approved');
    $service->distribute($version, ['url' => 'https://cdn.example.test/seo.zip', 'checksum' => str_repeat('a', 64)]);
    $service->publish($listing);
    $license = $service->license($listing, 'team', 7, 30);
    $trial = $service->trial($listing, 'team', 8, 14);
    $service->support($listing, ['channel' => 'email', 'url' => 'https://example.test/support', 'response_hours' => 24]);
    $service->review($listing, 'team', 7, 5, 'Useful');

    expect($listing->fresh()->status)->toBe('published')
        ->and($license->license_key)->not->toBeEmpty()
        ->and($trial->ends_at->isFuture())->toBeTrue()
        ->and($service->ratingSummary($listing->fresh()))->toBe(['average' => 5.0, 'count' => 1]);
});

it('rejects invalid releases and publication without a signed approved release', function (): void {
    $service = app(ExtensionMarketplaceService::class);
    $publisher = ExtensionPublisher::query()->create(['key' => 'p', 'name' => 'Publisher']);
    $listing = $service->listing(['key' => 'bad', 'name' => 'Bad', 'publisher_id' => $publisher->id]);

    expect(fn () => $service->version($listing, ['version' => '1', 'download_url' => 'https://example.test/a', 'checksum' => str_repeat('a', 64)]))->toThrow(ValidationException::class)
        ->and(fn () => $service->publish($listing))->toThrow(ValidationException::class)
        ->and(fn () => $service->trial($listing, 'team', 1, 0))->toThrow(ValidationException::class);
});
