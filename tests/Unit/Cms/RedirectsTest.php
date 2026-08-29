<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Redirects\Services\RedirectService;

uses(RefreshDatabase::class);

it('resolves redirect chains, counts hits, and detects loops', function (): void {
    $service = app(RedirectService::class);
    $service->create('/old', '/new');
    $service->create('/new', '/final');
    expect($service->resolve('/old')['path'])->toBe('/final');
    expect($service->resolve('/old')['redirect']?->hit_count)->toBeGreaterThanOrEqual(1);
    $service->create('/loop-a', '/loop-b');
    $service->create('/loop-b', '/loop-a');
    expect($service->resolve('/loop-a')['loop'])->toBeTrue();
});

it('imports redirects, records slug changes, and rejects self redirects', function (): void {
    $service = app(RedirectService::class);
    expect($service->import([['from_path' => '/one', 'to_path' => '/two'], ['from_path' => '/three', 'to_path' => '/four']], null))->toBe(2);
    $service->recordSlugChange('/old-slug', '/new-slug');
    expect($service->suggestions('/old-slug')[0]->from_path)->toBe('/old-slug');
    expect(fn () => $service->create('/same', '/same'))->toThrow(ValidationException::class)
        ->and(fn () => $service->resolve('/missing', 0))->toThrow(ValidationException::class)
        ->and(fn () => $service->create('', ''))->toThrow(ValidationException::class);
});

it('isolates resolution and suggestions by tenant', function (): void {
    $service = app(RedirectService::class);
    $service->create('/private', '/team-ten', teamId: 10);
    $service->create('/private', '/team-eleven', teamId: 11);

    expect($service->resolve('/private', teamId: 10)['path'])->toBe('/team-ten')
        ->and($service->resolve('/private', teamId: 11)['path'])->toBe('/team-eleven')
        ->and($service->suggestions('/private', teamId: 10)[0]->to_path)->toBe('/team-ten');
});
