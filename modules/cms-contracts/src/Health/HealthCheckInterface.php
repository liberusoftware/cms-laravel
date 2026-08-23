<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Health;

/**
 * A single named dependency probe contributed to the readiness registry by the
 * module that owns that dependency. It reports a coarse reachable/unreachable
 * result and declares whether its failure should pull the instance out of
 * rotation. Implementations must never leak infrastructure detail (hostnames,
 * DSNs, exception messages) — the readiness endpoint is public.
 *
 * @api This interface is part of the public extension API.
 */
interface HealthCheckInterface
{
    /**
     * A stable dot-notation name for the check, e.g. `database`, `cache`.
     */
    public function name(): string;

    /**
     * Whether a failure of this check makes the instance unable to serve
     * traffic (`true` → 503 `down`) rather than merely degraded (`false` → 200).
     */
    public function isCritical(): bool;

    /**
     * Probe the dependency. Returns true when it is reachable right now, false
     * otherwise. Implementations swallow their own errors and never throw.
     */
    public function check(): bool;
}
