<?php

declare(strict_types=1);

use Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface;
use Liberu\Cms\Contracts\Search\SearchIndexInterface;
use Liberu\Cms\Observability\Health\HealthCheckRegistry;
use Liberu\Cms\Search\Health\SearchHealthCheck;

/**
 * A search index whose readiness is fixed, to drive the health check both ways.
 */
function searchIndexReporting(bool $ready): SearchIndexInterface
{
    return new class($ready) implements SearchIndexInterface
    {
        public function __construct(private bool $ready) {}

        public function search(string $query): iterable
        {
            return [];
        }

        public function isReady(): bool
        {
            return $this->ready;
        }
    };
}

it('passes when the active driver reports ready', function (): void {
    $check = new SearchHealthCheck(searchIndexReporting(true), critical: false);

    expect($check->name())->toBe('search')
        ->and($check->isCritical())->toBeFalse()
        ->and($check->check())->toBeTrue();
});

it('fails when the active driver reports not ready', function (): void {
    $check = new SearchHealthCheck(searchIndexReporting(false), critical: false);

    expect($check->check())->toBeFalse();
});

it('contributes a degraded search check to the readiness probe', function (): void {
    $registry = new HealthCheckRegistry;
    $registry->register(new SearchHealthCheck(searchIndexReporting(false), critical: false));
    app()->instance(HealthCheckRegistryInterface::class, $registry);

    $this->getJson('/health/ready')
        ->assertOk()
        ->assertJson([
            'status' => 'degraded',
            'checks' => [['name' => 'search', 'status' => 'fail']],
        ]);
});

it('lists the search check on the real readiness probe', function (): void {
    $names = collect($this->getJson('/health/ready')->json('checks'))->pluck('name');

    expect($names)->toContain('search');
});
