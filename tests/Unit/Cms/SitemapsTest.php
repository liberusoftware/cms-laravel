<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Sitemaps\Services\SitemapService;

uses(RefreshDatabase::class);
it('stores filtered site/locale entries, extensions, chunks, and notifications', function (): void {
    $service = app(SitemapService::class);
    $service->add('https://example.com/a', 1, 'news', 'en', .9, ['news' => ['publication' => 'Today']]);
    $service->add('https://example.com/b', 1, 'web', 'en');
    $service->exclude('https://example.com/b', 1);
    expect($service->entries(1, 'news', 'en'))->toHaveCount(1)->and($service->chunks(1, 1))->toHaveCount(1)->and($service->notify('google', 1)['queued'])->toBeTrue();
});
it('validates sitemap URLs, priorities, and engines', function (): void {
    $service = app(SitemapService::class);
    expect(fn () => $service->add('/relative'))->toThrow(ValidationException::class)->and(fn () => $service->add('https://example.com', null, 'web', null, 2))->toThrow(ValidationException::class)->and(fn () => $service->notify('duckduckgo'))->toThrow(ValidationException::class);
});

it('removes excluded entries from subsequent cached reads', function (): void {
    $service = app(SitemapService::class);
    $service->add('https://example.com/cached', 7);
    expect($service->entries(7))->toHaveCount(1);
    $service->exclude('https://example.com/cached', 7);

    expect($service->entries(7))->toHaveCount(0);
});

it('updates and removes entries through the domain boundary', function (): void {
    $service = app(SitemapService::class);
    $entry = $service->add('https://example.com/lifecycle', 8);

    expect($service->update($entry, ['url' => 'https://example.com/updated', 'priority' => .8])->url)->toBe('https://example.com/updated');
    $service->remove($entry->fresh());

    expect($entry->fresh())->toBeNull();
});
