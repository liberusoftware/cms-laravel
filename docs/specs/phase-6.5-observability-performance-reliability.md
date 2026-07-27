# Phase 6.5 — Observability, Performance & Reliability

The last deferred phase of the Liberu CMS build. It makes an already-hardened,
already-extensible product **operable**: you can tell whether it is healthy, see
what it is doing, and run its search at production scale — without violating the
project's golden rules (everything is a removable Composer package; modules talk
only via contracts + events; no feature code in the host).

Design decision of record: [ADR 0003 — observability as seams](../adr/0003-observability-as-seams.md).

## Governing thesis: split by nature

Performance / Observability / Reliability sit in tension with "everything is a
removable, infra-agnostic package", because they are inherently about concrete
deployment infrastructure (Redis, a real queue, a metrics backend). So the phase
splits along the precedent Phase 6 already set — *deployment-level cross-cutting
concerns live in the host, gated by config flags that default to the zero-infra
option* (`SecurityHeaders`, CSP report-only, `PRODUCTION-CHECKLIST`):

- **Observability** → a thin removable module (`cms-observability`) + contracts. The
  engineering **center of gravity** of the phase: real code, real tests, provable on
  SQLite.
- **Performance** → **config + docs** (Redis / Octane / Horizon / caching strategy).
  The one buildable item is the **search backend** (Meilisearch via a Scout driver).
- **Reliability** → mostly the readiness endpoint (counted under observability) plus
  one lean queue-hardening ticket; the rest is checklist.

What is **buildable-and-tested** vs **config-and-documented** is decided honestly by
what this box can verify: it has no Docker, Redis, Meilisearch, or pcov, so anything
that only a deployed stack can prove is documented, never fake-verified locally.

## Scope

**In scope**

- `cms-observability`: **readiness** endpoint + health-check registry, and a
  **metrics** recorder contract with a log-channel default.
- Cross-module health checks (search, media) and metric instrumentation (API latency,
  search queries, domain-event counters).
- Search on the **Meilisearch seam**: a `SearchIndexInterface` driver seam in
  `cms-search`, with `DatabaseSearchIndex` (default) and `ScoutSearchIndex` (opt-in);
  add `laravel/scout`.
- Reliability: harden the one queued job (`SendNotification`).
- `docs/PERFORMANCE.md` + `PRODUCTION-CHECKLIST` + `.env.example` production guidance.

**Out of scope (deferred, documented as seams)**

- **Distributed tracing / OpenTelemetry** — an Octane/deploy concern, unverifiable
  locally; left as a documented seam.
- **Bundling Laravel Pulse** (or any metrics backend) — operators bind their own.
- **Application-level caching code** — documented strategy only (invalidation via the
  existing `ContentPublished` / `ContentStateChanged` events); building it means owning
  cache-invalidation correctness under tenancy + workflow, which fails the SQLite bar.
- **Module boot-timing metrics** — infeasible cleanly: `ModuleManager` is
  `final readonly` and boots modules before the later-loading `cms-observability`
  binds its recorder (same ordering wall that limited `cms-audit`).
- Redis / Octane / Horizon as committed defaults; Meilisearch in CI.

## `cms-observability` (new removable package)

Packaging mirrors `cms-audit`: depends only on `cms-contracts` + `cms-core`
(+ framework). Its contracts live in `cms-contracts`, tagged `@api`. Other modules
contribute via those contracts under `bound()` guards and import **nothing** from
`cms-observability`; the module self-registers its route and middleware, so nobody
imports it either. Removable and headless-safe like every module.

### Readiness

`Liveness` ("did the process boot") already exists as Laravel's `GET /up` and is left
untouched. This phase adds **readiness** ("are dependencies reachable right now"):

- **Route:** `GET /health/ready`, owned by `cms-observability` (registered in
  `bootModule`, like `cms-seo`'s sitemap). **Unauthenticated, untenanted**, deliberately
  outside `/api/v1` (that group's Sanctum auth + tenant scope + throttle are wrong for a
  probe). A **light per-IP throttle** guards it (it touches DB/cache/queue), mirroring
  `throttle:cms-forms`.
- **Response:** JSON `{ status, checks: [{ name, status }] }` with **coarse** per-check
  statuses (ok/fail) and **no sensitive infra detail** (no hostnames, DSNs, or exception
  messages — it is public). Overall `status` is `ok` / `degraded` / `down`.
- **Registry:** `HealthCheckInterface` + `HealthCheckRegistryInterface` in `cms-contracts`.
  Each check declares `isCritical(): bool` (config-overridable).
- **Criticality — only the database is critical:**

  | Check | Owner | Criticality | HTTP on failure |
  |-------|-------|-------------|-----------------|
  | Database reachable | `cms-observability` | **critical** | **503** (`down`) |
  | Cache reachable | `cms-observability` | degraded | 200 (`degraded`) |
  | Queue reachable | `cms-observability` | degraded | 200 (`degraded`) |
  | Search index reachable | `cms-search` (via active driver `isReady()`) | degraded | 200 (`degraded`) |
  | Media storage writable | `cms-media` | degraded | 200 (`degraded`) |

  Rationale: the app genuinely cannot serve without its DB, but it *can* serve with
  search/queue/cache degraded — so those must never 503 and pull a node out of rotation.

### Metrics

A backend-agnostic recorder seam — the seam is the deliverable, the backend is the
operator's choice (same philosophy as the search driver):

- **Contract:** `MetricsRecorderInterface` (`cms-contracts`, `@api`), StatsD-shaped:
  `increment(string $name, int $by = 1, array $tags = [])`,
  `timing(string $name, float $milliseconds, array $tags = [])`,
  `gauge(string $name, float $value, array $tags = [])`.
- **Default binding:** `LogMetricsRecorder` → one structured JSON line per metric to a
  **dedicated, isolated log channel** (`config('cms-observability.metrics.channel')`),
  so it never pollutes the app log. `NullMetricsRecorder` when disabled.
- **Config:** `cms-observability.metrics.enabled` defaults **on** (isolated channel, so
  the seam is exercised out of the box at no cost) + the channel name.
- **No Pulse install.** Binding a Pulse / StatsD / Prometheus recorder is an operator
  step, documented in `docs/PERFORMANCE.md`.
- **Instrumentation — three layers, ascending coupling:**
  1. **Domain (zero coupling):** `cms-observability` listens on the EventBus and
     increments counters — `content.published`, `content.state_changed`,
     `form.submitted`, `media.uploaded`. Pure listener (like `cms-audit` /
     `cms-notifications`); no emitter changes.
  2. **HTTP/API (one touch):** a `RecordApiMetrics` middleware owned by
     `cms-observability` recording request **count + latency** tagged by route + status.
     It **self-appends** (via the router/container) and **self-filters** to `api/v1/*`,
     so `cms-api` never references it.
  3. **Search (one touch):** `cms-search`'s controller records query count + latency +
     result count via the recorder, `bound()`-guarded.

## Reliability

The CMS has exactly **one** queued job (`cms-notifications/SendNotification`), so the
buildable surface is small and no generic idempotency machinery is invented:

- **Harden `SendNotification`:** explicit `$tries` + `backoff`; a `failed()` handler
  that marks its `NotificationLog` row `failed` **and** emits a `notification.failed`
  metric (`bound()`-guarded — ties reliability into the metrics seam).
- **Idempotency guard** keyed on the `NotificationLog` id, so a retried job that already
  delivered will not re-send (the one place a duplicate is user-visible: a duplicate
  email). No broad `ShouldBeUnique`, no generic dedup layer.
- **`failed_jobs` table present** + documented retention.
- **Graceful degradation** (search/cache down → serve empty/last-known rather than 500)
  and **Horizon / worker supervision** → **checklist only**.

## Performance (config + docs; zero committed default changes)

Local defaults stay `database`; the production flip lives in docs (Phase-6
`SecurityHeaders`/CSP precedent). Deliverable = `docs/PERFORMANCE.md` (referenced from
`PRODUCTION-CHECKLIST.md`) covering:

- **Redis** for cache / queue / session as production defaults.
- **Octane** documented as *optional*, with the known Filament/Livewire statefulness
  caveats — **not** endorsed as the default run mode.
- **Horizon** for queue supervision.
- OPcache / preload / HTTP-cache guidance.
- **Caching strategy** — *what* to cache (published-content reads, menu trees, sitemap)
  and *how* to invalidate it via the existing events. Documented, **not built** (an
  unproven cache with wrong invalidation is worse than none: stale published content,
  cross-tenant leaks).
- Graceful-degradation guidance.

Plus `.env.example` production comments for the `SESSION_DRIVER` / `CACHE_STORE` /
`QUEUE_CONNECTION` / Redis flip and the `cms-observability` / `cms-search` toggles.

## Search — the Meilisearch seam

Finish the seam `cms-search` was designed for, **behind** the existing contract so
nothing above the driver changes:

- **New:** `SearchIndexInterface` sits below the existing `SearchableSourceInterface`
  sources. `DatabaseSearchIndex` wraps today's `LIKE` repos — stays the **default**,
  keeps `SearchScoring` (title 2.0 > body 1.0), fully SQLite-provable.
  `ScoutSearchIndex` (Meilisearch via Scout) is opt-in via `cms-search.driver`. Models
  get the `Searchable` trait only when the Scout driver is active.
- **Unchanged:** `SearchRegistry`, `SearchController`, the `/api/v1/search` contract,
  the `SearchResult` DTO, and the ranking.
- **New dependency:** `laravel/scout` (approved).
- **Search health check** (see readiness) asks the *active driver*'s `isReady()`.

## Tickets (execution order)

Base: **fresh branch off `main`** (`4391d9f`, all of Phases 0–7 merged). Stacked
feature branches, user pushes/PRs bottom-up.

| # | Ticket | Workstream | Blocked by |
|---|--------|-----------|-----------|
| 01 | Observability foundation (tracer) | Observability | — (first) |
| 02 | Cross-module contributions (media check + API middleware) | Observability | 01 |
| 03 | Search on the Meilisearch seam | Performance / Search | 01 |
| 04 | Reliability: queue hardening | Reliability | 01 |
| 05 | Performance & ops docs | Performance | 02–04 |

02 / 03 / 04 are mutually parallel once 01 lands; 05 is last so it documents the real
endpoint / channel / config names. The search health check lives in **03** (not 02)
because "is the index reachable" must ask the *active driver*, which only exists after
the seam is built.

## Definition of Done (per ticket, carried from Phase 6/7)

- Pint clean · PHPStan **max** clean with **no new baseline entries** · arch tests green
  (`cms-observability` removable + embeddable; no `App\` imports; no cross-module imports).
- Full Pest suite green on **SQLite** locally
  (`php -d memory_limit=-1 vendor/bin/pest`; PHPStan local `--memory-limit=1G`).
- CI matrix (sqlite + mysql + pgsql) green on push (Phase-6 ticket-03 matrix).
- `PublicApiTest` green — every new `cms-contracts` type tagged `@api` (or `@internal`)
  and added to `docs/EXTENSION-API.md`; `STACK.md` gains `laravel/scout` + the
  "Pulse not bundled" posture note.
- Per-ticket `/code-review` (Standards + Spec agents).
- **Scout verified via Scout's collection engine; no Meilisearch service in CI.**
- Infection stays non-blocking (calibration flip still pending the Phase-6 CI-log read).
- No Pulse / OTel anywhere. No Claude attribution in commits.

## Deferred beyond Phase 6.5

Distributed tracing / OpenTelemetry; a bundled metrics dashboard (Pulse); built
application caching; Meilisearch CI integration; module boot-timing metrics.
