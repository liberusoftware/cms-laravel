<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\HeadlessApi\Data\DeliveryQuery;
use Liberu\Cms\HeadlessApi\Services\PersistedQueryService;

uses(RefreshDatabase::class);

it('validates delivery query capabilities and persists tenant-scoped queries', function (): void {
    $query = DeliveryQuery::from(['version' => 'v2', 'fields' => ['title', 'slug'], 'include' => ['author'], 'filter' => ['status' => 'published'], 'page' => 2, 'per_page' => 50, 'locale' => 'en-US', 'preview' => true]);
    $stored = app(PersistedQueryService::class)->store('{ posts { title } }', 7);

    expect($query->version)->toBe('v2')->and($query->fields)->toBe(['title', 'slug'])->and(app(PersistedQueryService::class)->resolve(hash('sha256', '{ posts { title } }'), 7)?->id)->toBe($stored->id);
});

it('rejects unsafe query options and cross-tenant persisted query lookup', function (): void {
    expect(fn () => DeliveryQuery::from(['per_page' => 101]))->toThrow(ValidationException::class);
    $service = app(PersistedQueryService::class);
    $stored = $service->store('query', 7);
    expect($service->resolve(hash('sha256', 'query'), 8))->toBeNull()
        ->and(fn () => $service->resolve('invalid', 7))->toThrow(ValidationException::class);
});
