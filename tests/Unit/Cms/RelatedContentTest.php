<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\RelatedContent\Services\RelatedContentService;

uses(RefreshDatabase::class);

it('ranks explainable relationships and respects exclusions and taxonomy', function (): void {
    $service = app(RelatedContentService::class);
    $service->relate('post', 1, 'post', 2, 'similarity', .9, ['topic' => 'cms']);
    $service->relate('post', 1, 'post', 3, 'recency', .8, ['topic' => 'other']);
    $service->exclude('post', 1, 'post', 3);

    expect($service->related('post', 1, 10, ['topic' => 'cms']))->toHaveCount(1)->and($service->related('post', 1)[0]['target_id'])->toBe(2);
});

it('rejects self relationships and unknown modes', function (): void {
    $service = app(RelatedContentService::class);
    expect(fn () => $service->relate('post', 1, 'post', 1))->toThrow(ValidationException::class)
        ->and(fn () => $service->relate('', 0, 'post', 2))->toThrow(ValidationException::class);
    expect(fn () => $service->relate('post', 1, 'post', 2, 'unknown'))->toThrow(ValidationException::class);
});

it('removes only the requested tenant relationship', function (): void {
    $service = app(RelatedContentService::class);
    $service->relate('post', 4, 'post', 5, teamId: 10);
    $service->relate('post', 4, 'post', 5, teamId: 11);

    expect($service->remove('post', 4, 'post', 5, 10))->toBe(1)
        ->and($service->related('post', 4, teamId: 11))->toHaveCount(1)
        ->and($service->related('post', 4, teamId: 10))->toBeEmpty();
});
