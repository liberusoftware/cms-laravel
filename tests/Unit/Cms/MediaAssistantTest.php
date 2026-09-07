<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\MediaAssistant\Services\MediaAssistantService;

uses(RefreshDatabase::class);

it('stores provider suggestions with provenance and supports human review', function (): void {
    $service = app(MediaAssistantService::class);
    $suggestion = $service->suggest('media/photo.jpg', 'alt_text', 'A mountain landscape', 'vision-provider', 'model-1', 0.94, ['source' => 'upload'], 7);
    $accepted = $service->review($suggestion, 'accepted', 'editor-1', 'Verified', 7);
    $tag = $service->suggest('media/photo.jpg', 'tag', 'mountains', 'editorial-provider', null, null, [], 7);
    $service->review($tag, 'accepted', 'editor-1', null, 7);

    expect($accepted->status)->toBe('accepted')
        ->and($accepted->reviewer_key)->toBe('editor-1')
        ->and($service->acceptedTags('media/photo.jpg', 7))->toBe(['mountains']);
});

it('rejects invalid suggestions, confidence, decisions, and tenant access', function (): void {
    $service = app(MediaAssistantService::class);
    $suggestion = $service->suggest('media/photo.jpg', 'caption', 'A caption', 'provider', null, null, [], 7);

    expect(fn () => $service->suggest('../secret', 'tag', 'x', 'provider', null, null, [], 7))->toThrow(ValidationException::class);
    expect(fn () => $service->suggest('safe.jpg', 'tag', 'x', 'provider', null, 2.0, [], 7))->toThrow(ValidationException::class);
    expect(fn () => $service->review($suggestion, 'invalid', 'editor-1', null, 7))->toThrow(ValidationException::class);
    expect(fn () => $service->review($suggestion, 'accepted', 'editor-1', null, 8))->toThrow(ValidationException::class);
});
