<?php

declare(strict_types=1);

use Liberu\Cms\Contracts\Health\HealthCheckInterface;
use Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface;
use Liberu\Cms\Observability\Health\HealthCheckRegistry;

/**
 * A stub check with a fixed name, criticality, and result, so readiness can be
 * driven through every failure combination without touching real infrastructure.
 */
function fakeCheck(string $name, bool $critical, bool $result): HealthCheckInterface
{
    return new readonly class($name, $critical, $result) implements HealthCheckInterface
    {
        public function __construct(
            private string $name,
            private bool $critical,
            private bool $result,
        ) {}

        public function name(): string
        {
            return $this->name;
        }

        public function isCritical(): bool
        {
            return $this->critical;
        }

        public function check(): bool
        {
            return $this->result;
        }
    };
}

/**
 * Replace the registry the readiness controller reads with one holding exactly
 * the given checks.
 */
function withChecks(HealthCheckInterface ...$checks): void
{
    $registry = new HealthCheckRegistry;

    foreach ($checks as $check) {
        $registry->register($check);
    }

    app()->instance(HealthCheckRegistryInterface::class, $registry);
}

it('reports ok with 200 when every check passes', function (): void {
    withChecks(
        fakeCheck('database', critical: true, result: true),
        fakeCheck('cache', critical: false, result: true),
    );

    $this->getJson('/health/ready')
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
            'checks' => [
                ['name' => 'database', 'status' => 'ok'],
                ['name' => 'cache', 'status' => 'ok'],
            ],
        ]);
});

it('returns 503 down when a critical check fails', function (): void {
    withChecks(
        fakeCheck('database', critical: true, result: false),
        fakeCheck('cache', critical: false, result: true),
    );

    $this->getJson('/health/ready')
        ->assertServiceUnavailable()
        ->assertJson([
            'status' => 'down',
            'checks' => [
                ['name' => 'database', 'status' => 'fail'],
                ['name' => 'cache', 'status' => 'ok'],
            ],
        ]);
});

it('returns 200 degraded when only a non-critical check fails', function (): void {
    withChecks(
        fakeCheck('database', critical: true, result: true),
        fakeCheck('cache', critical: false, result: false),
        fakeCheck('queue', critical: false, result: false),
    );

    $this->getJson('/health/ready')
        ->assertOk()
        ->assertJson([
            'status' => 'degraded',
            'checks' => [
                ['name' => 'database', 'status' => 'ok'],
                ['name' => 'cache', 'status' => 'fail'],
                ['name' => 'queue', 'status' => 'fail'],
            ],
        ]);
});

it('leaks no infrastructure detail beyond a coarse name and status', function (): void {
    withChecks(fakeCheck('database', critical: true, result: false));

    $payload = $this->getJson('/health/ready')->json();

    expect(array_keys($payload))->toEqualCanonicalizing(['status', 'checks'])
        ->and($payload['checks'][0])->toEqual(['name' => 'database', 'status' => 'fail']);
});

it('serves the probe unauthenticated and untenanted with the real infra checks', function (): void {
    $this->getJson('/health/ready')
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonStructure(['status', 'checks' => [['name', 'status']]]);
});

it('throttles the probe per IP', function (): void {
    config(['cms-observability.readiness.throttle' => 1]);

    $this->getJson('/health/ready')->assertOk();
    $this->getJson('/health/ready')->assertTooManyRequests();
});
