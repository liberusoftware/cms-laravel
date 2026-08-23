<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Metrics;

/**
 * The backend-agnostic seam through which any part of the CMS records a counter,
 * timing, or gauge without knowing where metrics land. The default binding
 * writes one structured line per metric to an isolated log channel; an operator
 * may bind a Pulse / StatsD / Prometheus recorder instead. Metric names use
 * stable dot-notation (`content.published`, `api.request`), analogous to event
 * and filter names.
 *
 * @api This interface is part of the public extension API.
 */
interface MetricsRecorderInterface
{
    /**
     * Increment a counter metric.
     *
     * @param  array<string, scalar>  $tags
     */
    public function increment(string $name, int $by = 1, array $tags = []): void;

    /**
     * Record a timing (duration) metric in milliseconds.
     *
     * @param  array<string, scalar>  $tags
     */
    public function timing(string $name, float $milliseconds, array $tags = []): void;

    /**
     * Record a point-in-time gauge value.
     *
     * @param  array<string, scalar>  $tags
     */
    public function gauge(string $name, float $value, array $tags = []): void;
}
