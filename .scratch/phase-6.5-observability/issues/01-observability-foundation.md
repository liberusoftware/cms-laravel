# 01 — Observability foundation (readiness + metrics, tracer)

**What to build:** The new removable `cms-observability` package and the two contracts
it introduces, wired end-to-end so both halves self-prove before cross-module
contributions land in ticket 02. Readiness (`GET /health/ready`) backed by the three
infra checks; a metrics recorder writing to an isolated log channel, exercised by the
zero-coupling domain-event counters. Write ADR 0003 (done) and the phase spec (done)
here; keep them in sync if design shifts.

**Blocked by:** None — first ticket of Phase 6.5. Base: **new branch off `main`**
(`4391d9f`).

**Status:** DONE

## Contracts (in `cms-contracts`, tagged `@api`, added to `docs/EXTENSION-API.md`)

- [ ] `Health\HealthCheckInterface` — `name(): string`, `isCritical(): bool`,
      `check(): bool` (or a small result value object). Coarse ok/fail only.
- [ ] `Health\HealthCheckRegistryInterface` — `register(HealthCheckInterface)` +
      `all(): iterable`. Mirrors the sitemap / api / search registries.
- [ ] `Metrics\MetricsRecorderInterface` — `increment(string, int $by = 1, array $tags = [])`,
      `timing(string, float $ms, array $tags = [])`, `gauge(string, float, array $tags = [])`.
- [ ] `PublicApiTest` stays green (every new contract `@api` or `@internal`).

## Package (`modules/cms-observability`)

- [ ] New package `Liberu\Cms\Observability`, deps = `cms-contracts` + `cms-core` only;
      added to root composer require + `phpstan.neon`; installed via
      `composer update liberu-cms/cms-observability --ignore-platform-reqs`.
- [ ] Provider binds `HealthCheckRegistry` (singleton) and
      `MetricsRecorderInterface → LogMetricsRecorder` (with `NullMetricsRecorder` when
      `cms-observability.metrics.enabled` is false).
- [ ] `config/cms-observability.php`: `metrics.enabled` (default **true**),
      `metrics.channel` (isolated), `readiness.throttle`, per-check `critical` overrides.
- [ ] A dedicated log channel merged into `config/logging.php` (or documented) so metrics
      never pollute the app log.

## Readiness

- [ ] Route `GET /health/ready` registered in `bootModule` — **unauthenticated,
      untenanted**, outside `/api/v1`, light **per-IP throttle**.
- [ ] Response `{ status: ok|degraded|down, checks: [{ name, status }] }`; coarse
      statuses, **no infra detail**. HTTP **503** iff a *critical* check fails, else 200.
- [ ] Three infra checks owned here: **Database** (`isCritical() = true`), **Cache**,
      **Queue** (both degraded). Criticality config-overridable.

## Metrics — domain-event counters (zero coupling)

- [ ] An EventBus listener increments `content.published`, `content.state_changed`,
      `form.submitted`, `media.uploaded`. Pure listener (like `cms-audit`), imports no
      emitter. `bound()`-guarded on the recorder.

## Tests / DoD

- [ ] Readiness failure-injection: stub each check failing → **DB-down = 503**,
      cache/queue-down = **degraded 200**; all-pass = ok 200. Throttle + no-infra-detail
      assertions.
- [ ] Metrics: fake the log channel, assert one structured record per call; assert the
      four domain counters fire on their events; disabled → `NullMetricsRecorder` no-ops.
- [ ] Arch: `cms-observability` removable + embeddable; no `App\` / cross-module imports.
- [ ] Pint · PHPStan **max** (no new baseline) · `PublicApiTest` · full suite green on
      SQLite · `/code-review` (Standards + Spec).
