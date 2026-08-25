<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ConfigurationManagement\Models\ConfigurationRelease;
use Liberu\Cms\ConfigurationManagement\Services\ConfigurationService;

uses(RefreshDatabase::class);

it('exports versioned configuration without secrets and records a checksum', function (): void {
    $release = app(ConfigurationService::class)->export([
        'app_name' => 'CMS',
        'nested' => ['client_id' => 'public', 'api_token' => 'hidden'],
        'password_hint' => 'hidden',
    ], '2026.08.25', 'staging', 7, 3);

    expect($release)->toBeInstanceOf(ConfigurationRelease::class)
        ->and($release->status)->toBe('draft')
        ->and($release->payload)->toBe(['app_name' => 'CMS', 'nested' => ['client_id' => 'public']])
        ->and($release->checksum)->toHaveLength(64);
});

it('compares releases and validates dependencies before promotion', function (): void {
    $service = app(ConfigurationService::class);
    $first = $service->export(['title' => 'One'], 'one', 'production', 7, 3, ['core']);
    $second = $service->export(['title' => 'Two'], 'two', 'production', 7, 3, ['core', 'search']);

    expect($service->compare($first, $second)['changes'])->toHaveCount(1)
        ->and($service->validateDependencies($second, ['core'])['missing'])->toBe(['search']);

    expect(fn () => $service->promote($second, ['core']))->toThrow(ValidationException::class);
    $service->promote($first, ['core']);
    $service->promote($second, ['core', 'search']);

    expect($first->fresh()->status)->toBe('superseded')
        ->and($second->fresh()->status)->toBe('promoted');
});

it('rolls back only promoted releases', function (): void {
    $service = app(ConfigurationService::class);
    $draft = $service->export(['enabled' => true], 'draft', 'production');

    expect(fn () => $service->rollback($draft))->toThrow(ValidationException::class);

    $service->promote($draft);
    expect($service->rollback($draft)->status)->toBe('rolled_back');
});
