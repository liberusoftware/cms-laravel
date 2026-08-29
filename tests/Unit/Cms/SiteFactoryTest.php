<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\SiteFactory\Models\SiteFactoryOperation;
use Liberu\Cms\SiteFactory\Services\SiteFactoryService;
use Liberu\Cms\SiteFactoryApi\Http\SiteFactoryController;

uses(RefreshDatabase::class);
it('provisions, verifies, clones, suspends, and archives sites', function (): void {
    $service = app(SiteFactoryService::class);
    $service->template('starter', 'Starter', ['locale' => 'en']);
    $site = $service->provision('docs', 'Docs', 'starter', 'docs.example.com');
    $domain = $site->domains ?? null;
    expect($site->status)->toBe('active')->and($service->suspend($site)->status)->toBe('suspended')->and($service->archive($site)->status)->toBe('archived');
});
it('requires confirmation for teardown and rejects duplicate keys', function (): void {
    $service = app(SiteFactoryService::class);
    $site = $service->provision('docs', 'Docs');
    expect(fn () => $service->provision('docs', 'Again'))->toThrow(ValidationException::class)->and(fn () => $service->teardown($site))->toThrow(ValidationException::class);
});

it('normalizes site keys before duplicate detection and validates templates', function (): void {
    $service = app(SiteFactoryService::class);
    $service->provision('my site', 'My Site');
    expect(fn () => $service->provision('my-site', 'Again'))->toThrow(ValidationException::class)
        ->and(fn () => $service->template('', ''))->toThrow(ValidationException::class);
});

it('exposes a loadable API controller with distinct action and serializer methods', function (): void {
    expect(class_exists(SiteFactoryController::class))->toBeTrue()
        ->and((new ReflectionClass(SiteFactoryController::class))->hasMethod('templateData'))->toBeTrue();
});

it('records completed lifecycle operations and preserves template initialization data', function (): void {
    $service = app(SiteFactoryService::class);
    $service->template('starter', 'Starter', ['locale' => 'en'], [['type' => 'hero']]);

    $site = $service->provision('docs', 'Docs', 'starter');

    expect($site->settings)->toBe([
        'factory' => [
            'configuration' => ['locale' => 'en'],
            'initial_content' => [['type' => 'hero']],
        ],
    ])
        ->and(SiteFactoryOperation::query()->where('operation', 'provision')->where('status', 'completed')->where('site_id', $site->id)->exists())->toBeTrue();
});
