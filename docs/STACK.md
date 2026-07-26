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

## Quality tooling

| Tool | Version | Scope |
|------|---------|-------|
| Laravel Pint | `^1.24` | Phase 0 packages + CMS tests are clean; repo-wide debt tracked separately |
| PHPStan + Larastan | `^2.2` / `^3.10` | **max** level on `packages/liberu-cms/*/src` |
| Rector | `^2.4` | `app`, `database`, `packages/liberu-cms`, `tests` |
| Pest | `5.x-dev` | Unit, Feature, and per-package `Modules` suites |
| Infection | `^0.34` | Mutation testing on `cms-core` / `cms-contracts` (CI, non-blocking for now) |

## Deviations from the source material

1. **Pest 5, not Pest 4.** The repo already required `pestphp/pest:5.x-dev`; the
   foundation guidelines mention Pest 4. We build on what is installed (Pest 5).
2. **Module system: hand-rolled `packages/liberu-cms/*`.** The CMS uses hand-rolled
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
5. **PHPStan scoped to the CMS packages.** Running max level over the pre-existing
   `app/` would flood with findings unrelated to Phase 0; raising `app/` is future
   work (tracked).

## Dev environment (Docker / Sail)

`docker-compose.yml` boots app (Octane/RoadRunner), queue worker, MySQL, Redis,
Mailpit, and **Meilisearch** (search). PHP build arg corrected to 8.5. A
PostgreSQL profile is not yet included — see [OPEN-QUESTIONS](OPEN-QUESTIONS.md).
