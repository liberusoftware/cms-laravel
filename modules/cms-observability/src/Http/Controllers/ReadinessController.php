<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface;

/**
 * Serves the public readiness probe. It runs every registered health check and
 * reports a coarse per-check ok/fail plus an overall status, deliberately
 * leaking no infrastructure detail. A failing *critical* check returns 503
 * (`down`) so the load balancer drops the node; any other failure reports
 * `degraded` at 200 so a search / cache / queue hiccup never pulls it out of
 * rotation.
 */
final readonly class ReadinessController
{
    public function __construct(private HealthCheckRegistryInterface $registry) {}

    public function __invoke(): JsonResponse
    {
        $checks = [];
        $criticalFailed = false;
        $anyFailed = false;

        foreach ($this->registry->all() as $check) {
            $ok = $check->check();

            $checks[] = ['name' => $check->name(), 'status' => $ok ? 'ok' : 'fail'];

            if (! $ok) {
                $anyFailed = true;
                $criticalFailed = $criticalFailed || $check->isCritical();
            }
        }

        $status = match (true) {
            $criticalFailed => 'down',
            $anyFailed => 'degraded',
            default => 'ok',
        };

        return response()->json(
            ['status' => $status, 'checks' => $checks],
            $criticalFailed ? 503 : 200,
        );
    }
}
