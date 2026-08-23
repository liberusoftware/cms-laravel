# Stack

The pinned technology stack for Liberu CMS, recorded per Part A §3. Versions are
constrained in `composer.json`; actual resolved versions come from `composer.lock`.

## Core runtime

| Component | Target (Part B) | Constraint | Resolved / notes |
|-----------|-----------------|------------|------------------|
| PHP | 8.5 | `^8.5` | 8.5.8 (local Herd) |
| Laravel | 13 | `^13.0` | 13.13.0 |
| Filament | 5.x | `^5.0` | 5.x |
| Livewire | 4 | `^4.0` | 4.x |
| Database | PostgreSQL **and** MySQL | — | Portable via the schema/query builder; no vendor SQL |

## Adopted ecosystem packages (Part A §3)

| Package | Purpose | Boundary rule |
|---------|---------|---------------|
| `bezhansalleh/filament-shield` `^4.0` | RBAC / permissions | Phase 1: kept behind the Users module's permission contract |
| `biostate/filament-menu-builder` `^5.0` | Navigation admin | Phase 3: kept behind the Menu module's contract |
| `spatie/laravel-permission` `^7.0` | Permission storage | See open question on Shield vs. Spatie reconciliation |
| `laravel/scout` `^11.0` | Search index driver | Phase 6.5: required by `cms-search`, behind `SearchIndexInterface`. Opt-in via `cms-search.driver=scout` (Meilisearch); the DB `LIKE` driver stays the default. CI proves the Scout wiring on the collection engine — no Meilisearch service. |

## Quality tooling

| Tool | Version | Scope |
|------|---------|-------|
| Laravel Pint | `^1.24` | Phase 0 packages + CMS tests are clean; repo-wide debt tracked separately |
| PHPStan + Larastan | `^2.2` / `^3.10` | **max** level on `modules/*/src` |
| Rector | `^2.4` | `app`, `database`, `modules`, `tests` |
| Pest | `5.x-dev` | Unit, Feature, and per-package `Modules` suites |
| Infection | `^0.34` | Mutation testing on `cms-core` / `cms-contracts` (CI, non-blocking for now) |

## Deviations from the source material

1. **Pest 5, not Pest 4.** The repo already required `pestphp/pest:5.x-dev`; the
   foundation guidelines mention Pest 4. We build on what is installed (Pest 5).
2. **Module system: hand-rolled `modules/*`.** The CMS uses hand-rolled
   path-repository packages (namespace `Liberu\Cms\*`) per an explicit project
   decision, matching Part A §4's literal layout. `internachi/modular` was removed
   in Phase 6 (it was never used) so the repo now has exactly one module system.
   See [OPEN-QUESTIONS](OPEN-QUESTIONS.md).
3. **Laravel 13 confirmed.** The source material mentions both "Laravel 13"
   (target) and "Laravel 12 foundation" in one place. Laravel 13 is viable and
   installed, so 13 is the resolved target.
4. **`config.audit.block-insecure: true` (Phase 6).** Re-enabled once the tree was
   clean: removing the unused `internachi/modular` dropped its `composer/composer`
   subtree (the source of the transitive advisories), and phpseclib was bumped to
   3.0.55 (CVE-2026-55599). `composer audit` now runs as a **blocking** job in the
   security workflow, and `block-insecure: true` enforces the same gate on every
   local `composer update`.
5. **PHPStan covers the whole repo (Phase 6).** Max level now analyses `app/` and
   `database/` alongside the CMS packages; the 173 pre-existing host findings are
   frozen in `phpstan-baseline.neon` and burned down over time. The CMS packages
   remain clean at max with no baseline entries.
6. **`content_entries.data` is `longText`, not `json` (Phase 6, ticket 03).** The
   Delivery-API search runs a portable `LIKE` over the raw payload, and PostgreSQL
   rejects `LIKE` against a `json`/`jsonb` column. The model's `array` cast still
   (de)serialises it transparently, so nothing above the schema changed. Other JSON
   columns (forms `fields`, submissions `data`/`meta`, content-types `fields`) stay
   `json` — they are only ever array-cast, never queried with SQL.
7. **No metrics backend bundled (Phase 6.5).** Observability ships the `cms-observability`
   module as *seams with zero-infra defaults*: readiness (`GET /health/ready`) over a
   module-contributed check registry, and a `MetricsRecorderInterface` whose default
   binding writes to an isolated log channel. **Laravel Pulse is deliberately not
   installed** — it drags in migrations, a dashboard, and a storage backend that cannot
   be verified on the dev box, and softens the module's removability. An operator binds
   their own recorder (Pulse / StatsD / Prometheus). Distributed tracing / OpenTelemetry
   is out of scope, left as a documented seam. See
   [ADR 0003](adr/0003-observability-as-seams.md).

## Dev environment (Docker / Sail)

`docker-compose.yml` boots app (Octane/RoadRunner), queue worker, MySQL, Redis,
Mailpit, and **Meilisearch** (search). PHP build arg corrected to 8.5. A
**PostgreSQL** service sits behind the `postgres` compose profile (`docker compose
--profile postgres up`); MySQL remains the default dev DB. CI proves portability by
running the full Pest suite against sqlite, mysql, and pgsql — see
[OPEN-QUESTIONS #8](OPEN-QUESTIONS.md).
