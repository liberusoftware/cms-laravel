<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Multisite\Services\MultisiteService;

uses(RefreshDatabase::class);

it('governs site lifecycle, administrators, quotas, and references', function (): void {
    $service = app(MultisiteService::class);
    $source = $service->createSite('source', 'Source');
    $target = $service->createSite('target', 'Target');

    expect($service->assignAdmin($source, 42, 'admin')->role)->toBe('admin');
    $quota = $service->setQuota($source, ['content_items' => 10]);
    expect($service->recordUsage($quota, ['content_items' => 3])->usage['content_items'])->toBe(3);
    expect($service->reference($source, $target, 'page', 'page-1')->mode)->toBe('shared');
    expect($service->transition($source, 'suspended')->status)->toBe('suspended');
});

it('rejects unsafe references, invalid roles, and quota overages', function (): void {
    $service = app(MultisiteService::class);
    $site = $service->createSite('one', 'One');
    $other = $service->createSite('two', 'Two');
    $quota = $service->setQuota($site, ['content_items' => 1]);

    expect(fn () => $service->reference($site, $site, 'page', 'same'))->toThrow(ValidationException::class)
        ->and(fn () => $service->assignAdmin($site, 1, 'invalid'))->toThrow(ValidationException::class)
        ->and(fn () => $service->recordUsage($quota, ['content_items' => 2]))->toThrow(ValidationException::class)
        ->and($other->status)->toBe('active');
});

it('does not reactivate archived sites and can transition a network atomically', function (): void {
    $service = app(MultisiteService::class);
    $first = $service->createSite('first', 'First');
    $second = $service->createSite('second', 'Second');
    $service->transition($first, 'archived');

    expect(fn () => $service->transition($first->refresh(), 'active'))->toThrow(ValidationException::class)
        ->and($service->networkTransition([$first->id, $second->id], 'suspended'))->toBe(2);
});
