<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\StaticPublishing\Contracts\DeploymentAdapterInterface;
use Liberu\Cms\StaticPublishing\Models\StaticBuild;
use Liberu\Cms\StaticPublishing\Services\StaticPublishingService;
use Liberu\Cms\StaticPublishing\Support\DeploymentAdapterRegistry;

uses(RefreshDatabase::class);
it('builds full, incremental, and preview manifests with diagnostics', function (): void {
    $service = app(StaticPublishingService::class);
    $full = $service->build([['path' => '/'], ['path' => '/about'], ['path' => 'invalid']], 'docs');
    $incremental = $service->build([['path' => '/about']], 'docs', 'incremental', 'cdn', $full);
    expect($full->state)->toBe('published')->and($service->diagnostics($full)['route_count'])->toBe(2)->and($service->rollback($incremental)->state)->toBe('rolled_back');
});
it('validates build kinds and invalidation paths', function (): void {
    $service = app(StaticPublishingService::class);
    expect(fn () => $service->build([], null, 'unknown'))->toThrow(ValidationException::class);
    $build = $service->build([], 'docs');
    expect(fn () => $service->invalidate($build, 'relative'))->toThrow(ValidationException::class);
});

it('records malformed routes as diagnostics instead of publishing them', function (): void {
    $build = app(StaticPublishingService::class)->build([['path' => '/valid'], ['path' => 'relative'], ['url' => 'missing-path']], 'docs');

    expect($build->manifest)->toHaveCount(1)->and($build->diagnostics['invalid_routes'])->toBe(2);
});

it('deploys published builds through a provider-neutral adapter registry', function (): void {
    app(DeploymentAdapterRegistry::class)->register(new class implements DeploymentAdapterInterface
    {
        public function key(): string
        {
            return 'test-cdn';
        }

        public function deploy(StaticBuild $build): array
        {
            return ['url' => 'https://cdn.example.test/'.$build->getKey()];
        }
    });
    $service = app(StaticPublishingService::class);
    $build = $service->build([['path' => '/']], 'docs');

    expect($service->deploy($build, 'test-cdn'))->toBe(['url' => 'https://cdn.example.test/'.$build->getKey()])
        ->and($build->fresh()->deployment)->toBe('test-cdn');
});
