<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\AccessibilityAssistant\Services\AccessibilityAssistantService;

uses(RefreshDatabase::class);

it('reports common authoring accessibility findings', function (): void {
    $findings = app(AccessibilityAssistantService::class)->analyze('<html><body><h2></h2><img src="hero.jpg"><a href="/next"></a><table><tr><td>Value</td></tr></table><video src="movie.mp4"></video></body></html>');
    $codes = array_column($findings, 'code');

    expect($codes)->toContain('image-alt', 'table-caption', 'empty-heading', 'link-name', 'video-captions', 'document-language');
});

it('supports explicit exceptions and rejects empty content', function (): void {
    $service = app(AccessibilityAssistantService::class);

    expect($service->analyze('<img src="hero.jpg">', ['image-alt']))->toBe([])
        ->and(fn () => $service->analyze(' '))->toThrow(ValidationException::class);
});
