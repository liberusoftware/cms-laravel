# 05 — Performance & ops docs

**What to build:** The documentation half of Performance & Reliability. No committed
default changes and no cache code — local stays `database`; the production posture lives
in docs (Phase-6 `SecurityHeaders`/CSP precedent). Sequenced last so it documents the
**real** endpoint / channel / config names created by tickets 01–04.

**Blocked by:** 02, 03, 04 (documents their surface).

**Status:** PLANNED

## `docs/PERFORMANCE.md` (new; referenced from `PRODUCTION-CHECKLIST.md`)

- [ ] **Redis** for `CACHE_STORE` / `SESSION_DRIVER` / `QUEUE_CONNECTION` as production
      defaults (local stays `database`).
- [ ] **Octane** documented as *optional*, with the Filament/Livewire statefulness
      caveats — **not** the default run mode.
- [ ] **Horizon** for queue supervision + worker process management.
- [ ] OPcache / preload / HTTP-cache guidance.
- [ ] **Caching strategy — documented, not built:** *what* to cache (published-content
      reads, menu trees, sitemap) and *how* to invalidate via the existing
      `ContentPublished` / `ContentStateChanged` events. Call out the risk (wrong
      invalidation → stale published content / cross-tenant leaks).
- [ ] **Graceful degradation** guidance (search/cache down → serve empty/last-known,
      surfaced by the readiness `degraded` status).
- [ ] How to bind a real **metrics recorder** (Pulse / StatsD / Prometheus) against
      `MetricsRecorderInterface`, and where the default metrics channel writes.
- [ ] How to enable the **Scout/Meilisearch** search driver (`cms-search.driver=scout`).

## `PRODUCTION-CHECKLIST.md` additions

- [ ] Readiness probe wiring (`GET /health/ready`) for the load balancer / k8s;
      liveness stays `GET /up`.
- [ ] Redis / Octane / Horizon flip; failed-jobs retention; metrics channel target.

## `.env.example`

- [ ] Production-guidance comments for the driver flip + `cms-observability` /
      `cms-search` toggles (local defaults untouched — the flip is in the checklist).

## DoD

- [ ] Docs only — no code, no committed default changes. Links resolve; config/env names
      match what tickets 01–04 actually shipped. Pint clean (no PHP changed) ·
      `/code-review` (doc accuracy pass).
