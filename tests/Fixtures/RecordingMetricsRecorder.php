<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;

/**
 * A metrics recorder test double that captures every call, so a test can assert
 * what was recorded (and with which tags) without a real backend. Shared across
 * the observability tests in place of hand-rolled anonymous spies.
 */
final class RecordingMetricsRecorder implements MetricsRecorderInterface
{
    /**
     * @var array<int, array{method: string, name: string, value: int|float, tags: array<string, scalar>}>
     */
    public array $calls = [];

    public function increment(string $name, int $by = 1, array $tags = []): void
    {
        $this->calls[] = ['method' => 'increment', 'name' => $name, 'value' => $by, 'tags' => $tags];
    }

    public function timing(string $name, float $milliseconds, array $tags = []): void
    {
        $this->calls[] = ['method' => 'timing', 'name' => $name, 'value' => $milliseconds, 'tags' => $tags];
    }

    public function gauge(string $name, float $value, array $tags = []): void
    {
        $this->calls[] = ['method' => 'gauge', 'name' => $name, 'value' => $value, 'tags' => $tags];
    }

    /**
     * The metric name recorded by every call, in order.
     *
     * @return array<int, string>
     */
    public function names(): array
    {
        return array_column($this->calls, 'name');
    }

    /**
     * The recorder methods invoked, in order (e.g. `increment`, `timing`).
     *
     * @return array<int, string>
     */
    public function methods(): array
    {
        return array_column($this->calls, 'method');
    }
}
