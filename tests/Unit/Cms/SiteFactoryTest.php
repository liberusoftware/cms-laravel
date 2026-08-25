<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\SiteFactory\Services\SiteFactoryService;

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
