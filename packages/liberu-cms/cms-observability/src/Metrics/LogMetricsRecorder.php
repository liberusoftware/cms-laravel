<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Metrics;

use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Psr\Log\LoggerInterface;

/**
 * The default recorder: writes one structured record per metric to an isolated
 * log channel, so the seam is exercised out of the box without polluting the app
 * log. Real aggregation is an operator concern — bind a Pulse / StatsD /
 * Prometheus recorder to {@see MetricsRecorderInterface} instead.
 */
final readonly class LogMetricsRecorder implements MetricsRecorderInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function increment(string $name, int $by = 1, array $tags = []): void
    {
        $this->record('counter', $name, $by, $tags);
    }

    public function timing(string $name, float $milliseconds, array $tags = []): void
    {
        $this->record('timing', $name, $milliseconds, $tags);
    }

    public function gauge(string $name, float $value, array $tags = []): void
    {
        $this->record('gauge', $name, $value, $tags);
    }

    /**
     * @param  array<string, scalar>  $tags
     */
    private function record(string $type, string $name, int|float $value, array $tags): void
    {
        $this->logger->info($name, [
            'type' => $type,
            'metric' => $name,
            'value' => $value,
            'tags' => $tags,
        ]);
    }
}
