<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ImageProcessing\Services\ImageProcessingService;

uses(RefreshDatabase::class);

it('validates images and creates tenant-safe responsive derivatives', function (): void {
    $service = app(ImageProcessingService::class);
    $service->validate('image/jpeg', 1024, 1600, 900);
    $profile = $service->profile('hero', 'avif', 80, 800, 450, 'cover', 7);
    $derivative = $service->derivative($profile, 'media/hero.jpg', 'abc123', ['focal_x' => 0.5], 7);

    expect($derivative->status)->toBe('ready')
        ->and($service->cdnUrl($derivative, 'https://cdn.example.test'))->toContain('/derivatives/hero/abc123.avif');
});

it('rejects unsupported, unsafe, and cross-tenant processing requests', function (): void {
    $service = app(ImageProcessingService::class);
    $profile = $service->profile('thumb', 'webp', 82, 320, 180, 'contain', 7);

    expect(fn () => $service->validate('image/svg+xml', 100))->toThrow(ValidationException::class);
    expect(fn () => $service->derivative($profile, '../secret', 'hash', [], 7))->toThrow(ValidationException::class);
    expect(fn () => $service->derivative($profile, 'safe.jpg', 'hash', [], 8))->toThrow(ValidationException::class);
    expect(fn () => $service->cdnUrl($service->derivative($profile, 'safe.jpg', 'hash', [], 7), 'not-a-url'))->toThrow(ValidationException::class);
});
