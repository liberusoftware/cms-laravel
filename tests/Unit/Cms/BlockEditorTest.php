<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\BlockEditor\Services\BlockEditorService;

uses(RefreshDatabase::class);

it('saves revision-safe block documents, previews them, and enforces locking', function (): void {
    $service = app(BlockEditorService::class);
    $blocks = [['type' => 'heading', 'data' => ['text' => 'Hello']]];
    $document = $service->save(5, 'page', '42', $blocks);
    expect($document->version)->toBe(1)->and($document->preview_html)->toContain('Hello');
    $locked = $service->lock($document);
    expect(fn () => $service->save(5, 'page', '42', $blocks, 1))->toThrow(ValidationException::class)->and($locked->locked)->toBeTrue();
});

it('rejects unknown blocks, stale versions, and empty patterns', function (): void {
    $service = app(BlockEditorService::class);
    expect(fn () => $service->save(5, 'page', '42', [['type' => 'unknown']]))->toThrow(ValidationException::class)
        ->and(fn () => $service->createPattern(5, '', []))->toThrow(ValidationException::class);
});
