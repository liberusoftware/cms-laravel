# 02 — Cross-module contributions (media health check + API-metrics middleware)

**What to build:** Prove the two registries from ticket 01 are genuinely open to other
modules — a health check contributed by `cms-media`, and API request-latency metrics via
a middleware that `cms-observability` owns but `cms-api` never references.

**Blocked by:** 01. Parallel with 03 and 04.

**Status:** DONE

## Media storage health check (`cms-media`)

- [ ] `cms-media` registers a `HealthCheckInterface` (storage writable — a small
      put/delete probe on the media disk) into `HealthCheckRegistryInterface` in
      `registerModule`/`bootModule`, `bound()`-guarded. **Degraded**, not critical.
- [ ] `cms-media` imports only the contract; no import of `cms-observability`.
- [ ] Readiness now lists a `storage` check; storage-down → degraded 200.

## API-metrics middleware (`cms-observability`)

- [ ] `RecordApiMetrics` middleware records request **count + latency**
      (`api.request` timing + counter) tagged by route name + status.
- [ ] **Self-appends** via the router/container and **self-filters** to `api/v1/*`
      (or the framework `api` group) — `cms-api` is **not** modified and does not
      reference the middleware. `bound()`-guarded on the recorder.
- [ ] Never records for non-API paths (web, `/health/ready`, `/up`).

## Tests / DoD

- [ ] Media check: fake a failing disk → degraded 200 with the `storage` check `fail`;
      healthy → ok.
- [ ] Middleware: hit an `/api/v1` route → asserts `api.request` timing + counter with
      route/status tags (fake the recorder); a web route records nothing.
- [ ] Arch: no new cross-module imports; `cms-media` touches only contracts.
- [ ] Pint · PHPStan **max** (no new baseline) · `PublicApiTest` · full suite green on
      SQLite · `/code-review`.
