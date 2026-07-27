# Observability ships as removable seams with zero-infra defaults, not bundled backends

Phase 6.5 adds observability (readiness + metrics) and finishes the search backend as **contracts with working zero-infra defaults**, rather than adopting concrete infrastructure. Metrics default to a log channel (no Laravel Pulse is installed); readiness is a self-owned public probe backed by a module-contributed check registry; and Meilisearch enters as a Scout driver *behind* the existing `cms-search` contract (the DB `LIKE` driver stays the default). An operator plugs in a real backend — Pulse / StatsD / Prometheus for metrics, Meilisearch for search — by binding an implementation; nothing is bundled or required. This keeps every observability piece removable and provable on SQLite, matching the CMS's "everything is a removable package, no infra lock-in" rule.

## Considered Options

- **Bundle Laravel Pulse as the metrics/observability backend** — rejected: it drags in migrations, a dashboard, and a storage backend that cannot be verified on the dev box (no Docker/Redis locally), and it softens the observability module's removability. The metrics *seam* is the deliverable; the backend is the operator's choice.
- **Go Scout-native for search** (models become `Searchable`, Meilisearch owns matching *and* ranking, retire the hand-rolled `LIKE` + `SearchScoring`) — rejected: it discards tested behaviour and the custom ranking, and makes the *default* search path depend on an external engine. Instead a `SearchIndexInterface` sits below the existing sources with `DatabaseSearchIndex` (default) and `ScoutSearchIndex` (opt-in).
- **Adopt Redis / Octane / Horizon as committed defaults** — rejected for this phase: that is deployment posture, not features. They stay documented, flag-gated config (local remains `database`), mirroring the Phase-6 `SecurityHeaders` / CSP approach.

## Consequences

- Readiness returns **200 (degraded)** for every dependency failure except the database, which returns **503** — so a search / cache / queue hiccup never pulls a node out of the load balancer.
- Metrics are **on by default** but isolated to their own log channel, so the seam is exercised out of the box without polluting the app log; real aggregation requires an operator-bound recorder.
- Real Meilisearch behaviour is **deploy-verified only** — CI proves the `ScoutSearchIndex` wiring via Scout's in-memory collection engine, never a Meilisearch service. Same honesty bar as Redis / Octane.
- Distributed tracing / OpenTelemetry is explicitly **out of scope**, left as a documented seam.
