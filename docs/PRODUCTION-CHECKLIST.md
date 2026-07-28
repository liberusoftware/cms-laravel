# Production readiness checklist

Work through this before exposing Liberu CMS to the public internet. It closes
the common deployment foot-guns (leaked stack traces, insecure cookies, missing
headers) covered by OWASP A05/A02.

## Application

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` — **never** expose stack traces or config in production.
- [ ] `APP_KEY` is set (`php artisan key:generate`) and kept secret.
- [ ] `APP_URL` is the real HTTPS URL.
- [ ] Config/route/view/event caches built: `php artisan config:cache route:cache view:cache event:cache` (rebuild on every deploy).

## Transport & cookies

- [ ] TLS terminated at the edge; all HTTP redirected to HTTPS.
- [ ] `SESSION_SECURE_COOKIE=true` (cookies only sent over HTTPS).
- [ ] Force HTTPS URL generation behind a proxy — call `URL::forceScheme('https')`
      in a service provider, or set `TrustProxies`/`ASSET_URL` appropriately.
- [ ] HSTS confirmed on responses (`SECURITY_HSTS_ENABLED=true`, sent by the
      `SecurityHeaders` middleware). Consider submitting to the HSTS preload list
      once `SECURITY_HSTS_PRELOAD=true` is safe for the whole domain.

## Security headers (`config/security-headers.php`)

- [ ] `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`,
      `Referrer-Policy` present (default on via the middleware).
- [ ] Content-Security-Policy: ships **report-only** so it cannot break the
      Filament panel. Collect reports, tune `SECURITY_CSP_POLICY`, then set
      `SECURITY_CSP_REPORT_ONLY=false` to enforce.

## Data & workers

- [ ] Database migrated: `php artisan migrate --force`.
- [ ] Baseline roles/permissions seeded (`CmsBaselineRolesSeeder`, run via
      `CoreSetupSeeder`) so the admin can use the panel.
- [ ] Queue worker running (`php artisan queue:work`, or Horizon if enabled) —
      notifications and other queued jobs need it.
- [ ] `php artisan storage:link` created; `storage/` and `bootstrap/cache/`
      writable by the web user only.

## Dependencies

- [ ] `composer install --no-dev --optimize-autoloader`.
- [ ] `composer audit` clean (enforced in CI; `audit.block-insecure` is on).
- [ ] Front-end assets built: `npm ci && npm run build`.

## Observability & health probes

- [ ] **Liveness** — point the platform's liveness probe at `GET /up` (framework;
      "did the process boot?"), restarting the pod on failure.
- [ ] **Readiness** — point the load balancer / k8s `readinessProbe` at
      `GET /health/ready` (`cms-observability`). It returns **503** only when the
      critical database check fails and **200 `degraded`** for cache/queue/storage/
      search, so a non-critical hiccup never drains the node. Unauthenticated,
      untenanted, per-IP throttled (`CMS_READINESS_THROTTLE`, default 60/min).
- [ ] **Metrics** — the default `LogMetricsRecorder` writes to the `cms-metrics`
      log channel (`CMS_METRICS_CHANNEL`); ship that channel somewhere, or bind a
      real recorder (Pulse / StatsD / Prometheus) to `MetricsRecorderInterface`.
      See [PERFORMANCE.md](PERFORMANCE.md#metrics-in-production).

## Performance & scaling (see [PERFORMANCE.md](PERFORMANCE.md))

- [ ] **Redis flip** for `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION`
      (local stays `database`) — required once you run more than one web node.
- [ ] **Horizon** supervising the Redis queue (`php artisan horizon`), restarted
      on deploy; the bare `queue:work` above is the single-node fallback.
- [ ] **Failed-job retention**: schedule `php artisan queue:prune-failed --hours=168`.
- [ ] **Search**: leave `SEARCH_DRIVER=database` unless you need scale, then flip to
      `scout` with a reachable Meilisearch (`MEILISEARCH_HOST`/`MEILISEARCH_KEY`) and
      run `scout:import`.
- [ ] **OPcache** enabled with `validate_timestamps=0` (reset on deploy); config/
      route/view/event caches built (above).
- [ ] **Octane** only if validated — see the Filament/Livewire statefulness caveats
      in PERFORMANCE.md; it is **not** the default run mode.

## Verify

- [ ] A public page and the `/app` panel both render over HTTPS.
- [ ] Response headers include the security set above (curl `-I`).
- [ ] Hitting a bad route returns a generic error page, **not** a stack trace.
- [ ] `GET /health/ready` returns `200` with `status: ok` (or `degraded` with the
      failing check named), and `503` when the database is unreachable.
