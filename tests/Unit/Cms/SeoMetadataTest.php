<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Seo\SeoMetadataService;

uses(RefreshDatabase::class);
it('stores metadata and reports content checks', function (): void {
    $service = app(SeoMetadataService::class);
    $service->save('page', 1, ['title' => 'Guide', 'description' => 'A guide', 'canonical_url' => 'https://example.com/guide', 'structured_data' => ['@type' => 'Article']]);
    $result = $service->check('page', 1);
    expect($result['score'])->toBe(100)->and($result['issues'])->toBeEmpty();
});
it('rejects relative canonical URLs', function (): void {
    expect(fn () => app(SeoMetadataService::class)->save('page', 1, ['canonical_url' => '/relative']))->toThrow(ValidationException::class);
});

it('rejects invalid SEO subjects at the domain boundary', function (): void {
    expect(fn () => app(SeoMetadataService::class)->save('', 0, []))->toThrow(ValidationException::class);
});

it('normalizes and validates robots directives', function (): void {
    $service = app(SeoMetadataService::class);
    $metadata = $service->save('page', 2, ['robots' => ' NOINDEX, FOLLOW ']);

    expect($metadata->robots)->toBe('noindex,follow')
        ->and(fn () => $service->save('page', 3, ['robots' => 'index,unsupported']))->toThrow(ValidationException::class)
        ->and(fn () => $service->save('page', 4, ['robots' => 'index,index']))->toThrow(ValidationException::class);
});
