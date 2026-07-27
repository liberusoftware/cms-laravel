# cms-observability

The operability layer for Liberu CMS (Phase 6.5). Answers *"is the system healthy
right now?"* (readiness) and *"what is it doing?"* (metrics) as removable seams
with zero-infra defaults. Owns no domain data; feature modules contribute checks
and the system records metrics through contracts in `cms-contracts`.

See [ADR 0003 — observability as seams](../../../docs/adr/0003-observability-as-seams.md).

## Readiness — `GET /health/ready`

Public, unauthenticated, untenanted, deliberately outside `/api/v1`, with a light
per-IP throttle. Returns coarse per-check statuses and **no infrastructure detail**:

```json
{ "status": "ok", "checks": [ { "name": "database", "status": "ok" } ] }
```

Overall `status` is `ok` / `degraded` / `down`. Each check declares its
criticality (config-overridable under `cms-observability.readiness.critical`):

| Check | Criticality | HTTP on failure |
|-------|-------------|-----------------|
| `database` | **critical** | **503** (`down`) |
| `cache` | degraded | 200 (`degraded`) |
| `queue` | degraded | 200 (`degraded`) |

Only the database is critical by default: the app cannot serve without it, but it
*can* serve with cache / queue degraded — so those must never pull a node out of
rotation. Other modules add their own checks (search, media) in later tickets via
`HealthCheckRegistryInterface`.

## Metrics

A backend-agnostic recorder seam (`MetricsRecorderInterface`, StatsD-shaped:
`increment` / `timing` / `gauge`). The default `LogMetricsRecorder` writes one
structured record per metric to an **isolated log channel**
(`cms-observability.metrics.channel`, default `cms-metrics`), so the seam is
exercised out of the box without polluting the app log. `NullMetricsRecorder` is
bound when `cms-observability.metrics.enabled` is false.

**No metrics backend is bundled.** Binding a Pulse / StatsD / Prometheus recorder
is an operator step (documented in `docs/PERFORMANCE.md`).

Domain counters are recorded by a pure EventBus listener (zero coupling, like the
audit and notifications subscribers): `content.published`,
`content.state_changed`, `form.submitted`, `media.uploaded`.

## Design

- **Removable & headless-safe.** Depends only on `cms-contracts` + `cms-core`;
  imports no feature module, and no module imports it — it self-registers its
  route, throttle, and listeners. Removing it just stops the probe and metrics.
- **Zero-infra defaults.** Readiness runs on SQLite; metrics land in a log
  channel. Real backends (Redis, Meilisearch, Pulse) are the operator's choice.
