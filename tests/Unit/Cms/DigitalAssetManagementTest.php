<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DigitalAssetManagement\Services\DigitalAssetManagementService;

uses(RefreshDatabase::class);

it('registers, approves, versions with renditions, and distributes assets', function (): void {
    $service = app(DigitalAssetManagementService::class);
    $asset = $service->register(['name' => 'Brand mark', 'asset_type' => 'image', 'storage_key' => 'brand/logo.svg', 'license' => 'owned', 'brand_asset' => true], 3);
    $service->addRendition($asset, 'thumbnail', 'brand/logo-thumb.webp');
    $service->approve($asset);

    expect($service->distribute($asset, ['web', 'email'])->status)->toBe('distributed')
        ->and($asset->fresh()->renditions)->toBe(['thumbnail' => 'brand/logo-thumb.webp'])
        ->and($asset->fresh()->approved)->toBeTrue();
});

it('rejects invalid assets, renditions, and empty distribution', function (): void {
    $service = app(DigitalAssetManagementService::class);

    expect(fn () => $service->register(['name' => 'Expired', 'asset_type' => 'image', 'storage_key' => 'expired', 'expires_at' => '2020-01-01'], 3))
        ->toThrow(ValidationException::class);

    $asset = $service->register(['name' => 'Photo', 'asset_type' => 'image', 'storage_key' => 'photo.jpg'], 3);
    expect(fn () => $service->addRendition($asset, '', ''))->toThrow(ValidationException::class)
        ->and(fn () => $service->distribute($asset, []))->toThrow(ValidationException::class);
});
