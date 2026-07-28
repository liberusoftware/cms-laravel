# Phase 6.5 — Observability, Performance & Reliability (execution order)

Goal: make an already-hardened, already-extensible product **operable** — you can tell
whether it is healthy (readiness), see what it is doing (metrics), and run its search at
production scale (Meilisearch seam) — without breaking the golden rules (everything is a
removable Composer package; modules talk only via contracts + events; no feature code in
the host).

Governing decision: **observability ships as removable seams with zero-infra defaults,
not bundled backends** — no Pulse install, metrics default to a log channel, Meilisearch
enters as a Scout driver *behind* the existing search contract. See
[ADR 0003](../../docs/adr/0003-observability-as-seams.md). Full design:
[docs/specs/phase-6.5-observability-performance-reliability.md](../../docs/specs/phase-6.5-observability-performance-reliability.md).

Foundation-first order; each ticket is a stacked feature branch (user pushes/PRs
bottom-up). Base: **new branch off `main`** (`4391d9f`, all of Phases 0–7 merged).

| # | Ticket | Workstream | Blocked by |
|---|--------|-----------|-----------|
| 01 | Observability foundation (tracer) | Observability | — (first) |
| 02 | Cross-module contributions (media check + API middleware) | Observability | 01 |
| 03 | Search on the Meilisearch seam | Performance / Search | 01 |
| 04 | Reliability: queue hardening | Reliability | 01 |
| 05 | Performance & ops docs | Performance | 02–04 |

02 / 03 / 04 are mutually parallel once 01 lands; 05 is last so it documents the real
endpoint / channel / config names. The search health check lives in **03** (not 02)
because "is the index reachable" must ask the *active driver*, which only exists once the
seam is built.

**Status: PLANNED** — no code written yet.

## Deferred beyond this phase

Distributed tracing / OpenTelemetry; a bundled metrics dashboard (Pulse); built
application caching; Meilisearch CI integration; module boot-timing metrics.

## Conventions carried from Phase 6/7

- Windows/Herd: `php artisan` / `php` / composer via **PowerShell** (not the Bash tool —
  not on Bash PATH); `--ignore-platform-reqs` on composer ops when needed.
- No pgsql/mysql PDO or Docker locally → verify **full suite green on SQLite**; MySQL +
  Postgres legs run on push via the Phase-6 ticket-03 CI matrix. **No Meilisearch in CI**
  — the Scout driver is proven via Scout's in-memory collection engine.
- `php artisan test` OOMs (forked runner reads php.ini 128M) → run
  `php -d memory_limit=-1 vendor/bin/pest`. PHPStan local: `--memory-limit=1G`.
- Every ticket ends: Pint clean · PHPStan **max** clean (no new baseline) · arch-test
  green · `PublicApiTest` green (new contracts tagged `@api`) · full suite green · per
  ticket `/code-review`. No Claude attribution in commits.
