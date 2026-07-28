# 03 — Search on the Meilisearch seam

**What to build:** Finish the search backend `cms-search` was designed for. Introduce a
`SearchIndexInterface` driver seam *below* the existing sources so the `/api/v1/search`
surface, DTO, and ranking are untouched; the database driver stays the default, and
Meilisearch (via Scout) becomes an opt-in production driver. Contribute the search
readiness check (via the active driver) and search query metrics.

**Blocked by:** 01 (needs the health + metrics contracts). Parallel with 02 and 04.

**Status:** DONE

## Driver seam (`cms-search`)

- [ ] `SearchIndexInterface` — `search(...)` + `isReady(): bool`. Sits below
      `SearchableSourceInterface`; the registry/controller call the configured driver.
- [ ] `DatabaseSearchIndex` — wraps today's `LIKE` repositories, keeps `SearchScoring`
      (title 2.0 > body 1.0). **Default.** `isReady()` = DB reachable (trivially true).
- [ ] `ScoutSearchIndex` — Meilisearch through Scout. Models get the `Searchable` trait
      **only** when this driver is active. `isReady()` = index reachable.
- [ ] `config('cms-search.driver')` (`database` | `scout`), default `database`.
- [ ] **Unchanged:** `SearchRegistry`, `SearchController`, the `/api/v1/search` contract,
      `SearchResult` DTO, ranking.

## Dependency

- [ ] Add `laravel/scout` to `cms-search/composer.json` (approved); lock synced via
      `composer update liberu-cms/cms-search --ignore-platform-reqs`. Update `STACK.md`
      adopted-packages table.

## Readiness + metrics contributions

- [ ] `cms-search` registers a `search` health check that calls the **active driver's**
      `isReady()`. **Degraded**, not critical.
- [ ] `SearchController` records `search.query` count + `search.latency` timing +
      result count via `MetricsRecorderInterface`, `bound()`-guarded.

## Tests / DoD

- [ ] Database driver: existing search behaviour + ranking unchanged (regression).
- [ ] Scout driver **wiring** proven via Scout's in-memory **collection engine** (no
      Meilisearch): driver selected by config, models `Searchable`, query returns hits,
      `isReady()` reflects the engine. **No Meilisearch service in CI.**
- [ ] Search health check flips ok/degraded with a stubbed driver `isReady()`.
- [ ] Search metrics recorded (fake recorder).
- [ ] Arch clean; Pint · PHPStan **max** (no new baseline) · `PublicApiTest` · full suite
      green on SQLite · `/code-review`.
