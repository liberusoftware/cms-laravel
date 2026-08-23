# Performance, scaling & reliability

How to run Liberu CMS under production load. The golden rule of this repo —
*local stays zero-infra* — means **none of the flips below are committed
defaults**. Local development runs on SQLite/`database` drivers with no Redis,
queue worker, or search engine required; production posture lives here as
documented, opt-in configuration, mirroring the Phase-6 `SecurityHeaders` / CSP
approach. Work the [production checklist](PRODUCTION-CHECKLIST.md) alongside this
guide; it is the actionable step list, this is the *why*.

Observability is the companion to this guide: `modules/cms-observability`
answers "is the system healthy right now?" ([readiness](#readiness--liveness)) and
"what is it doing?" ([metrics](#metrics-in-production)). See
[ADR 0003](adr/0003-observability-as-seams.md).

## Redis for cache, session & queue

The single highest-impact production flip. Local defaults are `database` for all
three; in production point them at Redis:

```dotenv
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-strong-password
REDIS_PORT=6379
```

- **Cache** — a shared, fast store so config/route/view caches and any
  application caching (see [caching strategy](#caching-strategy-documented-not-built))
  are consistent across web nodes.
- **Session** — required the moment you run more than one web node behind a load
  balancer; `database`/`file` sessions do not share across hosts cleanly.
- **Queue** — a real broker so `SendNotification` (and any future job) runs out of
  band with retries and backoff rather than blocking the request.

## Queue supervision (Horizon)

With `QUEUE_CONNECTION=redis`, run [Laravel Horizon](https://laravel.com/docs/horizon)
for worker supervision, auto-balancing, and a metrics dashboard, instead of a bare
`php artisan queue:work`:

```bash
php artisan horizon
```

Supervise the `horizon` process itself (systemd / supervisord / a k8s
`Deployment`), and restart it on deploy (`php artisan horizon:terminate`).

**Failed-job retention.** Exhausted jobs land in the framework `failed_jobs`
table. `SendNotification` retries with backoff (`cms-notifications.queue.tries` /
`backoff`, default 3 tries at 10/30/60s) and, on final failure, marks its
`NotificationLog` row `failed` and emits a `notification.failed` metric. Prune the
table on a schedule so it does not grow unbounded:

```bash
php artisan queue:prune-failed --hours=168   # keep one week
```

## Octane (optional — not the default run mode)

[Octane](https://laravel.com/docs/octane) (FrankenPHP/Swoole/RoadRunner) keeps the
framework booted between requests for a large throughput win. It is **opt-in and
not endorsed as the default**, because a long-lived worker process changes the
statefulness assumptions Filament and Livewire are built on:

- Static properties, singletons holding request state, and container bindings
  resolved once at boot can **leak between requests** if any module stashes
  per-request state on a shared service.
- The admin panel (Filament) and its Livewire components must be validated under
  Octane before you rely on it; test thoroughly with `OCTANE_SERVER=frankenphp`
  and watch for cross-request/-tenant bleed.

Treat Octane as a deliberate, tested optimisation — not a flip-and-forget default.

## OPcache, preload & HTTP caching

- **OPcache** on in production (`opcache.enable=1`), with
  `opcache.validate_timestamps=0` so it never stats files on a hot path — remember
  to reset it on deploy (`opcache_reset()` / a fresh FPM pool).
- **Preload** the framework + hot classes via `opcache.preload` for a further cold
  reduction (revisit if using Octane, which already keeps classes resident).
- **Config/route/view/event caches** built every deploy (already in the checklist):
  `php artisan config:cache route:cache view:cache event:cache`.
- **HTTP caching / CDN** in front of published, unauthenticated content (the public
  site and the Delivery API's public reads) — a reverse proxy or CDN with sensible
  `Cache-Control` offloads the most read-heavy traffic. Keep authenticated `/app`
  and tenant-scoped API responses uncached.

## Caching strategy (documented, not built)

Application-level caching is **deliberately not implemented in this phase** — an
unproven cache with wrong invalidation is worse than none (stale published content,
or cross-tenant leaks). When you add it, the design is:

**What is worth caching** (read-heavy, changes rarely):

- Published-content reads (a page/post resolved by slug for the public site / API).
- Menu trees (`cms-menus`).
- The sitemap (`cms-seo`).

**How to invalidate** — key everything by tenant, and bust on the domain events the
CMS already emits rather than on TTL alone:

- `Liberu\Cms\Contracts\Events\Content\ContentPublished` and `…\ContentStateChanged`
  — flush the affected content's cache entries (and the menu/sitemap caches that
  include it) when it is published, unpublished, or archived.
- Scope every cache key by `team_id`; a key that forgets the tenant is a
  cross-tenant leak, the worst failure mode here.

Build it behind tests that prove both correctness (a state change is reflected on
the next read) **and** isolation (one tenant's cache never serves another's).

## Graceful degradation

The system is designed to keep serving when a *non-critical* dependency is down,
rather than 500. This is surfaced by the readiness probe's `degraded` status
(below): only the **database** is critical (503); cache, queue, storage, and search
degrade to 200.

- **Search down** — the `/api/v1/search` endpoint should return empty/last-known
  results rather than error; the `search` readiness check reports `degraded`.
- **Cache down** — fall through to the source of truth (slower, still correct); the
  `cache` check reports `degraded`.
- **Queue down** — notifications wait rather than failing the triggering request;
  the `queue` check reports `degraded`.

## Readiness & liveness

Wire both probes into the load balancer / k8s:

- **Liveness** — `GET /up` (framework). "Did the process boot?" Restart the pod on
  failure.
- **Readiness** — `GET /health/ready` (`cms-observability`). "Are dependencies
  reachable right now?" Unauthenticated, untenanted, per-IP throttled
  (`cms-observability.readiness.throttle`, default 60/min). Returns
  `{ status, checks: [{ name, status }] }` with **503** when the critical
  (database) check fails and **200** (`degraded`) for any non-critical failure — so
  a search/cache/queue/storage hiccup never pulls the node out of rotation. Point
  the k8s `readinessProbe` here so traffic drains only on a real outage.

Per-check criticality is config-overridable: `cms-observability.readiness.critical`
(database/cache/queue), `cms-media.readiness.critical`, `cms-search.readiness.critical`
— all default to degraded except the database.

## Metrics in production

Metrics record through the backend-agnostic `MetricsRecorderInterface`. **No
metrics backend is bundled.** Out of the box the default `LogMetricsRecorder` writes
one structured line per metric to an **isolated log channel**
(`cms-observability.metrics.channel`, default `cms-metrics`), enabled by
`cms-observability.metrics.enabled` (`CMS_METRICS_ENABLED`, default on) — so the
seam is exercised without polluting the app log, but nothing is aggregated.

For real aggregation, **bind your own recorder** (Laravel Pulse / StatsD /
Prometheus) in a host service provider:

```php
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;

$this->app->singleton(MetricsRecorderInterface::class, StatsdMetricsRecorder::class);
```

Metrics emitted today (stable dot-notation names):

| Metric | Type | Source |
|--------|------|--------|
| `content.published`, `content.state_changed`, `form.submitted`, `media.uploaded` | counter | domain events (zero-coupling listener) |
| `api.request` | counter + timing | `RecordApiMetrics` middleware, tagged `route` + `status`, `api/v1/*` only |
| `search.query`, `search.latency`, `search.results` | counter / timing / gauge | `SearchController` |
| `notification.failed` | counter | `SendNotification::failed()`, tagged `channel` |

Distributed tracing / OpenTelemetry is out of scope — a documented seam, not built.

## Search at scale (Meilisearch via Scout)

Local/default search is a portable database `LIKE` (`cms-search.driver=database`),
fully self-contained. For production-scale relevance, switch to the Scout driver
backed by [Meilisearch](https://www.meilisearch.com):

```dotenv
SEARCH_DRIVER=scout          # cms-search.driver
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://your-meilisearch:7700
MEILISEARCH_KEY=your-master-key
```

The query surface, `SearchResult` shape, and ranking are unchanged — only the
backend swaps, behind `SearchIndexInterface`. A source is queried through Scout
only when it implements `ScoutSearchableSourceInterface`; adopt it per content
module. The `search` readiness check reports the active driver's reachability
(the Meilisearch deep-ping is a documented deferral — bind your own if you need
it). Run an initial `php artisan scout:import` per searchable model.

## Reference — production config & env

| Concern | Env / config | Local default | Production |
|---------|--------------|---------------|-----------|
| Cache store | `CACHE_STORE` | `database` | `redis` |
| Session driver | `SESSION_DRIVER` | `database` | `redis` |
| Queue connection | `QUEUE_CONNECTION` | `database` | `redis` (+ Horizon) |
| Search driver | `SEARCH_DRIVER` (`cms-search.driver`) | `database` | `scout` (Meilisearch) |
| Metrics enabled | `CMS_METRICS_ENABLED` | `true` (log channel) | bind a real recorder |
| Metrics channel | `CMS_METRICS_CHANNEL` (`cms-observability.metrics.channel`) | `cms-metrics` | your channel/backend |
| Readiness throttle | `CMS_READINESS_THROTTLE` (`cms-observability.readiness.throttle`) | `60`/min | tune to probe rate |
| Notification retries | `NOTIFICATIONS_TRIES` (`cms-notifications.queue.tries`) | `3` | tune to channel SLAs |
| App server | `OCTANE_SERVER` | (none; PHP-FPM) | FrankenPHP *if tested* |
