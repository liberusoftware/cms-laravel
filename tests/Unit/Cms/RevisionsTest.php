<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Revisions\Services\RevisionService;

uses(RefreshDatabase::class);
it('supports immutable-style snapshots, compare, restore, branching, and publishing', function (): void {
    $service = app(RevisionService::class);
    $one = $service->create('post', 1, ['title' => 'One'], 5);
    $two = $service->create('post', 1, ['title' => 'Two'], 6);
    expect($one->revision_number)->toBe(1)->and($service->compare($one, $two)['changes'][0]['to'])->toBe('Two');
    $restored = $service->restore($one, 7);
    expect($restored->snapshot())->toBe(['title' => 'One']);
    $branch = $service->branch($two, 'experiment', 8);
    expect($branch->branch)->toBe('experiment');
    expect($service->publish($two)->published)->toBeTrue();
});
it('deduplicates autosaves and validates branches', function (): void {
    $service = app(RevisionService::class);
    $first = $service->autosave('page', 1, ['body' => 'draft']);
    $same = $service->autosave('page', 1, ['body' => 'draft']);
    expect($same->getKey())->toBe($first->getKey());
    expect(fn () => $service->branch($first, ''))->toThrow(ValidationException::class)
        ->and(fn () => $service->branch($first, '!!!'))->toThrow(ValidationException::class)
        ->and(fn () => $service->create('', 0, []))->toThrow(ValidationException::class);
});
