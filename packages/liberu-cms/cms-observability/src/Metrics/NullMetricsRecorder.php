<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Metrics;

use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;

/**
 * The no-op recorder bound when `cms-observability.metrics.enabled` is false.
 * Callers still record freely against the contract; nothing is written.
 */
final class NullMetricsRecorder implements MetricsRecorderInterface
{
    public function increment(string $name, int $by = 1, array $tags = []): void {}

    public function timing(string $name, float $milliseconds, array $tags = []): void {}

    public function gauge(string $name, float $value, array $tags = []): void {}
}
